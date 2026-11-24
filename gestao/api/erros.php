<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido.'
        ]);

        exit;
    }

    $dataReferencia = $_GET['data_referencia'] ?? '';

    $data = DateTime::createFromFormat('!Y-m-d', $dataReferencia);

    if (
        !$data ||
        $data->format('Y-m-d') !== $dataReferencia
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Data de referência inválida.'
        ]);

        exit;
    }

    $db = Database::getConnection();

    $sql = "
        SELECT
            tls.id,
            tls.matricula,
            tls.placa,
            tls.status,
            tls.texto,
            tls.valor,
            tls.data_hora
        FROM tb_log_semanal tls
        JOIN tbveiculo v ON tls.placa = v.placa
        WHERE data_referencia = :data_referencia
        AND tls.status = 0
        AND v.unidade in ('ES', 'RJ')
        ORDER BY data_hora DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':data_referencia' => $dataReferencia
    ]);

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sucessos = 0;
    $erros = 0;

    foreach ($logs as &$log) {
        $log['status'] = (int) $log['status'];
        $log['valor'] = $log['valor'] !== null
            ? (float) $log['valor']
            : null;

        if ($log['status'] === 1) {
            $sucessos++;
            $log['status_descricao'] = 'Sucesso';
        } else {
            $erros++;
            $log['status_descricao'] = 'Erro';
        }
    }

    unset($log);

    echo json_encode([
        'success' => true,
        'data_referencia' => $dataReferencia,
        'total' => count($logs),
        'sucessos' => $sucessos,
        'erros' => $erros,
        'logs' => $logs
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    error_log('[ERROS API] ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Erro ao consultar os registros.'
    ], JSON_UNESCAPED_UNICODE);
}