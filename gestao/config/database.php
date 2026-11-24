<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = getenv('DB_HOST') ?: '10.17.0.5';
        $database = getenv('DB_DATABASE') ?: 'bdfrota';
        $username = getenv('DB_USERNAME') ?: 'blvp';
        $password = getenv('DB_PASSWORD') ?: '@F3r7n6c2';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $host,
            $database
        );

        try {
            self::$connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                ]
            );

            self::$connection->exec(
                "SET time_zone = '-03:00'"
            );

            return self::$connection;

        } catch (PDOException $e) {
            error_log(
                '[DATABASE] Erro de conexão: ' . $e->getMessage()
            );

            throw new RuntimeException(
                'Não foi possível conectar ao banco de dados.'
            );
        }
    }
}
