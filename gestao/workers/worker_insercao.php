<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Acesso permitido somente via PHP CLI.\n");
}

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/registro_log.php';
require_once __DIR__ . '/../utils/integracao_ticketlog.php';

$execucaoId = isset($argv[1]) ? (int) $argv[1] : 0;

if ($execucaoId <= 0) {
    exit("Informe um ID de execução válido.\n");
}

$pdo = Database::getConnection();

$lockName = 'insercao_' . $execucaoId;

$stmtLock = $pdo->prepare("SELECT GET_LOCK(:nome, 0)");
$stmtLock->execute([':nome' => $lockName]);

if ((int) $stmtLock->fetchColumn() !== 1) {
    exit("Já existe um worker processando esta execução.\n");
}

function atualizarSaldoTecnico(
    PDO $pdo,
    string $matricula,
    string $dataReferencia,
    string $status
): bool {
    $sql = "
        UPDATE tbsaldo
        SET status_insercao = :status
        WHERE matricula = :matricula
          AND data = :data_referencia
          AND status_insercao IN ('PENDENTE', 'PROCESSANDO', 'ERRO')
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':status' => $status,
        ':matricula' => $matricula,
        ':data_referencia' => $dataReferencia
    ]);

    return $stmt->rowCount() > 0;
}

function registrarResultado(
    PDO $pdo,
    string $dataReferencia,
    array $item,
    array $resultado,
    string $status,
    string $integracao
): void {
    try {
        $mensagem = $resultado['mensagem'] ?? 'Resultado não informado.';

        if (!empty($resultado['resultado_indeterminado'])) {
            $mensagem = '[RESULTADO INDETERMINADO] ' . $mensagem;
        }

        registrar_log_semanal(
            $pdo,
            $dataReferencia,
            $item['matricula'] !== null ? (string) $item['matricula'] : null,
            $item['placa'],
            $resultado['etapa'] ?? 'PROCESSAMENTO',
            $integracao,
            (float) $item['valor'],
            $status === 'SUCESSO' ? 1 : 0,
            isset($resultado['http_code'])
                ? (int) $resultado['http_code']
                : null,
            $mensagem
        );

    } catch (Throwable $e) {
        error_log(
            '[WORKER] Falha ao registrar log do item ' .
            $item['id'] . ': ' . $e->getMessage()
        );
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT id, data_referencia, status
        FROM tb_execucao_insercao
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([':id' => $execucaoId]);
    $execucao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$execucao) {
        throw new RuntimeException('Execução não encontrada.');
    }

    if (in_array($execucao['status'], ['CONCLUIDO', 'CONCLUIDO_COM_ERROS'])) {
        exit("Esta execução já foi concluída.\n");
    }

    $dataReferencia = $execucao['data_referencia'];

    $stmt = $pdo->prepare("
        UPDATE tb_execucao_insercao
        SET status = 'PROCESSANDO',
            iniciado_em = COALESCE(iniciado_em, NOW())
        WHERE id = :id
    ");

    $stmt->execute([':id' => $execucaoId]);

    echo "[INICIO] Execução {$execucaoId} - Referência {$dataReferencia}\n";

    while (true) {

        /*
         * Reserva atomicamente o próximo item pendente.
         */
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            SELECT
                id,
                matricula,
                placa,
                uf,
                classificacao,
                valor
            FROM tb_execucao_insercao_item
            WHERE execucao_id = :execucao_id
              AND status = 'PENDENTE'
            ORDER BY id ASC
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([':execucao_id' => $execucaoId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $pdo->commit();
            break;
        }

        $stmt = $pdo->prepare("
            UPDATE tb_execucao_insercao_item
            SET status = 'PROCESSANDO',
                tentativas = tentativas + 1,
                iniciado_em = NOW(),
                mensagem = NULL
            WHERE id = :id
        ");

        $stmt->execute([':id' => $item['id']]);

        /*
         * Técnicos possuem registro correspondente em tbsaldo.
         * Supervisores e cotas fixas não dependem desse registro.
         */
        if ($item['classificacao'] === 'Tecnico') {
            $atualizou = atualizarSaldoTecnico(
                $pdo,
                (string) $item['matricula'],
                $dataReferencia,
                'PROCESSANDO'
            );

            if (!$atualizou) {
                $pdo->prepare("
                    UPDATE tb_execucao_insercao_item
                    SET status = 'ERRO',
                        mensagem = 'Saldo do técnico não está pendente ou não foi encontrado.',
                        finalizado_em = NOW()
                    WHERE id = :id
                ")->execute([':id' => $item['id']]);

                $pdo->prepare("
                    UPDATE tb_execucao_insercao
                    SET processados = processados + 1,
                        erros = erros + 1
                    WHERE id = :id
                ")->execute([':id' => $execucaoId]);

                $pdo->commit();

                registrarResultado(
                    $pdo,
                    $dataReferencia,
                    $item,
                    [
                        'etapa' => 'VALIDACAO',
                        'mensagem' => 'Saldo do técnico não está pendente ou não foi encontrado.',
                        'http_code' => null
                    ],
                    'ERRO',
                    'SISTEMA'
                );

                continue;
            }
        }

        $pdo->commit();

        $placa = (string) $item['placa'];
        $uf = strtoupper(trim((string) $item['uf']));
        $valor = (float) $item['valor'];

        echo "[PROCESSANDO] {$placa} | {$item['matricula']} | R$ " .
            number_format($valor, 2, ',', '.') . "\n";

        /*
         * Somente Ticketlog está habilitada.
         */
        $resultado = null;
        $integracao = 'TICKETLOG';

        try {
            if (!in_array($uf, ['RJ', 'ES', ''], true)) {
                $resultado = [
                    'sucesso' => false,
                    'http_code' => null,
                    'etapa' => 'VALIDACAO_UF',
                    'mensagem' => "UF {$uf} não possui integração habilitada neste momento.",
                    'resultado_indeterminado' => false
                ];

                $integracao = 'SISTEMA';

            } else {
                $resultado = ticketlogProcessarCartao(
                    $placa,
                    $valor
                );
            }

        } catch (Throwable $e) {
            $resultado = [
                'sucesso' => false,
                'http_code' => null,
                'etapa' => 'PROCESSAMENTO',
                'mensagem' => $e->getMessage(),
                'resultado_indeterminado' => true
            ];
        }

        $status = !empty($resultado['sucesso'])
            ? 'SUCESSO'
            : 'ERRO';

        /*
         * Persiste o resultado individual e os contadores.
         */
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE tb_execucao_insercao_item
            SET status = :status,
                mensagem = :mensagem,
                finalizado_em = NOW()
            WHERE id = :id
        ");

        $mensagem = $resultado['mensagem'] ?? 'Resultado não informado.';

        if (!empty($resultado['resultado_indeterminado'])) {
            $mensagem = '[RESULTADO INDETERMINADO] ' . $mensagem;
        }

        $stmt->execute([
            ':status' => $status,
            ':mensagem' => $mensagem,
            ':id' => $item['id']
        ]);

        if ($item['classificacao'] === 'Tecnico') {
            atualizarSaldoTecnico(
                $pdo,
                (string) $item['matricula'],
                $dataReferencia,
                $status
            );
        }

        $colunaContador = $status === 'SUCESSO'
            ? 'sucessos'
            : 'erros';

        $pdo->prepare("
            UPDATE tb_execucao_insercao
            SET processados = processados + 1,
                {$colunaContador} = {$colunaContador} + 1
            WHERE id = :id
        ")->execute([':id' => $execucaoId]);

        $pdo->commit();

        registrarResultado(
            $pdo,
            $dataReferencia,
            $item,
            $resultado,
            $status,
            $integracao
        );

        echo "[{$status}] {$placa} - {$mensagem}\n";
    }

    /*
     * Confere se existem itens pendentes ou abandonados
     * em PROCESSANDO antes de encerrar a execução.
     */
    $stmt = $pdo->prepare("
        SELECT
            SUM(status = 'PENDENTE') AS pendentes,
            SUM(status = 'PROCESSANDO') AS processando,
            SUM(status = 'ERRO') AS erros
        FROM tb_execucao_insercao_item
        WHERE execucao_id = :id
    ");

    $stmt->execute([':id' => $execucaoId]);
    $contadores = $stmt->fetch(PDO::FETCH_ASSOC);

    $pendentes = (int) ($contadores['pendentes'] ?? 0);
    $processando = (int) ($contadores['processando'] ?? 0);
    $erros = (int) ($contadores['erros'] ?? 0);

    if ($pendentes === 0 && $processando === 0) {
        $statusFinal = $erros > 0
            ? 'CONCLUIDO_COM_ERROS'
            : 'CONCLUIDO';

        $stmt = $pdo->prepare("
            UPDATE tb_execucao_insercao
            SET status = :status,
                finalizado_em = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':status' => $statusFinal,
            ':id' => $execucaoId
        ]);

        echo "[FINALIZADO] Execução {$execucaoId}: {$statusFinal}\n";

    } else {
        echo "[ATENCAO] Existem itens pendentes ou em processamento. " .
            "A execução permanecerá aberta.\n";
    }

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        '[WORKER INSERCAO] Execução ' .
        $execucaoId . ': ' . $e->getMessage()
    );

    echo "[ERRO FATAL] " . $e->getMessage() . "\n";

    exit(1);

} finally {
    $stmt = $pdo->prepare("SELECT RELEASE_LOCK(:nome)");
    $stmt->execute([':nome' => $lockName]);
}
