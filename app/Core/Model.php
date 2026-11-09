<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::connect();
    }

    protected static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    protected static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    protected static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($params);
    }

    protected static function lastInsertId(): string
    {
        return self::db()->lastInsertId();
    }
}

