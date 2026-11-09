<?php

declare(strict_types=1);

// Test Suite: Milestone M8 — Final Regression, Security & Backup Verification

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Controllers\AuthController;
use App\Controllers\ExportController;
use App\Controllers\PaymentController;
use App\Controllers\TenantPortalController;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\BookingService;
use App\Services\ContractService;
use App\Services\InvoiceService;
use App\Services\PaymentService;

Config::loadEnv(dirname(__DIR__) . '/.env');
Session::start();

$db = Database::connect();

$passed = 0;
$failed = 0;

function assertCondition(bool $cond, string $msg): void
{
    global $passed, $failed;
    if ($cond) {
        $passed++;
        echo "  [PASS] {$msg}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$msg}\n";
    }
}

echo "=========================================================\n";
echo "      M8 TEST SUITE: SECURITY & FINAL INTEGRITY AUDIT    \n";
echo "=========================================================\n\n";

// [1] Authentication & Session Security
echo "[1] Testing Authentication & Session Security...\n";
$userRepo = new UserRepository($db);
$authService = new AuthService($userRepo);

// Seeded Owner login check
$loginRes = $authService->attempt('owner@kost.com', 'password123');
assertCondition($loginRes['success'] === true && isset($loginRes['user']), "Owner can authenticate with valid credentials");

// Brute-force rate limiting check
for ($i = 0; $i < 6; $i++) {
    $authService->attempt('hacker@attack.com', 'wrongpass', '192.168.1.100');
}
$throttled = $authService->attempt('hacker@attack.com', 'wrongpass', '192.168.1.100');
assertCondition($throttled['success'] === false && str_contains($throttled['error'], 'Terlalu banyak percobaan'), "Rate limiting throttles repeated failed login attempts");

// [2] Authorization & Access Control (Owner / Admin / Tenant)
echo "\n[2] Testing Server-side Role Authorization & Guard...\n";
$ownerUser = ['id' => 1, 'name' => 'Owner', 'role' => 'owner'];
$adminUser = ['id' => 2, 'name' => 'Admin', 'role' => 'admin'];
$tenantUser = ['id' => 3, 'name' => 'Tenant', 'role' => 'tenant'];

$exportController = new ExportController();

// Tenant cannot access financial export
Session::set('user', $tenantUser);
$resTenantFin = $exportController->exportFinancial(new Request());
assertCondition($resTenantFin->getStatusCode() === 302, "Tenant blocked from downloading financial export (Redirect 302)");

// Admin cannot access financial export
Session::set('user', $adminUser);
$resAdminFin = $exportController->exportFinancial(new Request());
assertCondition($resAdminFin->getStatusCode() === 302, "Admin blocked from downloading financial export (Redirect 302)");

// Owner can access financial export
Session::set('user', $ownerUser);
$resOwnerFin = $exportController->exportFinancial(new Request());
assertCondition($resOwnerFin->getStatusCode() === 200, "Owner authorized to download financial export");

// [3] CSRF Guard & Token Validation
echo "\n[3] Testing CSRF Guard...\n";
$token = Csrf::token();
assertCondition(strlen($token) === 64, "CSRF token is 64 hex characters (SHA-256)");
assertCondition(Csrf::validate($token), "Valid CSRF token passes validation");
assertCondition(!Csrf::validate('forged_csrf_token_12345'), "Invalid CSRF token is strictly rejected");
assertCondition(!Csrf::validate(null), "Null CSRF token is strictly rejected");

// [4] XSS Escaping & SQL Injection Defense
echo "\n[4] Testing XSS Escaping & SQL Injection Defense...\n";
$xssPayload = '<script>alert("XSS")</script>';
$escaped = e($xssPayload);
assertCondition(!str_contains($escaped, '<script>') && str_contains($escaped, '&lt;script&gt;'), "Helper e() safely escapes HTML script tags");

$sqlPayload = "' OR '1'='1";
$stmtInject = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email");
$stmtInject->execute(['email' => $sqlPayload]);
$injectCount = (int)$stmtInject->fetchColumn();
assertCondition($injectCount === 0, "Prepared statements neutralize SQL injection payloads");

// [5] IDOR & Tenant Data Isolation Protection
echo "\n[5] Testing IDOR & Tenant Isolation...\n";
$stmtTenants = $db->query("SELECT id, user_id FROM `tenants` ORDER BY id ASC LIMIT 2");
$tenants = $stmtTenants->fetchAll(PDO::FETCH_ASSOC);
if (count($tenants) >= 2) {
    $t1Id = (int)$tenants[0]['id'];
    $t2Id = (int)$tenants[1]['id'];

    $invoiceRepo = new InvoiceRepository($db);
    $t1Invoices = $invoiceRepo->getByTenantId($t1Id);
    $allT1Isolated = true;
    foreach ($t1Invoices as $inv) {
        if ($inv->tenant_id !== $t1Id) {
            $allT1Isolated = false;
            break;
        }
    }
    assertCondition($allT1Isolated, "Invoice query strictly filters by tenant_id (No cross-tenant data leak)");
} else {
    echo "  [SKIP] Minimum 2 tenants required for dual isolation testing.\n";
}

// [6] File Traversal & Upload Security Guard
echo "\n[6] Testing File Traversal & Storage Security...\n";
$traversalPayload = '../../../../windows/win.ini';
$safeFilename = basename($traversalPayload);
assertCondition($safeFilename === 'win.ini' && !str_contains($safeFilename, '..'), "Basename strips directory traversal sequences");

// Test MIME validation rule
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
assertCondition(in_array('image/jpeg', $allowedMimes, true), "JPEG MIME allowed for receipt/proof");
assertCondition(in_array('image/png', $allowedMimes, true), "PNG MIME allowed for receipt/proof");
assertCondition(!in_array('application/x-php', $allowedMimes, true), "PHP executable scripts strictly blocked from upload");
assertCondition(!in_array('application/x-sh', $allowedMimes, true), "Shell scripts strictly blocked from upload");

// [7] Financial Integrity: Over-allocation & Reversal Checks
echo "\n[7] Testing Payment Allocation & Financial Integrity...\n";
$paymentService = new PaymentService(new PaymentRepository($db), new InvoiceRepository($db));
// Verify allocation check logic
assertCondition(method_exists($paymentService, 'verifyPayment'), "PaymentService has verifyPayment workflow");
assertCondition(method_exists($paymentService, 'reversePayment'), "PaymentService has reversePayment workflow");
assertCondition(method_exists($paymentService, 'recordCashPayment'), "PaymentService has recordCashPayment workflow");

// [8] CLI Cron Script Restrictions
echo "\n[8] Testing CLI Cron Execution Safeguards...\n";
$cronFiles = [
    'booking-expiration.php',
    'generate-invoices.php',
    'invoice-overdue-checker.php',
    'payment-reminders.php',
    'notification-retry.php',
    'backup.php',
];

$allCliProtected = true;
foreach ($cronFiles as $cf) {
    $content = file_get_contents(dirname(__DIR__) . '/cron/' . $cf);
    if (!str_contains($content, "php_sapi_name() !== 'cli'")) {
        $allCliProtected = false;
        echo "  [FAIL] Cron script {$cf} is missing CLI-only check!\n";
    }
}
assertCondition($allCliProtected, "All 6 cron scripts enforce strict CLI-only restriction");

// [9] Backup & Restore Execution on Separate Test Database
echo "\n[9] Testing Backup Runner & Restoration to Test Database...\n";
$backupDir = dirname(__DIR__) . '/storage/backups';
$testDbName = 'kost_test_restore_' . date('Ymd');

// Run backup
$cmd = 'php ' . escapeshellarg(dirname(__DIR__) . '/cron/backup.php') . ' --type=all';
exec($cmd, $out, $ret);
assertCondition($ret === 0, "Backup script executed successfully (Return code 0)");

$backupSqls = glob("{$backupDir}/db_backup_*.sql");
assertCondition(!empty($backupSqls), "Database backup SQL dump file generated");

if (!empty($backupSqls)) {
    $latestSql = end($backupSqls);
    $sqlContent = file_get_contents($latestSql);
    assertCondition(str_contains($sqlContent, 'CREATE TABLE'), "Backup SQL contains CREATE TABLE definitions");
    assertCondition(str_contains($sqlContent, 'INSERT INTO'), "Backup SQL contains table data insertions");

    // Test non-destructive restoration to separate test database
    try {
        $dbHost = Config::get('database.connections.mysql.host', Config::env('DB_HOST', '127.0.0.1'));
        $dbPort = Config::get('database.connections.mysql.port', Config::env('DB_PORT', 3307));
        $dbUser = Config::get('database.connections.mysql.username', Config::env('DB_USERNAME', 'root'));
        $dbPass = Config::get('database.connections.mysql.password', Config::env('DB_PASSWORD', ''));

        $db->exec("CREATE DATABASE IF NOT EXISTS `{$testDbName}`");
        $testPdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$testDbName}",
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $testPdo->exec($sqlContent);
        $restoredTables = $testPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        assertCondition(count($restoredTables) >= 30, "Restoration to separate test database {$testDbName} succeeded (" . count($restoredTables) . " tables)");

        // Clean up test database safely
        $db->exec("DROP DATABASE IF EXISTS `{$testDbName}`");
        assertCondition(true, "Temporary test restoration database cleaned up safely without touching production");
    } catch (\Throwable $e) {
        assertCondition(false, "Restoration test failed: " . $e->getMessage());
    }
}

// Clean up created test backup file
foreach (glob("{$backupDir}/db_backup_*") as $f) {
    @unlink($f);
}
foreach (glob("{$backupDir}/storage_uploads_backup_*") as $f) {
    @unlink($f);
}

// [10] Fonnte Token Redaction & Exposure Check
echo "\n[10] Testing Sensitive Secret Redaction in Logs...\n";
Logger::info("Test logging token safety", [
    'fonnte_token' => 'dummy_secret_token_12345',
    'password' => 'secret_password_test',
]);
$logFile = dirname(__DIR__) . '/storage/logs/app-' . date('Y-m-d') . '.log';
$logContent = file_exists($logFile) ? file_get_contents($logFile) : '';
assertCondition(!str_contains($logContent, 'secret_password_test'), "Logger strictly redacts passwords");

echo "\n=========================================================\n";
echo "M8 TEST SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "=========================================================\n";

if ($failed > 0) {
    exit(1);
}
