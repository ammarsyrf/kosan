<?php

declare(strict_types=1);

// CLI Cron Script: Payment Reminders Runner
// Checks invoices due dates and dispatches reminder notifications according to schedule (H-7, H-3, H-1, Due Date, H+1).
// Options:
//   --dry-run   Simulates reminder execution without calling external API or creating logs.

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
use App\Repositories\InvoiceRepository;
use App\Services\NotificationService;

Config::loadEnv(BASE_PATH . '/.env');

$isDryRun = in_array('--dry-run', $argv, true);
$lockFile = BASE_PATH . '/storage/cache/payment-reminders.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "[SKIP] Process payment-reminders is already running.\n";
    exit(0);
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting payment reminders check" . ($isDryRun ? " (DRY-RUN MODE)" : "") . "...\n";

    $pdo = Database::connect();
    $invoiceRepo = new InvoiceRepository();
    $notifService = new NotificationService();

    // Check schedule settings
    $settings = $pdo->query("SELECT `key`, `value` FROM `settings` WHERE `group` = 'notification'")->fetchAll(PDO::FETCH_KEY_PAIR);

    $today = date('Y-m-d');
    $todayTime = strtotime($today);

    // Get all unpaid or partially_paid invoices
    $unpaidInvoices = $invoiceRepo->getAll([
        'due_from' => date('Y-m-d', strtotime('-5 days')), // Check up to H+5
        'due_to' => date('Y-m-d', strtotime('+10 days')), // Check up to H-10
    ]);

    $dispatched = 0;

    foreach ($unpaidInvoices as $invoice) {
        if ($invoice->isPaid() || in_array($invoice->status, ['cancelled', 'void'], true)) {
            continue;
        }

        $dueTime = strtotime($invoice->due_date);
        $diffDays = (int)round(($dueTime - $todayTime) / 86400);

        $templateCode = null;

        // Schedule Mapping:
        // diffDays == 7 -> H-7
        // diffDays == 3 -> H-3
        // diffDays == 1 -> H-1
        // diffDays == 0 -> Hari Jatuh Tempo
        // diffDays == -1 -> H+1 (Overdue)
        if ($diffDays === 7 && ($settings['reminder_h7_active'] ?? '1') === '1') {
            $templateCode = 'REMINDER_H7';
        } elseif ($diffDays === 3 && ($settings['reminder_h3_active'] ?? '1') === '1') {
            $templateCode = 'REMINDER_H3';
        } elseif ($diffDays === 1 && ($settings['reminder_h1_active'] ?? '1') === '1') {
            $templateCode = 'REMINDER_H1';
        } elseif ($diffDays === 0 && ($settings['reminder_due_active'] ?? '1') === '1') {
            $templateCode = 'REMINDER_DUE';
        } elseif ($diffDays === -1 && ($settings['reminder_overdue_active'] ?? '1') === '1') {
            $templateCode = 'REMINDER_OVERDUE';
        }

        if (!$templateCode) {
            continue;
        }

        if ($isDryRun) {
            echo "  [DRY-RUN] Would send {$templateCode} for Invoice #{$invoice->invoice_number} (Due: {$invoice->due_date}, Sisa: {$invoice->formattedBalance()}) to {$invoice->tenant_phone}\n";
            $dispatched++;
            continue;
        }

        $res = $notifService->sendInvoiceReminder($invoice, $templateCode, $today);
        if (!$res['skipped']) {
            $dispatched++;
            echo "  [SENT] Dispatched {$templateCode} for Invoice #{$invoice->invoice_number} -> Status: {$res['status']}\n";
        } else {
            echo "  [SKIP] {$res['reason']}\n";
        }
    }

    echo "[INFO] Total reminders processed: {$dispatched}\n";
    if ($dispatched > 0 && !$isDryRun) {
        Logger::info("Cron payment-reminders: Dispatched {$dispatched} WhatsApp reminders on {$today}.");
    }

    echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
} catch (\Throwable $e) {
    Logger::error("Cron payment-reminders failed: " . $e->getMessage());
    echo "[ERROR] " . $e->getMessage() . "\n";
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    @unlink($lockFile);
}
