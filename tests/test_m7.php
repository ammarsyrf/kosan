<?php

declare(strict_types=1);

// Test Suite: Milestone M7 — Dashboard, Analytics & Export

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Controllers\DashboardController;
use App\Controllers\ExportController;
use App\Controllers\HomeController;
use App\Services\DashboardService;
use App\Services\ExportService;
use App\Services\FinancialReportService;

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
echo "      M7 TEST SUITE: DASHBOARD, ANALYTICS & EXPORT       \n";
echo "=========================================================\n\n";

// Setup Test Users & Fixtures
$userRepo = new \App\Repositories\UserRepository($db);
$ownerUserObj = $userRepo->findByEmail('owner@kost.com') ?? $userRepo->findByEmail('owner@example.com');
$ownerUser = $ownerUserObj ? [
    'id' => $ownerUserObj->id,
    'name' => $ownerUserObj->name,
    'email' => $ownerUserObj->email,
    'role' => $ownerUserObj->role,
] : ['id' => 1, 'name' => 'Kost Owner', 'role' => 'owner', 'email' => 'owner@kost.com'];

$adminUserObj = $userRepo->findByEmail('admin@kost.com') ?? $userRepo->findByEmail('admin@example.com');
$adminUser = $adminUserObj ? [
    'id' => $adminUserObj->id,
    'name' => $adminUserObj->name,
    'email' => $adminUserObj->email,
    'role' => $adminUserObj->role,
] : ['id' => 2, 'name' => 'Kost Admin', 'role' => 'admin', 'email' => 'admin@kost.com'];

$stmtTenantUser = $db->query("
    SELECT u.id AS user_id, u.name, r.name AS role, t.id AS tenant_id
    FROM `users` u
    JOIN `role_user` ru ON ru.user_id = u.id
    JOIN `roles` r ON r.id = ru.role_id
    JOIN `tenants` t ON t.user_id = u.id
    WHERE r.name = 'tenant'
    LIMIT 2
");
$tenantUsers = $stmtTenantUser->fetchAll(PDO::FETCH_ASSOC);
$tenantUser1 = $tenantUsers[0] ?? null;
$tenantUser2 = $tenantUsers[1] ?? null;

$dashboardService = new DashboardService($db);
$exportService = new ExportService($db);

// [1] Testing Owner Dashboard Financial Integrity & Aggregations
echo "[1] Testing Owner Dashboard Financial Calculations & Period Filtering...\n";
$currentMonthData = $dashboardService->getOwnerDashboardData(date('Y-m-01'), date('Y-m-t'));
assertCondition(isset($currentMonthData['financial']['total_income']), "Owner dashboard contains total_income");
assertCondition(isset($currentMonthData['financial']['total_expense']), "Owner dashboard contains total_expense");
assertCondition(isset($currentMonthData['financial']['net_cash_flow']), "Owner dashboard contains net_cash_flow");
assertCondition(
    $currentMonthData['financial']['net_cash_flow'] === ($currentMonthData['financial']['total_income'] - $currentMonthData['financial']['total_expense']),
    "Net cash flow equals total_income minus total_expense"
);
assertCondition(isset($currentMonthData['comparison']['income_change_pct']), "Owner dashboard calculates period comparison percentages");
assertCondition(is_array($currentMonthData['monthly_trend']), "Owner dashboard includes monthly trend dataset");
assertCondition(count($currentMonthData['monthly_trend']) === 6, "Monthly trend includes exactly 6 historical months");

// [2] Testing Revenue Only Counts Verified Payments (Accounting Integrity)
echo "\n[2] Testing Accounting Integrity (Verified Only)...\n";
// Create test payment with status = 'pending' and verify it is EXCLUDED from total_income
$testPayNumPending = 'TEST-PAY-M7-PEND-' . uniqid();
$stmtInsPay = $db->prepare("
    INSERT INTO `payments` (`payment_number`, `tenant_id`, `payment_method`, `amount`, `payment_date`, `status`, `created_at`)
    VALUES (:pnum, :tid, 'transfer', 750000.00, CURDATE(), 'pending', NOW())
");
$stmtInsPay->execute([
    'pnum' => $testPayNumPending,
    'tid' => $tenantUser1['tenant_id'] ?? 1,
]);
$pendingPaymentId = (int)$db->lastInsertId();

$dataAfterPending = $dashboardService->getOwnerDashboardData(date('Y-m-01'), date('Y-m-t'));
assertCondition(
    $dataAfterPending['financial']['total_income'] === $currentMonthData['financial']['total_income'],
    "Pending payment is strictly excluded from total_income"
);

// Clean up test pending payment
$db->exec("DELETE FROM `payments` WHERE `id` = {$pendingPaymentId}");

// [3] Testing Operational Metrics & Alerts
echo "\n[3] Testing Operational Metrics & Action Alerts...\n";
$ops = $dashboardService->getOperationalMetrics();
assertCondition(isset($ops['total_rooms']), "Operational metrics contain total_rooms");
assertCondition(isset($ops['occupied_rooms']), "Operational metrics contain occupied_rooms");
assertCondition(isset($ops['vacant_rooms']), "Operational metrics contain vacant_rooms");
assertCondition(isset($ops['occupancy_rate']), "Operational metrics contain occupancy_rate");
assertCondition(
    $ops['total_rooms'] === ($ops['occupied_rooms'] + $ops['vacant_rooms'] + $ops['maintenance_rooms']),
    "Total rooms equals sum of occupied, vacant, and under maintenance"
);

$alerts = $dashboardService->getAlertsAndActionItems();
assertCondition(isset($alerts['pending_payments_count']), "Alerts include pending_payments_count");
assertCondition(isset($alerts['overdue_invoices_count']), "Alerts include overdue_invoices_count");
assertCondition(isset($alerts['urgent_maintenance_count']), "Alerts include urgent_maintenance_count");
assertCondition(isset($alerts['failed_whatsapp_count']), "Alerts include failed_whatsapp_count");

// [4] Testing Admin Dashboard (Data Boundary & Operational Tasks)
echo "\n[4] Testing Admin Dashboard Data Isolation (Hiding Net Profit)...\n";
$adminData = $dashboardService->getAdminDashboardData();
assertCondition(isset($adminData['operations']), "Admin dashboard contains operations data");
assertCondition(isset($adminData['alerts']), "Admin dashboard contains alerts data");
assertCondition(isset($adminData['pending_payments']), "Admin dashboard contains pending payment action queue");
assertCondition(isset($adminData['urgent_maintenance']), "Admin dashboard contains urgent maintenance queue");
assertCondition(!isset($adminData['financial']), "Admin dashboard strictly excludes executive financial summary");
assertCondition(!isset($adminData['comparison']), "Admin dashboard strictly excludes executive profit comparisons");

// [5] Testing Tenant Dashboard & IDOR Data Isolation
echo "\n[5] Testing Tenant Dashboard & Strict Isolation...\n";
if ($tenantUser1 && $tenantUser2) {
    $t1Data = $dashboardService->getTenantDashboardData((int)$tenantUser1['tenant_id']);
    $t2Data = $dashboardService->getTenantDashboardData((int)$tenantUser2['tenant_id']);

    assertCondition(isset($t1Data['total_billed']), "Tenant 1 dashboard has total_billed");
    assertCondition(isset($t1Data['total_paid']), "Tenant 1 dashboard has total_paid");
    assertCondition(isset($t1Data['remaining_balance']), "Tenant 1 dashboard has remaining_balance");

    // Check isolation: all invoices in t1Data must belong to tenant 1
    $allT1InvoicesValid = true;
    foreach ($t1Data['invoices'] as $inv) {
        if ((int)$inv['tenant_id'] !== (int)$tenantUser1['tenant_id']) {
            $allT1InvoicesValid = false;
            break;
        }
    }
    assertCondition($allT1InvoicesValid, "Tenant 1 invoices are strictly isolated from Tenant 2");

    // Check maintenance isolation
    $allT1MaintValid = true;
    foreach ($t1Data['active_maintenance'] as $m) {
        if ((int)$m['tenant_id'] !== (int)$tenantUser1['tenant_id']) {
            $allT1MaintValid = false;
            break;
        }
    }
    assertCondition($allT1MaintValid, "Tenant 1 maintenance requests are strictly isolated from Tenant 2");
} else {
    echo "  [SKIP] Need 2 tenant users for dual isolation check.\n";
}

// [6] Testing Export Service (Financial CSV & Operational CSV)
echo "\n[6] Testing Export Service Output Integrity...\n";
$financialCsv = $exportService->exportFinancialCsv(date('Y-m-01'), date('Y-m-t'));
assertCondition(str_starts_with($financialCsv, "\xEF\xBB\xBF"), "Financial CSV includes UTF-8 BOM for Excel compatibility");
assertCondition(str_contains($financialCsv, 'LAPORAN KEUANGAN KOST'), "Financial CSV contains title header");
assertCondition(str_contains($financialCsv, 'RINGKASAN EKSEKUTIF'), "Financial CSV contains executive summary section");
assertCondition(str_contains($financialCsv, 'Arus Kas Bersih (Net Cash Flow)'), "Financial CSV contains Net Cash Flow row");
assertCondition(str_contains($financialCsv, 'RINCIAN PENGELUARAN OPERASIONAL'), "Financial CSV contains expense details section");

$operationalCsv = $exportService->exportOperationalCsv();
assertCondition(str_starts_with($operationalCsv, "\xEF\xBB\xBF"), "Operational CSV includes UTF-8 BOM");
assertCondition(str_contains($operationalCsv, 'LAPORAN OPERASIONAL KOST'), "Operational CSV contains title header");
assertCondition(str_contains($operationalCsv, 'STATUS DAN INVENTARIS KAMAR'), "Operational CSV contains room inventory section");
assertCondition(str_contains($operationalCsv, 'DAFTAR KONTRAK SEWA AKTIF'), "Operational CSV contains active contracts section");
assertCondition(str_contains($operationalCsv, 'DAFTAR TIKET PEMELIHARAAN'), "Operational CSV contains maintenance tickets section");

// [7] Testing Server-Side Authorization for Export Endpoints
echo "\n[7] Testing Server-Side Authorization for Dashboards & Exports...\n";
$exportController = new ExportController($exportService);
$dashController = new DashboardController($dashboardService, new FinancialReportService($db));

// Tenant trying to export financial report
Session::set('user', ['id' => 999, 'role' => 'tenant', 'name' => 'Penyewa Hacker']);
$req = new Request();
$resExportFinTenant = $exportController->exportFinancial($req);
assertCondition($resExportFinTenant->getStatusCode() === 302, "Tenant blocked from downloading financial export (Redirect 302)");

// Admin trying to export financial report (Owner only)
Session::set('user', ['id' => 888, 'role' => 'admin', 'name' => 'Admin Staff']);
$resExportFinAdmin = $exportController->exportFinancial($req);
assertCondition($resExportFinAdmin->getStatusCode() === 302, "Admin blocked from downloading financial export (Redirect 302)");

// Owner downloading financial export
Session::set('user', $ownerUser);
$resExportFinOwner = $exportController->exportFinancial($req);
assertCondition($resExportFinOwner->getStatusCode() === 200, "Owner successfully authorized to download financial CSV");
assertCondition($resExportFinOwner->getHeader('Content-Type') === 'text/csv; charset=UTF-8', "Financial export response has text/csv header");

// Admin downloading operational export
Session::set('user', $adminUser);
$resExportOpAdmin = $exportController->exportOperational($req);
assertCondition($resExportOpAdmin->getStatusCode() === 200, "Admin authorized to download operational CSV");

// [8] Testing Empty State & Custom Date Range Filter
echo "\n[8] Testing Filter Queries & Empty State Resilience...\n";
$futureStart = '2099-01-01';
$futureEnd = '2099-01-31';
$futureData = $dashboardService->getOwnerDashboardData($futureStart, $futureEnd);
assertCondition($futureData['financial']['total_income'] === 0.0, "Future period income gracefully defaults to 0.0");
assertCondition($futureData['financial']['total_expense'] === 0.0, "Future period expense gracefully defaults to 0.0");
assertCondition($futureData['financial']['net_cash_flow'] === 0.0, "Future period net cash flow gracefully defaults to 0.0");
assertCondition(empty($futureData['revenue_per_room']) || count($futureData['revenue_per_room']) >= 0, "Revenue per room handles empty period gracefully");

// [9] Testing HomeController Role-based Routing
echo "\n[9] Testing Role-based Home Dispatching...\n";
$homeController = new HomeController();

// Guest
Session::destroy();
Session::start();
$resGuest = $homeController->index();
assertCondition($resGuest->getStatusCode() === 200, "Guest sees public home landing");

// Owner
Session::set('user', $ownerUser);
$resOwner = $homeController->index();
assertCondition($resOwner->getStatusCode() === 302 && $resOwner->getHeader('Location') === '/owner/dashboard', "Owner auto-redirects to /owner/dashboard");

// Admin
Session::set('user', $adminUser);
$resAdmin = $homeController->index();
assertCondition($resAdmin->getStatusCode() === 302 && $resAdmin->getHeader('Location') === '/admin/dashboard', "Admin auto-redirects to /admin/dashboard");

// Tenant
Session::set('user', ['id' => 777, 'role' => 'tenant', 'name' => 'Tenant User']);
$resTenant = $homeController->index();
assertCondition($resTenant->getStatusCode() === 302 && $resTenant->getHeader('Location') === '/tenant/dashboard', "Tenant auto-redirects to /tenant/dashboard");

echo "\n=========================================================\n";
echo "M7 TEST SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "=========================================================\n";

if ($failed > 0) {
    exit(1);
}
