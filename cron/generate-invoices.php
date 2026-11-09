<?php

declare(strict_types=1);

// CLI Cron Script: Routine Invoice Generator Runner
// Generates routine monthly invoices based on contract billing anchor days.
// Options:
//   --dry-run   Simulates invoice generation without saving to database.

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
use App\Services\InvoiceService;

Config::loadEnv(BASE_PATH . '/.env');

$isDryRun = in_array('--dry-run', $argv, true);
$lockFile = BASE_PATH . '/storage/cache/generate-invoices.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "[SKIP] Process generate-invoices is already running.\n";
    exit(0);
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting routine invoice generation" . ($isDryRun ? " (DRY-RUN MODE)" : "") . "...\n";

    $pdo = Database::connect();
    $invoiceService = new InvoiceService();

    // Query all active contracts
    $stmt = $pdo->query("
        SELECT id, contract_number, billing_anchor_day, start_date, end_date, rent_price_snapshot
        FROM `rental_contracts`
        WHERE `status` = 'active'
    ");
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $currentYear = (int)date('Y');
    $currentMonth = (int)date('m');
    $generatedCount = 0;

    foreach ($contracts as $contract) {
        if ($isDryRun) {
            echo "  [DRY-RUN] Would process contract #{$contract['contract_number']} (Anchor: {$contract['billing_anchor_day']})\n";
            $generatedCount++;
            continue;
        }

        $invId = $invoiceService->generateRoutineInvoice(
            (int)$contract['id'],
            $currentYear,
            $currentMonth,
            'CronInvoiceGenerator'
        );

        if ($invId) {
            $generatedCount++;
            echo "  [SUCCESS] Generated Invoice ID #{$invId} for Contract #{$contract['contract_number']}\n";
        }
    }

    echo "[INFO] Total routine invoices generated: {$generatedCount}\n";
    if ($generatedCount > 0 && !$isDryRun) {
        Logger::info("Cron generate-invoices: Generated {$generatedCount} routine invoices for {$currentYear}-{$currentMonth}.");
    }

    echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
} catch (\Throwable $e) {
    Logger::error("Cron generate-invoices failed: " . $e->getMessage());
    echo "[ERROR] " . $e->getMessage() . "\n";
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    @unlink($lockFile);
}

