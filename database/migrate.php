<?php

declare(strict_types=1);

// Migration Runner — Kost Management System

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

Config::loadEnv(BASE_PATH . '/.env');

echo "==================================================\n";
echo "  Kost Management System — Migration Runner\n";
echo "==================================================\n\n";

try {
    $pdo = Database::connect();
    echo "[INFO] Terhubung ke database: " . config('database.connections.mysql.database') . "\n";
} catch (\Throwable $e) {
    echo "[ERROR] Gagal terhubung ke database: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. Ensure migrations table exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `migrations` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL UNIQUE,
        `batch` INT NOT NULL,
        `executed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// 2. Read executed migrations
$stmt = $pdo->query("SELECT `migration` FROM `migrations`");
$executed = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

// 3. Check schema.sql
$schemaFile = BASE_PATH . '/database/schema.sql';
if (file_exists($schemaFile) && !in_array('000_full_schema.sql', $executed, true)) {
    echo "[MIGRATE] Menjalankan database/schema.sql (33 tables)...\n";
    $sql = file_get_contents($schemaFile);

    try {
        $pdo->exec($sql);
        $insertStmt = $pdo->prepare("INSERT INTO `migrations` (`migration`, `batch`) VALUES (?, 1)");
        $insertStmt->execute(['000_full_schema.sql']);
        echo "[SUCCESS] Skema 33 tabel berhasil dibuat!\n";
    } catch (\Throwable $e) {
        echo "[ERROR] Eksekusi schema.sql gagal: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "[INFO] Skema utama sudah pernah dijalankan sebelumnya.\n";
}

// 3.5. Execute pending migration files in database/migrations/
$migrationFiles = glob(BASE_PATH . '/database/migrations/*.sql');
if ($migrationFiles) {
    sort($migrationFiles);
    foreach ($migrationFiles as $file) {
        $filename = basename($file);
        if (!in_array($filename, $executed, true)) {
            echo "[MIGRATE] Menjalankan migration: {$filename}...\n";
            $sql = file_get_contents($file);
            try {
                $pdo->exec($sql);
                $insertStmt = $pdo->prepare("INSERT INTO `migrations` (`migration`, `batch`) VALUES (?, 2)");
                $insertStmt->execute([$filename]);
                echo "  [SUCCESS] {$filename} berhasil dijalankan!\n";
            } catch (\Throwable $e) {
                echo "  [ERROR] Eksekusi {$filename} gagal: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    }
}

// 4. Verify tables count
$tablesStmt = $pdo->query("SHOW TABLES");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

echo "\n[INFO] Total tabel terdaftar di database: " . count($tables) . "\n";
foreach ($tables as $table) {
    echo "  - {$table}\n";
}

echo "\n[DONE] Migrasi database selesai!\n";

