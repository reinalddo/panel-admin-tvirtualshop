<?php

namespace App\Infrastructure\Database;

use PDO;
use PDOException;
use RuntimeException;

final class PdoConnectionFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public static function fromConfig(array $config): PDO
    {
        foreach (['host', 'port', 'name', 'user', 'password'] as $key) {
            if (!array_key_exists($key, $config)) {
                throw new RuntimeException("Falta el parámetro de base de datos: {$key}");
            }
        }

        $charset = $config['charset'] ?? 'utf8mb4';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            (int) $config['port'],
            $config['name'],
            $charset
        );

        try {
            $pdo = new PDO($dsn, (string) $config['user'], (string) $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('No se pudo conectar a la base panel_master: ' . $e->getMessage(), 0, $e);
        }

        return $pdo;
    }

    private function __construct()
    {
    }
}
