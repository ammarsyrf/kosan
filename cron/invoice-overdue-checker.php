<?php

declare(strict_types=1);

// CLI Cron Script: Invoice Overdue Status Checker
// Automatically marks unpaid or partially paid invoices past their due date as 'overdue'.

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Forbidden: This script can only be executed through the CLI.\n";
    exit(1);
}

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Logger;
use App\Services\InvoiceService;

Config::loadEnv(BASE_PATH . '/.env');

$lockFile = BASE_PATH . '/storage/cache/invoice-overdue-checker.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "[SKIP] Process invoice-overdue-checker is already running.\n";
    exit(0);
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Checking overdue invoices...\n";

    $invoiceService = new InvoiceService();
    $updatedCount = $invoiceService->updateOverdueInvoices();

    echo "[INFO] Total invoices marked as overdue: {$updatedCount}\n";
    if ($updatedCount > 0) {
        Logger::info("Cron invoice-overdue-checker: Marked {$updatedCount} invoices as overdue.");
    }

    echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
} catch (\Throwable $e) {
    Logger::error("Cron invoice-overdue-checker failed: " . $e->getMessage());
    echo "[ERROR] " . $e->getMessage() . "\n";
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    @unlink($lockFile);
}

