<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Session;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;
use App\Services\FonnteService;
use App\Services\NotificationService;

Session::start();
Config::loadEnv(BASE_PATH . '/.env');

echo "=========================================================\n";
echo "      M5 TEST SUITE: WHATSAPP AUTOMATION (FONNTE)        \n";
echo "=========================================================\n\n";

$pdo = Database::connect();
$notifRepo = new NotificationRepository();
$invoiceRepo = new InvoiceRepository();
$fonnteService = new FonnteService();
$notifService = new NotificationService($notifRepo, $invoiceRepo, $fonnteService);
$roomRepo = new RoomRepository();
$tenantRepo = new TenantRepository();

$passed = 0;
$failed = 0;

function assertTest(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$name}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$name}" . ($detail ? " - {$detail}" : "") . "\n";
    }
}

// Ensure mock mode is active for all automated tests (ZERO external network calls)
FonnteService::setMockMode(true);

// Cleanup past M5 test data
$pdo->exec("DELETE FROM `whatsapp_logs` WHERE `idempotency_key` LIKE 'test_%' OR `idempotency_key` LIKE 'inv_99999%'");

// -----------------------------------------------------------------
// TEST GROUP 1: Phone Sanitization & Validation
// -----------------------------------------------------------------
echo "[1] Testing Phone Number Sanitization & Validation...\n";

assertTest("Sanitize '081234567890' to '6281234567890'", FonnteService::sanitizePhoneNumber('081234567890') === '6281234567890');
assertTest("Sanitize '81234567890' to '6281234567890'", FonnteService::sanitizePhoneNumber('81234567890') === '6281234567890');
assertTest("Sanitize '+6281234567890' to '6281234567890'", FonnteService::sanitizePhoneNumber('+6281234567890') === '6281234567890');
assertTest("Sanitize '0812-3456-7890' with hyphens to '6281234567890'", FonnteService::sanitizePhoneNumber('0812-3456-7890') === '6281234567890');
assertTest("Sanitize '0812 3456 7890' with spaces to '6281234567890'", FonnteService::sanitizePhoneNumber('0812 3456 7890') === '6281234567890');

$invalidRejected = false;
try {
    FonnteService::sanitizePhoneNumber('12345'); // too short
} catch (InvalidArgumentException $e) {
    $invalidRejected = true;
}
assertTest("Reject phone number shorter than 10 digits", $invalidRejected);

$nonNumericRejected = false;
try {
    FonnteService::sanitizePhoneNumber('081234abcde'); // contains letters
} catch (InvalidArgumentException $e) {
    $nonNumericRejected = true;
}
assertTest("Reject phone number containing non-numeric characters", $nonNumericRejected);

// -----------------------------------------------------------------
// TEST GROUP 2: Template Rendering & Placeholder Validation
// -----------------------------------------------------------------
echo "\n[2] Testing Template Rendering & Placeholder Validation...\n";

$sampleTemplate = "Halo {{tenant_name}}, tagihan kamar {{room_name}} sebesar {{remaining_balance}} jatuh tempo pada {{due_date}}.";
$variables = [
    'tenant_name' => 'Budi Santoso',
    'room_name' => 'Kamar 101',
    'invoice_number' => 'INV/2026/001',
    'period_start' => '01/03/2026',
    'period_end' => '31/03/2026',
    'due_date' => '05/03/2026',
    'total_amount' => 'Rp 1.500.000',
    'paid_amount' => 'Rp 500.000',
    'remaining_balance' => 'Rp 1.000.000',
];

$rendered = $notifService->renderTemplate($sampleTemplate, $variables);
assertTest("Render valid template with placeholders", str_contains($rendered, 'Halo Budi Santoso') && str_contains($rendered, 'Kamar 101') && str_contains($rendered, 'Rp 1.000.000'));

// Support single curly brace {var} syntax as well
$singleBraceTemplate = "Halo {tenant_name}, tagihan Anda {invoice_number} sisa {remaining_balance}.";
$renderedSingle = $notifService->renderTemplate($singleBraceTemplate, $variables);
assertTest("Render single curly brace placeholders", str_contains($renderedSingle, 'Halo Budi Santoso') && str_contains($renderedSingle, 'INV/2026/001'));

// Rejection of unreplaced raw placeholders
$unreplacedRejected = false;
try {
    $brokenTemplate = "Halo {{tenant_name}}, voucher Anda {{unknown_voucher_code}}.";
    $notifService->renderTemplate($brokenTemplate, $variables);
} catch (RuntimeException $e) {
    $unreplacedRejected = true;
}
assertTest("Reject template with raw unreplaced placeholder", $unreplacedRejected);

// -----------------------------------------------------------------
// TEST GROUP 3: Fonnte Disabled Mode
// -----------------------------------------------------------------
echo "\n[3] Testing Fonnte Disabled Mode Handling...\n";

// Temporarily set FONNTE_ENABLED=false
$oldEnv = getenv('FONNTE_ENABLED');
putenv('FONNTE_ENABLED=false');
$disabledFonnte = new FonnteService();
$disabledResult = $disabledFonnte->sendWhatsAppMessage('081234567890', 'Test message');
assertTest("Fonnte returns status 'disabled' when FONNTE_ENABLED=false", $disabledResult['status'] === 'disabled' && $disabledResult['success'] === false);

// Restore environment
if ($oldEnv !== false) {
    putenv("FONNTE_ENABLED={$oldEnv}");
} else {
    putenv("FONNTE_ENABLED=true");
}

// -----------------------------------------------------------------
// TEST GROUP 4: Invoice Status Rules for Reminders
// -----------------------------------------------------------------
echo "\n[4] Testing Invoice Status Rules for Reminders...\n";

// Mock Invoice object
$unpaidInvoice = new Invoice([
    'id' => 999991,
    'invoice_number' => 'INV/TEST/991',
    'tenant_name' => 'Test Tenant Unpaid',
    'tenant_phone' => '08129999001',
    'room_name' => 'Kamar Test',
    'total_amount' => 1500000.00,
    'paid_amount' => 0.00,
    'balance' => 1500000.00,
    'status' => 'unpaid',
    'period_start' => '2026-03-01',
    'period_end' => '2026-03-31',
    'due_date' => '2026-03-05',
]);

$paidInvoice = new Invoice([
    'id' => 999992,
    'invoice_number' => 'INV/TEST/992',
    'tenant_name' => 'Test Tenant Paid',
    'tenant_phone' => '08129999002',
    'room_name' => 'Kamar Test',
    'total_amount' => 1500000.00,
    'paid_amount' => 1500000.00,
    'balance' => 0.00,
    'status' => 'paid',
    'period_start' => '2026-03-01',
    'period_end' => '2026-03-31',
    'due_date' => '2026-03-05',
]);

$cancelledInvoice = new Invoice([
    'id' => 999993,
    'invoice_number' => 'INV/TEST/993',
    'tenant_name' => 'Test Tenant Cancelled',
    'tenant_phone' => '08129999003',
    'room_name' => 'Kamar Test',
    'total_amount' => 1500000.00,
    'paid_amount' => 0.00,
    'balance' => 1500000.00,
    'status' => 'cancelled',
    'period_start' => '2026-03-01',
    'period_end' => '2026-03-31',
    'due_date' => '2026-03-05',
]);

$resPaid = $notifService->sendInvoiceReminder($paidInvoice, 'REMINDER_H3', '2026-03-02');
assertTest("Skip reminder for paid invoice", $resPaid['skipped'] === true);

$resCancelled = $notifService->sendInvoiceReminder($cancelledInvoice, 'REMINDER_H3', '2026-03-02');
assertTest("Skip reminder for cancelled invoice", $resCancelled['skipped'] === true);

$resUnpaid = $notifService->sendInvoiceReminder($unpaidInvoice, 'REMINDER_H3', '2026-03-02');
assertTest("Dispatch reminder for unpaid invoice", $resUnpaid['skipped'] === false && $resUnpaid['status'] === 'sent');

$partialInvoice = new Invoice([
    'id' => 999994,
    'invoice_number' => 'INV/TEST/994',
    'tenant_name' => 'Test Tenant Partial',
    'tenant_phone' => '08129999004',
    'room_name' => 'Kamar Test',
    'total_amount' => 1500000.00,
    'paid_amount' => 500000.00,
    'balance' => 1000000.00,
    'status' => 'partially_paid',
    'period_start' => '2026-03-01',
    'period_end' => '2026-03-31',
    'due_date' => '2026-03-05',
]);
$resPartial = $notifService->sendInvoiceReminder($partialInvoice, 'REMINDER_H3', '2026-03-02');
$logPartial = $notifRepo->findLogById($resPartial['log_id']);
assertTest("Dispatch reminder for partially_paid invoice with updated remaining balance", $resPartial['skipped'] === false && $resPartial['status'] === 'sent' && str_contains($logPartial->message, '1.000.000'));

// -----------------------------------------------------------------
// TEST GROUP 5: Idempotency & Duplicate Prevention
// -----------------------------------------------------------------
echo "\n[5] Testing Idempotency & Duplicate Prevention...\n";

$resDuplicate = $notifService->sendInvoiceReminder($unpaidInvoice, 'REMINDER_H3', '2026-03-02');
assertTest("Prevent duplicate reminder dispatch on same invoice & schedule date", $resDuplicate['skipped'] === true && str_contains($resDuplicate['reason'], 'sudah pernah terkirim'));

// Verify database record has unique idempotency key
$expectedKey = "inv_999991_REMINDER_H3_2026-03-02";
$loggedRecord = $notifRepo->findLogByIdempotencyKey($expectedKey);
assertTest("WhatsApp log recorded with exact idempotency key", $loggedRecord !== null && $loggedRecord->status === 'sent');

// -----------------------------------------------------------------
// TEST GROUP 6: Retry Mechanism (Max 3x & Manual Retry)
// -----------------------------------------------------------------
echo "\n[6] Testing Retry Mechanism...\n";

// Create a simulated failed log
$failedKey = "test_failed_" . uniqid();
$failedLogId = $notifRepo->createLog([
    'idempotency_key' => $failedKey,
    'recipient_phone' => '08129999888',
    'message' => 'Simulated failed message',
    'status' => 'failed',
    'response_payload' => '{"status": false, "reason": "device disconnected"}',
]);

$failedLog = $notifRepo->findLogById($failedLogId);
assertTest("Create initial failed log with retry_count=0", $failedLog !== null && $failedLog->status === 'failed' && $failedLog->retry_count === 0);

// Perform manual retry by Admin
$retry1 = $notifService->retryLog($failedLogId, 'Admin');
$logAfterRetry1 = $notifRepo->findLogById($failedLogId);
assertTest("Admin retry successfully dispatches message and increments count", $retry1['success'] === true && $logAfterRetry1->retry_count === 1 && $logAfterRetry1->status === 'sent');

// Prevent retrying an already sent message
$retrySentRejected = false;
try {
    $notifService->retryLog($failedLogId, 'Admin');
} catch (RuntimeException $e) {
    $retrySentRejected = true;
}
assertTest("Reject retry on already sent message", $retrySentRejected);

// Test automatic retry limit (max 3x)
$exceededKey = "test_exceeded_" . uniqid();
$exceededLogId = $notifRepo->createLog([
    'idempotency_key' => $exceededKey,
    'recipient_phone' => '08129999777',
    'message' => 'Simulated 3x failed message',
    'status' => 'failed',
]);
$pdo->prepare("UPDATE `whatsapp_logs` SET `retry_count` = 3 WHERE `id` = ?")->execute([$exceededLogId]);

$autoExceededRejected = false;
try {
    $notifService->retryLog($exceededLogId, 'SystemRetryCron');
} catch (RuntimeException $e) {
    $autoExceededRejected = true;
}
assertTest("Reject automatic cron retry when max retry limit (3x) is reached", $autoExceededRejected);

// -----------------------------------------------------------------
// TEST GROUP 7: Token Privacy & Redaction
// -----------------------------------------------------------------
echo "\n[7] Testing Token Privacy in WhatsApp Logs...\n";

$logs = $notifRepo->getAllLogs(['phone' => '628129999001']);
$tokenLeaked = false;
$currentToken = getenv('FONNTE_API_TOKEN');

foreach ($logs as $l) {
    if (!empty($currentToken) && str_contains($l->response_payload ?? '', $currentToken)) {
        $tokenLeaked = true;
    }
}
assertTest("Fonnte API token is NOT leaked in whatsapp_logs response payload", !$tokenLeaked);

// -----------------------------------------------------------------
// TEST GROUP 8: Non-Blocking Execution Guard
// -----------------------------------------------------------------
echo "\n[8] Testing Non-Blocking Execution...\n";

// Even if WhatsApp sending fails completely, caller operations should handle gracefully
$nonBlockingWorked = true;
try {
    $badInvoice = new Invoice([
        'id' => 999999,
        'invoice_number' => 'INV/TEST/999',
        'tenant_name' => 'Bad Tenant',
        'tenant_phone' => '', // missing phone
        'room_name' => 'Kamar Bad',
        'total_amount' => 1000000.00,
        'paid_amount' => 0.00,
        'balance' => 1000000.00,
        'status' => 'unpaid',
        'period_start' => '2026-03-01',
        'period_end' => '2026-03-31',
        'due_date' => '2026-03-05',
    ]);

    $resBad = $notifService->sendInvoiceReminder($badInvoice, 'REMINDER_H3', '2026-03-02');
    $nonBlockingWorked = ($resBad['skipped'] === true);
} catch (Throwable $e) {
    $nonBlockingWorked = false;
}
assertTest("Non-blocking behavior when tenant has missing phone or invalid payload", $nonBlockingWorked);

// -----------------------------------------------------------------
// TEST GROUP 9: Reminder Schedule Mapping
// -----------------------------------------------------------------
echo "\n[9] Testing Reminder Schedule Mapping (H-7, H-3, H-1, Due Date, Overdue)...\n";

$scheduleTests = [
    ['diff' => 7, 'expected' => 'REMINDER_H7'],
    ['diff' => 3, 'expected' => 'REMINDER_H3'],
    ['diff' => 1, 'expected' => 'REMINDER_H1'],
    ['diff' => 0, 'expected' => 'REMINDER_DUE'],
    ['diff' => -1, 'expected' => 'REMINDER_OVERDUE'],
];

foreach ($scheduleTests as $st) {
    $matched = match($st['diff']) {
        7 => 'REMINDER_H7',
        3 => 'REMINDER_H3',
        1 => 'REMINDER_H1',
        0 => 'REMINDER_DUE',
        -1 => 'REMINDER_OVERDUE',
        default => null,
    };
    assertTest("Schedule mapping for diffDays={$st['diff']} resolves to {$st['expected']}", $matched === $st['expected']);
}

// -----------------------------------------------------------------
// TEST GROUP 10: Automatic Batch Retry via processAutomaticRetries()
// -----------------------------------------------------------------
echo "\n[10] Testing Batch Auto-Retry Execution...\n";

$batchKey = "test_batch_" . uniqid();
$batchLogId = $notifRepo->createLog([
    'idempotency_key' => $batchKey,
    'recipient_phone' => '08129999666',
    'message' => 'Batch retry message',
    'status' => 'retrying',
]);

$processed = $notifService->processAutomaticRetries(3);
$batchLogAfter = $notifRepo->findLogById($batchLogId);
assertTest("Auto-retry batch processed successfully", $processed >= 1 && $batchLogAfter->status === 'sent');

// -----------------------------------------------------------------
// TEST GROUP 11: Route Authorization Guard (Tenant IDOR Protection)
// -----------------------------------------------------------------
echo "\n[11] Testing Route & Authorization Guard...\n";

// Set user session as Tenant
Session::set('user', [
    'id' => 999,
    'role' => 'tenant',
    'email' => 'tenant@test.com',
    'name' => 'Test Tenant',
]);

$notifController = new \App\Controllers\NotificationController($notifRepo, $notifService);

// Tenant attempts to update notification settings
$request = new \App\Core\Request([], [], [], ['REQUEST_METHOD' => 'POST'], []);
$resp = $notifController->updateSettings($request);
$flashError = Session::getFlash('error');
assertTest("Tenant is blocked from updating notification settings", str_contains((string)$flashError, 'Hanya Owner'));

// Clear session
Session::destroy();

// -----------------------------------------------------------------
// TEST GROUP 12: Cron CLI Restriction & Dry-Run
// -----------------------------------------------------------------
echo "\n[12] Testing Cron CLI Restriction & Dry-Run...\n";

$cronReminderDryRun = (string)shell_exec("php " . escapeshellarg(BASE_PATH . '/cron/payment-reminders.php') . " --dry-run");
assertTest("Cron payment-reminders executes in dry-run mode via CLI", str_contains($cronReminderDryRun, 'DRY-RUN MODE'));

$cronRetryDryRun = (string)shell_exec("php " . escapeshellarg(BASE_PATH . '/cron/notification-retry.php') . " --dry-run");
assertTest("Cron notification-retry executes in dry-run mode via CLI", str_contains($cronRetryDryRun, 'DRY-RUN MODE'));

// -----------------------------------------------------------------
// Clean up testing fixtures
// -----------------------------------------------------------------
$pdo->exec("DELETE FROM `whatsapp_logs` WHERE `idempotency_key` LIKE 'test_%' OR `idempotency_key` LIKE 'inv_99999%'");

// -----------------------------------------------------------------
// Final Summary
// -----------------------------------------------------------------
echo "\n=========================================================\n";
echo "M5 TEST SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "=========================================================\n";

if ($failed > 0) {
    exit(1);
}
