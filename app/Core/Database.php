<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $config = Config::get('database.connections.mysql', []);

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $dbname = $config['database'] ?? 'kost_management';
        $charset = $config['charset'] ?? 'utf8mb4';
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        $options = $config['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        try {
            self::$connection = new PDO($dsn, $username, $password, $options);
            return self::$connection;
        } catch (PDOException $e) {
            Logger::error("Database connection failed: " . $e->getMessage());
            throw new RuntimeException("Koneksi database gagal: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public static function isConnected(): bool
    {
        try {
            self::connect();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function setConnection(?PDO $pdo): void
    {
        self::$connection = $pdo;
    }

    public static function beginTransaction(): bool
    {
        return self::connect()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::connect()->commit();
    }

    public static function rollBack(): bool
    {
        return self::connect()->rollBack();
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connect();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}

