<?php

declare(strict_types=1);

// CLI Cron Script: Notification Retry Runner
// Retries failed/retrying WhatsApp messages that have not reached max retry limit (3).
// Options:
//   --dry-run   Simulates retry execution without calling external API or updating logs.

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
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;

Config::loadEnv(BASE_PATH . '/.env');

$isDryRun = in_array('--dry-run', $argv, true);
$lockFile = BASE_PATH . '/storage/cache/notification-retry.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "[SKIP] Process notification-retry is already running.\n";
    exit(0);
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting notification retry check" . ($isDryRun ? " (DRY-RUN MODE)" : "") . "...\n";

    $notifRepo = new NotificationRepository();
    $notifService = new NotificationService();

    $pendingRetries = $notifRepo->getPendingRetries(3);
    $totalFound = count($pendingRetries);

    echo "[INFO] Found {$totalFound} failed/retrying notification(s) eligible for retry.\n";

    if ($isDryRun) {
        foreach ($pendingRetries as $log) {
            echo "  [DRY-RUN] Would retry Log #{$log->id} to {$log->phone} (Current retry count: {$log->retry_count}/3)\n";
        }
        echo "[INFO] Dry run finished.\n";
    } else {
        $successCount = $notifService->processAutomaticRetries(3);
        $failedCount = $totalFound - $successCount;

        echo "[INFO] Retry execution finished. Succeeded: {$successCount}, Failed/Remaining: {$failedCount}\n";
        if ($totalFound > 0) {
            Logger::info("Cron notification-retry: Processed {$totalFound} items (Success: {$successCount}, Failed: {$failedCount}).");
        }
    }
} catch (Throwable $e) {
    echo "[ERROR] Notification retry execution failed: " . $e->getMessage() . "\n";
    Logger::error("Cron notification-retry exception: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
}
