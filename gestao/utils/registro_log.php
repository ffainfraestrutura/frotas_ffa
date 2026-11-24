<?php

declare(strict_types=1);

function registrar_log_semanal(
    PDO $pdo,
    string $dataReferencia,
    ?string $matricula,
    ?string $placa,
    ?string $etapa,
    ?string $integracao,
    ?float $valor,
    int $status,
    ?int $httpCode,
    string $texto
): bool {
    try {
        $sql = "
            INSERT INTO tb_log_semanal (
                data_referencia,
                matricula,
                placa,
                etapa,
                integracao,
                valor,
                status,
                http_code,
                texto
            ) VALUES (
                :data_referencia,
                :matricula,
                :placa,
                :etapa,
                :integracao,
                :valor,
                :status,
                :http_code,
                :texto
            )
        ";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':data_referencia' => $dataReferencia,
            ':matricula' => $matricula,
            ':placa' => $placa,
            ':etapa' => $etapa,
            ':integracao' => $integracao,
            ':valor' => $valor,
            ':status' => $status,
            ':http_code' => $httpCode,
            ':texto' => $texto
        ]);

    } catch (PDOException $e) {
        error_log('[LOG SEMANAL] ' . $e->getMessage());

        throw new RuntimeException(
            'Não foi possível registrar o log da inserção.',
            0,
            $e
        );
    }
}