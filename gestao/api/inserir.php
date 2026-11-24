<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

function responder(int $httpCode, array $dados): never
{
    http_response_code($httpCode);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(405, [
            'sucesso' => false,
            'mensagem' => 'Método não permitido.'
        ]);
    }

    $entrada = json_decode(file_get_contents('php://input'), true);

    $dataReferencia = $entrada['data_referencia'] ?? null;

    $data = DateTime::createFromFormat('!Y-m-d', (string) $dataReferencia);

    if (!$data || $data->format('Y-m-d') !== $dataReferencia) {
        responder(400, [
            'sucesso' => false,
            'mensagem' => 'Data de referência inválida.'
        ]);
    }

    if ($data->format('N') !== '1') {
        responder(400, [
            'sucesso' => false,
            'mensagem' => 'A data de referência deve ser uma segunda-feira.'
        ]);
    }

    $pdo = Database::getConnection();

    /*
     * Não permite iniciar outra execução enquanto existir
     * uma execução pendente ou em andamento para a mesma data.
     */
    $stmt = $pdo->prepare("
        SELECT id, status
        FROM tb_execucao_insercao
        WHERE data_referencia = :data
          AND status IN ('PENDENTE', 'PROCESSANDO')
        LIMIT 1
    ");

    $stmt->execute(['data' => $dataReferencia]);
    $execucaoAtiva = $stmt->fetch();

    if ($execucaoAtiva) {
        responder(409, [
            'sucesso' => false,
            'mensagem' => 'Já existe uma execução em andamento para esta data.',
            'execucao_id' => (int) $execucaoAtiva['id']
        ]);
    }

    /*
     * Busca os cartões:
     *
     * Prioridade:
     * 1. Cota fixa
     * 2. Supervisor
     * 3. Técnico
     *
     * Uma única linha por placa.
     *
     * Cartões que tiveram sucesso em qualquer execução
     * anterior da mesma data são ignorados.
     */
    $sql = "
        WITH tecnicos AS (
            SELECT
                tv.placa,
                tv.matcond AS matricula,
                tv.unidade AS uf,
                'Tecnico' AS classificacao,
                ROUND(ts.sldprjtd * 0.80, 2) AS valor,
                3 AS prioridade
            FROM tbsaldo ts
            INNER JOIN tbveiculo tv
                ON tv.matcond = ts.matricula
            WHERE ts.data = :data_referencia
              AND ts.status_insercao IN ('PENDENTE', 'ERRO')
        ),

        supervisores AS (
            SELECT
                tv.placa,
                tv.matcond AS matricula,
                tv.unidade AS uf,
                'Supervisor' AS classificacao,
                150.00 AS valor,
                2 AS prioridade
            FROM tbsupervisor ts
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
                1 AS prioridade
            FROM bdcombustivel.tbcotafixa tc
            LEFT JOIN tbveiculo tv
                ON tc.matricula = tv.matcond
                OR tc.placa = tv.placa
        ),

        base AS (
            SELECT * FROM cota_fixa
            UNION ALL
            SELECT * FROM supervisores
            UNION ALL
            SELECT * FROM tecnicos
        ),

        classificados AS (
            SELECT
                placa,
                matricula,
                uf,
                classificacao,
                valor,
                ROW_NUMBER() OVER (
                    PARTITION BY placa
                    ORDER BY prioridade
                ) AS rn
            FROM base
            WHERE placa IS NOT NULL
              AND TRIM(placa) <> ''
        )

        SELECT
            c.placa,
            c.matricula,
            c.uf,
            c.classificacao,
            c.valor
        FROM classificados c
        WHERE c.rn = 1

          AND NOT EXISTS (
              SELECT 1
              FROM tb_execucao_insercao_item ei
              INNER JOIN tb_execucao_insercao e
                  ON e.id = ei.execucao_id
              WHERE e.data_referencia = :data_referencia_sucesso
                AND ei.placa = c.placa
                AND ei.status = 'SUCESSO'
          )

        ORDER BY c.placa
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'data_referencia' => $dataReferencia,
        'data_referencia_sucesso' => $dataReferencia
    ]);

    $itens = $stmt->fetchAll();

    if (!$itens) {
        responder(422, [
            'sucesso' => false,
            'mensagem' => 'Não existem cartões pendentes ou com erro para processar nesta data.'
        ]);
    }

    $pdo->beginTransaction();

    /*
     * Cria a execução.
     */
    $stmtExecucao = $pdo->prepare("
        INSERT INTO tb_execucao_insercao (
            data_referencia,
            status,
            total,
            processados,
            sucessos,
            erros
        ) VALUES (
            :data_referencia,
            'PENDENTE',
            :total,
            0,
            0,
            0
        )
    ");

    $stmtExecucao->execute([
        'data_referencia' => $dataReferencia,
        'total' => count($itens)
    ]);

    $execucaoId = (int) $pdo->lastInsertId();

    /*
     * Salva uma fotografia dos dados que serão processados.
     * O worker não precisará recalcular os valores.
     */
    $stmtItem = $pdo->prepare("
        INSERT INTO tb_execucao_insercao_item (
            execucao_id,
            matricula,
            placa,
            uf,
            classificacao,
            valor,
            status
        ) VALUES (
            :execucao_id,
            :matricula,
            :placa,
            :uf,
            :classificacao,
            :valor,
            'PENDENTE'
        )
    ");

    foreach ($itens as $item) {
        $stmtItem->execute([
            'execucao_id' => $execucaoId,
            'matricula' => $item['matricula'],
            'placa' => $item['placa'],
            'uf' => $item['uf'],
            'classificacao' => $item['classificacao'],
            'valor' => $item['valor'] ?? 0
        ]);
    }

    $pdo->commit();

    /*
     * Inicia o worker em segundo plano.
     */
    $worker = realpath(__DIR__ . '/../workers/worker_insercao.php');

    if (!$worker || !is_file($worker)) {
        throw new RuntimeException('Arquivo do worker não encontrado.');
    }

    $phpBin = getenv('PHP_CLI_BIN') ?: '/usr/bin/php';

    $logDir = getenv('WORKER_LOG_DIR') ?: sys_get_temp_dir();

    if (!is_dir($logDir) || !is_writable($logDir)) {
        throw new RuntimeException('Diretório de logs do worker inválido ou sem permissão.');
    }

    $logFile = $logDir . '/worker-insercao-' . $execucaoId . '.log';

    $comando = sprintf(
        'nohup %s %s %d > %s 2>&1 < /dev/null &',
        escapeshellarg($phpBin),
        escapeshellarg($worker),
        $execucaoId,
        escapeshellarg($logFile)
    );

    exec($comando, $saida, $codigo);

    if ($codigo !== 0) {
        $stmt = $pdo->prepare("
            UPDATE tb_execucao_insercao
            SET status = 'ERRO',
                finalizado_em = NOW()
            WHERE id = :id
        ");

        $stmt->execute(['id' => $execucaoId]);

        throw new RuntimeException('Não foi possível iniciar o worker.');
    }

    responder(200, [
        'sucesso' => true,
        'mensagem' => 'Execução iniciada com sucesso.',
        'execucao_id' => $execucaoId,
        'total' => count($itens)
    ]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('[INSERCAO] ' . $e->getMessage());

    responder(500, [
        'sucesso' => false,
        'mensagem' => 'Não foi possível iniciar a execução.' . $e->getMessage()
    ]);
}
