<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

date_default_timezone_set('America/Sao_Paulo');

/*
|--------------------------------------------------------------------------
| Resposta JSON
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Requisição HTTP
|--------------------------------------------------------------------------
*/

function requisicaoJson(
    string $url,
    string $metodo,
    array $payload,
    string $authorization
): array {
    if (!function_exists('curl_init')) {
        throw new RuntimeException('A extensão cURL do PHP não está instalada.');
    }

    if ($authorization === '') {
        throw new RuntimeException(
            'Credencial de integração não configurada no ambiente.'
        );
    }

    $curl = curl_init($url);

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_POSTFIELDS => json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ),
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $authorization,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 40,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($curl);
    $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);

    curl_close($curl);

    if ($response === false) {
        throw new RuntimeException(
            'Falha na comunicação com a API: ' . $curlError
        );
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        throw new RuntimeException(
            "API retornou HTTP {$httpCode}."
        );
    }

    try {
        $json = json_decode(
            $response,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (JsonException $e) {
        throw new RuntimeException(
            'A API retornou uma resposta JSON inválida.'
        );
    }

    if (!is_array($json)) {
        throw new RuntimeException(
            'Formato inesperado na resposta da API.'
        );
    }

    return $json;
}

/*
|--------------------------------------------------------------------------
| Ticketlog: histórico de transações
|--------------------------------------------------------------------------
*/

function buscarTransacoesTicketlog(string $dataReferencia): array
{
    $url = 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/transacaoVeiculo/search';

    $authorization = trim(
        getenv('TICKETLOG_TRANSACOES_AUTH') ?: 'Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
    );

    $inicio = new DateTimeImmutable($dataReferencia);
    $inicio = $inicio->modify('-21 days');

    $transacoes = [];

    for (
        $dia = $inicio;
        $dia <= new DateTimeImmutable($dataReferencia);
        $dia = $dia->modify('+1 day')
    ) {
        $data = $dia->format('Y-m-d');

        $payload = [
            'codigoCliente' => 183062,
            'codigoTipoCartao' => 4,
            'dataTransacaoInicial' => $data . 'T00:00:00',
            'dataTransacaoFinal' => $data . 'T23:59:59',
            'considerarTransacao' => 'V',
            'ordem' => 'S',
        ];

        $resultado = requisicaoJson(
            $url,
            'POST',
            $payload,
            $authorization
        );

        if (!isset($resultado['transacoes'])) {
            throw new RuntimeException(
                "Resposta inesperada da Ticketlog para {$data}."
            );
        }

        if (!is_array($resultado['transacoes'])) {
            throw new RuntimeException(
                "Lista de transações inválida na Ticketlog para {$data}."
            );
        }

        $transacoes = array_merge(
            $transacoes,
            $resultado['transacoes']
        );
    }

    return $transacoes;
}

/*
|--------------------------------------------------------------------------
| ValeCard: histórico de transações
|--------------------------------------------------------------------------
*/

function buscarTransacoesValecard(string $dataReferencia): array
{
    $url = 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscartransacoesfrota';

    $authorization = trim(
        getenv('VALECARD_TRANSACOES_AUTH') ?: 'Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc='
    );

    $dataInicial = (new DateTimeImmutable($dataReferencia))
        ->modify('-21 days')
        ->format('Y-m-d');

    $payload = [
        'contratoCliente' => '524073',
        'dataInicial' => $dataInicial,
        'dataFinal' => $dataReferencia,
    ];

    $resultado = requisicaoJson(
        $url,
        'POST',
        $payload,
        $authorization
    );

    // O legado espera uma lista diretamente na resposta.
    // Também aceitamos a resposta dentro de "transacoes".
    $transacoes = $resultado['transacoes'] ?? $resultado;

    if (!is_array($transacoes)) {
        throw new RuntimeException(
            'Resposta inválida da ValeCard.'
        );
    }

    return $transacoes;
}

/*
|--------------------------------------------------------------------------
| Agrupamento das médias por placa
|--------------------------------------------------------------------------
*/

function calcularMediasPorPlaca(
    array $transacoes,
    string $integracao
): array {
    $placas = [];
    $medias = [];

    foreach ($transacoes as $transacao) {
        if (!is_array($transacao)) {
            continue;
        }

        $placa = strtoupper(trim((string) ($transacao['placa'] ?? '')));

        if ($placa === '') {
            continue;
        }

        if ($integracao === 'ticketlog') {
            $valorLitro = (float) ($transacao['valorLitro'] ?? 0);
            $kmPorLitro = (float) ($transacao['quilometrosPorLitro'] ?? 0);
            $combustivel = (string) (
                $transacao['tipoCombustivel'] ?? 'gasolina comum'
            );

            $dataTransacao = $transacao['dataTransacao'] ?? null;
        } else {
            $valorLitro = (float) ($transacao['valorUnitario'] ?? 0);
            $kmPorLitro = (float) ($transacao['consumo'] ?? 0);
            $combustivel = (string) (
                $transacao['produto'] ?? 'gasolina comum'
            );

            $dataTransacao = $transacao['data'] ?? null;
        }

        if (!$dataTransacao || $valorLitro < 0 || $kmPorLitro < 0) {
            continue;
        }

        try {
            if ($integracao === 'ticketlog') {
                $data = new DateTimeImmutable((string) $dataTransacao);
            } else {
                $data = DateTimeImmutable::createFromFormat(
                    'd/m/Y H:i:s',
                    (string) $dataTransacao
                );
            }
        } catch (Throwable $e) {
            continue;
        }

        if (!$data) {
            continue;
        }

        if (!isset($placas[$placa])) {
            $placas[$placa] = [
                'valor_litro' => 0,
                'data_ultima_transacao' => null,
                'total_km_litro' => 0,
                'quantidade' => 0,
                'combustiveis' => [],
            ];
        }

        /*
         * Mantém o preço da transação mais recente,
         * conforme o comportamento do código legado.
         */

        $dataSalva = $placas[$placa]['data_ultima_transacao'];

        if ($dataSalva === null || $data > $dataSalva) {
            $placas[$placa]['valor_litro'] = $valorLitro;
            $placas[$placa]['data_ultima_transacao'] = $data;
        }

        $placas[$placa]['total_km_litro'] += $kmPorLitro;
        $placas[$placa]['quantidade']++;

        $combustivel = strtolower(trim($combustivel));

        if (!isset($placas[$placa]['combustiveis'][$combustivel])) {
            $placas[$placa]['combustiveis'][$combustivel] = 0;
        }

        $placas[$placa]['combustiveis'][$combustivel]++;
    }

    foreach ($placas as $placa => $dados) {
        if ($dados['quantidade'] <= 0) {
            continue;
        }

        arsort($dados['combustiveis']);

        $combustivelMaisUsado = array_key_first(
            $dados['combustiveis']
        );

        $medias[$placa] = [
            'valorLitroMedio' => round(
                $dados['valor_litro'],
                2
            ),

            'quilometrosPorLitroMedio' => round(
                $dados['total_km_litro'] / $dados['quantidade'],
                2
            ),

            'combustivel' => $combustivelMaisUsado,
        ];
    }

    return $medias;
}

/*
|--------------------------------------------------------------------------
| Preços de combustível: fallback
|--------------------------------------------------------------------------
*/

function buscarPrecosCombustivel(): array
{
    $precos = [
        'gasolina' => 6.60,
        'diesel' => 7.50,
    ];

    $curl = curl_init(
        'https://combustivelapi.com.br/api/precos/'
    );

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($curl);
    $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($response === false || $httpCode !== 200) {
        return $precos;
    }

    $resultado = json_decode($response, true);

    if (!is_array($resultado)) {
        return $precos;
    }

    if (isset($resultado['precos']['gasolina']['br'])) {
        $precos['gasolina'] = (float) str_replace(
            ',',
            '.',
            (string) $resultado['precos']['gasolina']['br']
        );
    }

    if (isset($resultado['precos']['diesel']['br'])) {
        $precos['diesel'] = (float) str_replace(
            ',',
            '.',
            (string) $resultado['precos']['diesel']['br']
        );
    }

    return $precos;
}

/*
|--------------------------------------------------------------------------
| Validação da requisição
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método não permitido.', [], 405);
}

$entrada = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($entrada)) {
    responder(false, 'JSON inválido.', [], 400);
}

$dataReferencia = $entrada['data_referencia'] ?? '';

$date = DateTimeImmutable::createFromFormat(
    '!Y-m-d',
    (string) $dataReferencia
);

if (
    !$date ||
    $date->format('Y-m-d') !== $dataReferencia
) {
    responder(false, 'Data de referência inválida.', [], 400);
}

/*
 * O processo semanal utiliza segunda-feira como referência.
 */

if ($date->format('N') !== '1') {
    responder(
        false,
        'A data de referência precisa ser uma segunda-feira.',
        [],
        400
    );
}

/*
|--------------------------------------------------------------------------
| Processo principal
|--------------------------------------------------------------------------
*/

try {
    $pdo = Database::getConnection();

    /*
     * A data de gravação é a segunda-feira selecionada.
     * A data de consulta das transações é 21 dias antes.
     */

    $dataGravacao = $date->format('Y-m-d');

    $dataConsulta = $date
        ->modify('-21 days')
        ->format('Y-m-d');

    /*
     * Busca os históricos das duas integrações.
     *
     * Se uma consulta falhar, o cálculo é interrompido.
     * Assim não gravamos saldos baseados em histórico parcial.
     */

    $transacoesTicketlog = buscarTransacoesTicketlog(
        $dataConsulta
    );

    $transacoesValecard = buscarTransacoesValecard(
        $dataConsulta
    );

    $mediasTicketlog = calcularMediasPorPlaca(
        $transacoesTicketlog,
        'ticketlog'
    );

    $mediasValecard = calcularMediasPorPlaca(
        $transacoesValecard,
        'valecard'
    );

    $precos = buscarPrecosCombustivel();

    /*
     * Consulta dos técnicos ativos.
     *
     * A classificação é feita por placa:
     * - Cota fixa
     * - Supervisor
     * - Técnico
     *
     * EXISTS evita multiplicação de registros caso existam
     * várias cotas fixas ou registros de supervisor.
     *
     * O ROW_NUMBER garante apenas um veículo por matrícula,
     * pois tbsaldo possui chave única por matrícula e data.
     */

    $sqlTecnicos = "
        WITH classificacao_cte AS (
            SELECT
                v.idtbveiculo,
                v.matcond,
                v.placa,

                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM bdcombustivel.tbcotafixa c
                        WHERE c.placa = v.placa
                    ) THEN 'Cota Fixa'

                    WHEN EXISTS (
                        SELECT 1
                        FROM bdfrota.tbsupervisor s
                        WHERE s.matricula = v.matcond
                    ) THEN 'Supervisor'

                    WHEN v.matcond IS NOT NULL
                         AND TRIM(v.matcond) <> ''
                    THEN 'Tecnico'

                    ELSE 'Sem Matricula'
                END AS classificacao

            FROM bdfrota.tbveiculo v

            WHERE v.placa IS NOT NULL
              AND v.placa <> ''
              AND v.statusvel = 1
        ),

        km_por_condutor AS (
            SELECT
                tkd.matricula,
                SUM(tkd.kmsrodados) AS total_km,
                COUNT(tkd.idtbkmrodadosdia) AS dias_registrados,
                tf.ccusto

            FROM bdfrota.tbkmrodadosdia tkd

            INNER JOIN bdaniel.tbfuncionario tf
                ON tf.matricula = tkd.matricula

            WHERE EXISTS (
                SELECT 1
                FROM bdfrota.tbusuario u
                WHERE u.matricula = tkd.matricula
                  AND u.perfil = 0
            )

              AND tkd.dia > DATE_SUB(
                  NOW(),
                  INTERVAL 21 DAY
              )

            GROUP BY
                tkd.matricula,
                tf.ccusto
        ),

        candidatos AS (
            SELECT
                c.matricula,
                tv.placa,
                tv.categoria,
                tv.unidade,
                tvel.tipo,

                15 AS cota_minima,

                CASE
                    WHEN tvel.tipo = 'LEVE' THEN 35
                    WHEN tvel.tipo = 'MOTOCICLETA' THEN 27
                    WHEN tvel.tipo = 'UTILITÁRIO' THEN 60
                    WHEN tvel.tipo = 'REBOQUE' THEN 60
                    WHEN tvel.tipo = 'PESADA' THEN 80
                    ELSE 35
                END AS cota_maxima,

                class.classificacao,

                tf.ccusto,

                ROW_NUMBER() OVER (
                    PARTITION BY c.matricula
                    ORDER BY
                        CASE
                            WHEN c.placaassoc = tv.placa THEN 0
                            ELSE 1
                        END,
                        tv.status DESC,
                        tv.visivel DESC,
                        tv.idtbveiculo DESC
                ) AS numero_veiculo

            FROM bdfrota.tbcondutor c

            INNER JOIN bdfrota.tbveiculo tv
                ON tv.matcond = c.matricula

            INNER JOIN bdfrota.tbatipovel tvel
                ON tv.tipovel = tvel.idtbatipovel

            INNER JOIN bdaniel.tbfuncionario tf
                ON tf.matricula = c.matricula

            INNER JOIN classificacao_cte class
                ON class.idtbveiculo = tv.idtbveiculo

            WHERE c.ativo = 1
              AND c.matricula <> ''
              AND class.classificacao = 'Tecnico'
              AND tf.status = 'Ativo'

              AND EXISTS (
                  SELECT 1
                  FROM bdfrota.tbusuario u
                  WHERE u.matricula = c.matricula
                    AND u.perfil = 0
              )
        )

        SELECT
            c.matricula,
            c.placa,
            c.categoria,
            c.unidade,
            c.tipo,
            c.cota_minima,
            c.cota_maxima,
            c.classificacao,
            c.ccusto,

            CASE
                WHEN k.dias_registrados > 0
                THEN k.total_km / k.dias_registrados
                ELSE 0
            END AS media_km_diario

        FROM candidatos c

        LEFT JOIN km_por_condutor k
            ON k.matricula = c.matricula

        WHERE c.numero_veiculo = 1

        ORDER BY c.matricula
    ";

    $stmt = $pdo->query($sqlTecnicos);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Consulta dos tipos de veículos e KM/L previsto.
     */

    $sqlTipos = "
        SELECT
            tipo_combustivel,
            descricao,
            km_litro_previsto

        FROM bdcombustivel.tbtipoveiculo
    ";

    $stmtTipos = $pdo->query($sqlTipos);
    $tiposVeiculo = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

    $arrayTipos = [];

    foreach ($tiposVeiculo as $tipo) {
        $descricao = mb_strtoupper(
            trim((string) $tipo['descricao']),
            'UTF-8'
        );

        $arrayTipos[$descricao] = (float) (
            $tipo['km_litro_previsto'] ?? 0
        );
    }

    /*
     * Prepara o UPSERT.
     *
     * A chave UNIQUE (matricula, data) impede duplicidade.
     * Se o registro já existir, os valores calculados serão
     * atualizados.
     */

    $sqlInsert = "
        INSERT INTO bdfrota.tbsaldo (
            matricula,
            data,
            sldprjtd,
            saldo_real_calculado,
            saldo,
            valoraplicado,
            orcsemanal,
            kmproj,
            kmorcsem,
            valorcomb
        ) VALUES (
            :matricula,
            :data,
            :sldprjtd,
            :saldo_real_calculado,
            :saldo,
            :valoraplicado,
            :orcsemanal,
            :kmproj,
            :kmorcsem,
            :valorcomb
        )

        ON DUPLICATE KEY UPDATE
            sldprjtd = IF(status_insercao = 0, VALUES(sldprjtd), sldprjtd),
            saldo_real_calculado = IF(status_insercao = 0, VALUES(saldo_real_calculado), saldo_real_calculado),
            saldo = IF(status_insercao = 0, VALUES(saldo), saldo),
            valoraplicado = IF(status_insercao = 0, VALUES(valoraplicado), valoraplicado),
            orcsemanal = IF(status_insercao = 0, VALUES(orcsemanal), orcsemanal),
            kmproj = IF(status_insercao = 0, VALUES(kmproj), kmproj),
            kmorcsem = IF(status_insercao = 0, VALUES(kmorcsem), kmorcsem),
            valorcomb = IF(status_insercao = 0, VALUES(valorcomb), valorcomb)
    ";

    $stmtInsert = $pdo->prepare($sqlInsert);

    $resultados = [];

    $totalInseridos = 0;
    $totalAtualizados = 0;

    $pdo->beginTransaction();

    foreach ($usuarios as $usuario) {
        $matricula = trim((string) $usuario['matricula']);

        $placa = strtoupper(
            trim((string) $usuario['placa'])
        );

        $unidade = strtoupper(
            trim((string) $usuario['unidade'])
        );

        $tipo = mb_strtoupper(
            trim((string) $usuario['tipo']),
            'UTF-8'
        );

        $ccusto = mb_strtolower(
            (string) ($usuario['ccusto'] ?? ''),
            'UTF-8'
        );

        $mediaKmDiario = (float) $usuario['media_km_diario'];

        $centroCusto = mb_strtolower(
            (string) ($usuario['ccusto'] ?? ''),
            'UTF-8'
        );
        /*
         * Limites por centro de custo.
         */

        if (stripos($centroCusto, 'externa') !== false) {
            if ($tipo === 'UTILITÁRIO') {
                $cotaMaxima = 74;
                $cotaMinima = 44;
            } elseif ($tipo === 'PESADA') {
                $cotaMaxima = 80;
                $cotaMinima = 60;
            } else {
                $cotaMaxima = 64;
                $cotaMinima = 44;
            }
        } else {
            $cotaMaxima = 45;
            $cotaMinima = 26;
        }

        /*
         * Seleciona a integração de acordo com a unidade.
         */

        if (in_array($unidade, ['RJ', 'ES'])) {
            $fonte = $mediasTicketlog;
            $integracao = 'Ticketlog';
        } else {
            $fonte = $mediasValecard;
            $integracao = 'ValeCard';
        }

        /*
         * Preço e combustível.
         */

        if (isset($fonte[$placa])) {
            $valorCombustivel = (
                $fonte[$placa]['valorLitroMedio'] > 0
            )
                ? (float) $fonte[$placa]['valorLitroMedio']
                : $precos['gasolina'];

            $tipoCombustivel = strtolower(
                (string) $fonte[$placa]['combustivel']
            );
        } else {
            $tipoCombustivel = 'gasolina comum';
            $valorCombustivel = $precos['gasolina'];
        }

        /*
         * KM por litro previsto.
         */

        $kmLitro = $arrayTipos[$tipo] ?? 10;

        if ($kmLitro <= 0) {
            $kmLitro = 10;
        }

        if ($valorCombustivel <= 0) {
            throw new RuntimeException(
                "Preço de combustível inválido para a placa {$placa}."
            );
        }

        /*
         * Cálculo do valor individual.
         */

        $valorIndividual = (
            $mediaKmDiario / $kmLitro
        ) * $valorCombustivel;

        /*
         * Saldo real antes da aplicação dos limites.
         */

        $saldoRealCalculado = round(
            $valorIndividual * 5,
            2
        );

        /*
         * Aplicação dos limites mínimo e máximo.
         */

        if (
            $mediaKmDiario <= 5 ||
            $valorIndividual < $cotaMinima
        ) {
            $valorIndividual = $cotaMinima;
        }

        if ($valorIndividual > $cotaMaxima) {
            $valorIndividual = $cotaMaxima;
        }

        $valorIndividual = round($valorIndividual, 2);

        /*
         * Saldo semanal e percentual de 80%.
         */

        $valorSemanal = $valorIndividual * 5;

        $valorAInserir = round(
            $valorSemanal * 0.80,
            2
        );

        /*
         * Quilômetros projetados e orçados.
         */

        $kmProjetado = round(
            (
                ($valorIndividual * 0.80) /
                $valorCombustivel
            ) * $kmLitro,
            2
        );

        $kmOrcamentoSemanal = round(
            (
                $valorAInserir /
                $valorCombustivel
            ) * $kmLitro,
            2
        );

        /*
         * Grava ou atualiza o saldo.
         */

        $stmtInsert->execute([
            ':matricula' => $matricula,
            ':data' => $dataGravacao,
            ':sldprjtd' => $valorSemanal,
            ':saldo_real_calculado' => $saldoRealCalculado,
            ':saldo' => $valorAInserir,
            ':valoraplicado' => $valorAInserir,
            ':orcsemanal' => $valorAInserir,
            ':kmproj' => $kmProjetado,
            ':kmorcsem' => $kmOrcamentoSemanal,
            ':valorcomb' => $valorCombustivel,
        ]);

        /*
         * rowCount:
         * 1 = inserido
         * 2 = atualizado
         * 0 = registro já estava com os mesmos valores
         */

        $rowCount = $stmtInsert->rowCount();

        if ($rowCount === 1) {
            $totalInseridos++;
        } elseif ($rowCount === 2) {
            $totalAtualizados++;
        }

        $resultados[] = [
            'matricula' => $matricula,
            'placa' => $placa,
            'unidade' => $unidade,
            'classificacao' => $usuario['classificacao'],
            'integracao' => $integracao,
            'media_km_diario' => round($mediaKmDiario, 2),
            'km_litro' => $kmLitro,
            'valorcomb' => $valorCombustivel,
            'saldo_real_calculado' => $saldoRealCalculado,
            'sldprjtd' => $valorSemanal,
            'saldo' => $valorAInserir,
            'valoraplicado' => $valorAInserir,
            'orcsemanal' => $valorAInserir,
            'kmproj' => $kmProjetado,
            'kmorcsem' => $kmOrcamentoSemanal,
            'status_insercao' => 'PENDENTE',
        ];
    }

    $pdo->commit();

    responder(
        true,
        'Cálculo dos saldos concluído.',
        [
            'data_referencia' => $dataGravacao,
            'data_consulta' => $dataConsulta,
            'total' => count($resultados),
            'total_inseridos' => $totalInseridos,
            'total_atualizados' => $totalAtualizados,
            'total_ticketlog_transacoes' => count($transacoesTicketlog),
            'total_valecard_transacoes' => count($transacoesValecard),
            'total_ticketlog_placas' => count($mediasTicketlog),
            'total_valecard_placas' => count($mediasValecard),
            'saldos' => $resultados,
        ]
    );
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        '[SALDO][CALCULAR] ' . $e->getMessage()
    );

    responder(
        false,
        'Não foi possível calcular os saldos: ' . $e->getMessage(),
        [],
        500
    );
}
