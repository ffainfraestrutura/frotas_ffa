<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function responder(
    bool $success,
    string $message,
    array $data = [],
    int $httpCode = 200
): never {
    http_response_code($httpCode);

    echo json_encode(
        array_merge([
            'success' => $success,
            'message' => $message,
        ], $data),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(false, 'Método não permitido.', [], 405);
}

$dataReferencia = $_GET['data_referencia'] ?? '';

$date = DateTimeImmutable::createFromFormat('!Y-m-d', $dataReferencia);

if (
    !$date ||
    $date->format('Y-m-d') !== $dataReferencia
) {
    responder(false, 'Data de referência inválida.', [], 400);
}

try {
    $pdo = Database::getConnection();

    $inicio = $dataReferencia . ' 00:00:00';

    $fim = $date->modify('+1 day')->format('Y-m-d') . ' 00:00:00';

    /*
     * Busca os saldos da data selecionada.
     *
     * Funcionário:
     * Relacionamento pela matrícula.
     *
     * Veículo:
     * Prioriza veículo ativo e visível.
     * Em caso de empate, seleciona o maior ID.
     *
     * Quilometragem:
     * Calcula a média diária dos últimos 21 dias,
     * considerando usuários com perfil 0.
     *
     * KM/L:
     * Busca o consumo previsto conforme o tipo de veículo.
     */

    $sql = "   
    WITH
    tecnicos AS (
        SELECT
            tv.placa,
            tv.matcond AS matricula,
            tv.unidade AS uf,
            'Tecnico' AS classificacao,
            ROUND(ts.sldprjtd * 0.80, 2) AS valor,
            saldo,
            saldo_real_calculado,
            valorcomb,
            sldprjtd,
            3 AS prioridade
        FROM
            tbsaldo ts
            INNER JOIN tbveiculo tv
                ON tv.matcond = ts.matricula
        WHERE
            ts.data = :data_inicio_tecnico
    ),
    supervisores AS (
        SELECT
            tv.placa,
            tv.matcond AS matricula,
            tv.unidade AS uf,
            'Supervisor' AS classificacao,
            150.00 AS valor,
            150.00 AS saldo,
            150.00 AS saldo_real_calculado,
            0 AS valorcomb,
            150.00 AS sldprjtd,
            2 AS prioridade
        FROM
            tbsupervisor ts
            INNER JOIN tbveiculo tv
                ON tv.matcond = ts.matricula
    ),
    cota_fixa AS (
        SELECT
            COALESCE(NULLIF(TRIM(tc.placa), ''), tv.placa) AS placa,
            tc.matricula,
            tv.unidade AS uf,
            'Cota Fixa' AS classificacao,
            tc.valor,
            tc.valor AS saldo,
            tc.valor AS saldo_real_calculado,
            0 AS valorcomb,
            tc.valor AS sldprjtd,
            1 AS prioridade
        FROM
            bdcombustivel.tbcotafixa tc
            LEFT JOIN tbveiculo tv
                ON tc.matricula = tv.matcond
                    OR tc.placa = tv.placa
    ),
    base AS (
        SELECT *
        FROM
            cota_fixa
        UNION ALL
        SELECT *
        FROM
            supervisores
        UNION ALL
        SELECT *
        FROM
            tecnicos
    ),
    classificados AS (
        SELECT
            placa,
            matricula,
            uf,
            classificacao,
            valor,
            saldo,
            saldo_real_calculado,
            valorcomb,
            sldprjtd,
            ROW_NUMBER () OVER (PARTITION BY placa ORDER BY
                prioridade) AS rn
        FROM
            base
        WHERE
            placa IS NOT NULL
            AND TRIM(placa) <> ''
    )
SELECT
    c.placa,
    c.matricula,
    c.uf,
    c.classificacao,
    c.saldo,
    c.saldo_real_calculado,
    c.valorcomb,
    c.sldprjtd,
    COALESCE(km.media_km_diario, 0) AS media_km_diario,
    (
        SELECT tvp.km_litro_previsto
        FROM
            bdcombustivel.tbtipoveiculo tvp
        WHERE
            UPPER(TRIM(tvp.descricao)) = UPPER(TRIM(atv.tipo))
        ORDER BY
            tvp.idtbtipoveiculo DESC
        LIMIT 1
    ) AS km_litro,
    s.status AS status_insercao
FROM
    classificados c
    LEFT JOIN (
        SELECT tkd.matricula, AVG(tkd.kmsrodados) AS media_km_diario
        FROM
            bdfrota.tbkmrodadosdia tkd
        WHERE
            tkd.dia > DATE_SUB(NOW(), INTERVAL 21 DAY)
            AND EXISTS (
                    SELECT 1
                    FROM
                        bdfrota.tbusuario u
                    WHERE
                        u.matricula = tkd.matricula
                        AND u.perfil = 0
                )
        GROUP BY
            tkd.matricula
    ) km
        ON km.matricula = c.matricula
    LEFT JOIN bdfrota.tbveiculo v
        ON v.placa = c.placa
    LEFT JOIN bdfrota.tbatipovel atv
        ON atv.idtbatipovel = v.tipovel
    LEFT JOIN bdfrota.tb_execucao_insercao_item s
        ON s.matricula = c.matricula
            AND s.placa = c.placa
WHERE
    c.rn = 1
    AND c.uf IN ('RJ', 'ES')
GROUP BY
    placa,
    matricula
ORDER BY
    c.uf ASC,
    c.matricula ASC,
    s.execucao_id DESC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':data_inicio_tecnico' => $inicio,
    ]);

    $saldos = $stmt->fetchAll();

    /*
     * Verifica duplicidades pela combinação matrícula + data.
     *
     * A tabela já possui UNIQUE (matricula, data).
     * Esta consulta permanece como verificação adicional.
     */

    $sqlDuplicados = "
        SELECT
            matricula,
            COUNT(*) AS quantidade

        FROM bdfrota.tbsaldo

        WHERE data >= :data_inicio
          AND data < :data_fim

        GROUP BY matricula

        HAVING COUNT(*) > 1
    ";

    $stmtDuplicados = $pdo->prepare($sqlDuplicados);

    $stmtDuplicados->execute([
        ':data_inicio' => $inicio,
        ':data_fim' => $fim,
    ]);

    $duplicados = $stmtDuplicados->fetchAll();

    /*
     * Calcula os totais para o dashboard.
     */

    $totalSaldo = 0;
    $totalSaldoReal = 0;

    $totalSucesso = 0;
    $totalErro = 0;
    $totalPendente = 0;
    $totalProcessando = 0;

    foreach ($saldos as $saldo) {
        $totalSaldo += (float) ($saldo['saldo'] ?? 0);

        $totalSaldoReal += (float) (
            $saldo['saldo_real_calculado'] ?? 0
        );

        switch ($saldo['status_insercao']) {
            case 'SUCESSO':
                $totalSucesso++;
                break;

            case 'ERRO':
                $totalErro++;
                break;

            case 'PROCESSANDO':
                $totalProcessando++;
                break;

            default:
                $totalPendente++;
                break;
        }
    }

    responder(
        true,
        'Saldos carregados com sucesso.',
        [
            'data_referencia' => $dataReferencia,

            'total' => count($saldos),

            'total_saldo' => $totalSaldo,
            'total_saldo_real' => $totalSaldoReal,

            'total_sucesso' => $totalSucesso,
            'total_erros' => $totalErro,
            'total_pendente' => $totalPendente,
            'total_processando' => $totalProcessando,

            'duplicados' => $duplicados,
            'possui_duplicidades' => count($duplicados) > 0,

            'saldos' => $saldos,
        ]
    );
} catch (Throwable $e) {
    error_log('[SALDO][LISTAR] ' . $e->getMessage());

    responder(
        false,
        'Não foi possível carregar os saldos.',
        [],
        500
    );
}
