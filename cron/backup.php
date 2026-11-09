<?php

declare(strict_types=1);

// CLI Cron Script: Database & Private Storage Backup Runner
// Performs complete MySQL database dump and uploads storage archive.
// Options:
//   --dry-run       Simulates backup without creating files or pruning old archives.
//   --type=all|db|files  Backup target (default: all).

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Forbidden: This script can only be executed through the CLI.\n";
    exit(1);
}

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;

Config::loadEnv(BASE_PATH . '/.env');

$isDryRun = in_array('--dry-run', $argv, true);
$type = 'all';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--type=')) {
        $type = substr($arg, 7);
    }
}

$lockFile = BASE_PATH . '/storage/cache/backup.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "[SKIP] Process backup is already running.\n";
    exit(0);
}

$backupDir = BASE_PATH . '/storage/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0750, true);
}

// Security: Ensure non-public protection for storage/backups
$htaccess = $backupDir . '/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Deny from all\n");
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting system backup" . ($isDryRun ? " (DRY-RUN MODE)" : "") . " [Type: {$type}]...\n";

    $timestamp = date('Ymd_His');
    $pdo = Database::connect();

    // 1. Database Backup (Native PHP PDO mysqldump-compatible export)
    if (in_array($type, ['all', 'db'], true)) {
        $dbFileName = "db_backup_{$timestamp}.sql";
        $dbFilePath = "{$backupDir}/{$dbFileName}";

        echo "  [INFO] Generating database dump: {$dbFileName}...\n";

        if ($isDryRun) {
            echo "  [DRY-RUN] Database dump simulation passed.\n";
        } else {
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $sqlHandle = fopen($dbFilePath, 'w');
            if (!$sqlHandle) {
                throw new RuntimeException("Cannot create backup SQL file at {$dbFilePath}");
            }

            fwrite($sqlHandle, "-- Kost Management System Database Backup\n");
            fwrite($sqlHandle, "-- Generated at: " . date('Y-m-d H:i:s') . "\n");
            fwrite($sqlHandle, "-- Database: " . Config::get('database.database', 'kost_management') . "\n\n");
            fwrite($sqlHandle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($sqlHandle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
            fwrite($sqlHandle, "SET time_zone = '+07:00';\n\n");

            foreach ($tables as $table) {
                // Table structure
                $createTableStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
                $createSql = $createTableStmt['Create Table'] ?? '';
                fwrite($sqlHandle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($sqlHandle, "{$createSql};\n\n");

                // Table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $columnNames = array_map(fn($col) => "`{$col}`", array_keys($rows[0]));
                    $columnsSql = implode(', ', $columnNames);

                    fwrite($sqlHandle, "INSERT INTO `{$table}` ({$columnsSql}) VALUES\n");
                    $valLines = [];
                    foreach ($rows as $row) {
                        $escapedVals = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $escapedVals[] = 'NULL';
                            } else {
                                $escapedVals[] = $pdo->quote((string)$val);
                            }
                        }
                        $valLines[] = "(" . implode(', ', $escapedVals) . ")";
                    }
                    fwrite($sqlHandle, implode(",\n", $valLines) . ";\n\n");
                }
            }

            fwrite($sqlHandle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($sqlHandle);

            $fileSize = filesize($dbFilePath);
            echo "  [SUCCESS] Database backup created ({$fileSize} bytes).\n";
            Logger::info("Cron backup: Database backup completed: {$dbFileName} ({$fileSize} bytes)");
        }
    }

    // 2. Private Storage Uploads Backup (Zip Archive)
    if (in_array($type, ['all', 'files'], true)) {
        $filesFileName = "storage_uploads_backup_{$timestamp}.zip";
        $filesFilePath = "{$backupDir}/{$filesFileName}";
        $sourceDir = BASE_PATH . '/storage/uploads';

        echo "  [INFO] Archiving storage uploads: {$filesFileName}...\n";

        if ($isDryRun) {
            echo "  [DRY-RUN] Storage uploads backup simulation passed.\n";
        } else {
            if (extension_loaded('zip') && is_dir($sourceDir)) {
                $zip = new ZipArchive();
                if ($zip->open($filesFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    $archivedCount = 0;
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen($sourceDir) + 1);
                            $zip->addFile($filePath, $relativePath);
                            $archivedCount++;
                        }
                    }
                    $zip->close();
                    $zipSize = filesize($filesFilePath);
                    echo "  [SUCCESS] Storage uploads archived ({$archivedCount} files, {$zipSize} bytes).\n";
                    Logger::info("Cron backup: Storage uploads archive completed: {$filesFileName} ({$archivedCount} files)");
                } else {
                    echo "  [WARN] Failed to create ZIP archive for storage uploads.\n";
                }
            } else {
                echo "  [SKIP] Zip extension not available or uploads directory not found.\n";
            }
        }
    }

    // 3. Retention Policy Pruning (Keep backups for N days, default: 30 days)
    $retentionDays = (int)Config::get('app.backup_retention_days', 30);
    $pruneThreshold = time() - ($retentionDays * 86400);

    echo "  [INFO] Checking retention policy (Keep last {$retentionDays} days)...\n";
    $scannedFiles = glob("{$backupDir}/*.*");
    $prunedCount = 0;

    foreach ($scannedFiles as $bf) {
        if (basename($bf) === '.htaccess') {
            continue;
        }
        $mtime = filemtime($bf);
        if ($mtime < $pruneThreshold) {
            if ($isDryRun) {
                echo "  [DRY-RUN] Would prune old backup: " . basename($bf) . "\n";
            } else {
                @unlink($bf);
                $prunedCount++;
            }
        }
    }

    if ($prunedCount > 0) {
        echo "  [INFO] Pruned {$prunedCount} expired backup file(s).\n";
        Logger::info("Cron backup: Pruned {$prunedCount} expired backups older than {$retentionDays} days.");
    }

    echo "[" . date('Y-m-d H:i:s') . "] Backup process completed successfully.\n";
} catch (\Throwable $e) {
    Logger::error("Cron backup failed: " . $e->getMessage());
    echo "[ERROR] Backup failed: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    @unlink($lockFile);
}

