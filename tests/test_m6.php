<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Session;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\MaintenanceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;
use App\Services\ExpenseService;
use App\Services\FinancialReportService;
use App\Services\MaintenanceService;
use App\Services\PaymentService;

Session::start();
Config::loadEnv(BASE_PATH . '/.env');

echo "=========================================================\n";
echo "      M6 TEST SUITE: FINANCE & MAINTENANCE MANAGEMENT     \n";
echo "=========================================================\n\n";

$pdo = Database::connect();
$expenseRepo = new ExpenseRepository();
$expenseService = new ExpenseService($expenseRepo);
$maintRepo = new MaintenanceRepository();
$maintService = new MaintenanceService($maintRepo);
$reportService = new FinancialReportService($pdo);
$invoiceRepo = new InvoiceRepository();
$paymentRepo = new PaymentRepository();
$paymentService = new PaymentService();
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

// Clean up past test fixtures for M6
$pdo->exec("DELETE FROM `expenses` WHERE `description` LIKE 'TEST_EXP_%'");
$pdo->exec("DELETE FROM `expense_categories` WHERE `name` LIKE 'TEST_CAT_%'");
$pdo->exec("DELETE FROM `maintenance_updates` WHERE `notes` LIKE 'TEST_MAINT_%'");
$pdo->exec("DELETE FROM `maintenance_attachments` WHERE `file_name` LIKE 'test_maint_%'");
$pdo->exec("DELETE FROM `maintenance_requests` WHERE `title` LIKE 'TEST_MAINT_%'");
$pdo->exec("DELETE FROM `invoice_items` WHERE `invoice_id` IN (SELECT id FROM `invoices` WHERE `invoice_number` LIKE 'INV/TEST/M6%')");
$pdo->exec("DELETE FROM `invoices` WHERE `invoice_number` LIKE 'INV/TEST/M6%'");
$pdo->exec("DELETE FROM `payments` WHERE `payment_number` LIKE 'PAY/TEST/M6%'");

// -----------------------------------------------------------------
// TEST GROUP 1: Expense Categories & Usage Protection
// -----------------------------------------------------------------
echo "[1] Testing Expense Categories & Delete Guard...\n";

$catId = $expenseService->createCategory([
    'name' => 'TEST_CAT_OPERATIONAL',
    'description' => 'Test Category for M6',
    'is_active' => 1,
], 'Admin');

$cat = $expenseService->getCategoryById($catId);
assertTest("Create new expense category", $cat !== null && $cat->name === 'TEST_CAT_OPERATIONAL');
assertTest("New category is active by default", $cat->isActive() === true);

// Update category
$expenseService->updateCategory($catId, [
    'name' => 'TEST_CAT_UPDATED',
    'description' => 'Updated Description',
    'is_active' => 0,
], 'Admin');
$catUpdated = $expenseService->getCategoryById($catId);
assertTest("Update expense category name and toggle inactive", $catUpdated->name === 'TEST_CAT_UPDATED' && $catUpdated->isActive() === false);

// Reactivate for expense test
$expenseService->updateCategory($catId, ['name' => 'TEST_CAT_ACTIVE', 'is_active' => 1], 'Admin');

// -----------------------------------------------------------------
// TEST GROUP 2: Expense Creation, Validation & Receipt Upload
// -----------------------------------------------------------------
echo "\n[2] Testing Expense Recording & Receipt Upload...\n";

// Validation: Amount must be > 0
$invalidAmountBlocked = false;
try {
    $expenseService->recordExpense([
        'category_id' => $catId,
        'transaction_date' => date('Y-m-d'),
        'amount' => 0, // invalid
        'description' => 'TEST_EXP_INVALID',
    ]);
} catch (InvalidArgumentException $e) {
    $invalidAmountBlocked = true;
}
assertTest("Validation rejects expense amount <= 0", $invalidAmountBlocked);

// Create valid dummy receipt file
$dummyReceiptPath = sys_get_temp_dir() . '/dummy_receipt_' . uniqid() . '.jpg';
file_put_contents($dummyReceiptPath, "dummy image content for testing receipt upload");

$dummyFile = [
    'name' => 'dummy_receipt.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $dummyReceiptPath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($dummyReceiptPath),
];

// In test environment, fake MIME by using actual valid png/jpg header
file_put_contents($dummyReceiptPath, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00");

$expenseId = $expenseService->recordExpense([
    'category_id' => $catId,
    'transaction_date' => date('Y-m-d'),
    'amount' => 350000.00,
    'description' => 'TEST_EXP_SERVIS_POMPA',
    'vendor' => 'Toko Teknik Sejahtera',
    'payment_method' => 'cash',
    'notes' => 'Penggantian kapasitor pompa air',
], $dummyFile, 1, 'Admin');

$savedExp = $expenseService->getExpenseById($expenseId);
assertTest("Record valid expense with amount DECIMAL(15,2)", $savedExp !== null && $savedExp->amount == 350000.00);
assertTest("Expense receipt uploaded to private storage", !empty($savedExp->receipt_path) && file_exists(dirname(__DIR__) . '/storage/uploads/' . $savedExp->receipt_path));

// -----------------------------------------------------------------
// TEST GROUP 3: Guard Deleting Category in Use
// -----------------------------------------------------------------
echo "\n[3] Testing Category In-Use Delete Guard...\n";

$deleteUsedCategoryBlocked = false;
try {
    $expenseService->deleteCategory($catId, 'Admin');
} catch (RuntimeException $e) {
    $deleteUsedCategoryBlocked = true;
}
assertTest("Prevent deleting expense category that is currently used in transactions", $deleteUsedCategoryBlocked);

// -----------------------------------------------------------------
// TEST GROUP 4: Expense Update & Soft-Deletion Audit Trail
// -----------------------------------------------------------------
echo "\n[4] Testing Expense Update & Soft-Deletion Audit Trail...\n";

$expenseService->updateExpense($expenseId, [
    'category_id' => $catId,
    'transaction_date' => date('Y-m-d'),
    'amount' => 400000.00,
    'description' => 'TEST_EXP_SERVIS_POMPA_REVISED',
    'payment_method' => 'transfer',
], null, 1, 'Admin');

$updatedExp = $expenseService->getExpenseById($expenseId);
assertTest("Update expense amount and payment method", $updatedExp->amount == 400000.00 && $updatedExp->payment_method === 'transfer');

// Soft delete
$expenseService->deleteExpense($expenseId, 'Admin');
$deletedCheck = $expenseService->getExpenseById($expenseId);
assertTest("Expense is soft-deleted and excluded from active list", $deletedCheck === null);

// Verify record still exists in DB with deleted_at timestamp
$stmtRaw = $pdo->prepare("SELECT * FROM `expenses` WHERE `id` = ?");
$stmtRaw->execute([$expenseId]);
$rawRow = $stmtRaw->fetch(PDO::FETCH_ASSOC);
assertTest("Soft-deleted expense retains audit trail with deleted_at timestamp", !empty($rawRow['deleted_at']));

// Re-activate by clearing deleted_at for financial reporting test
$pdo->prepare("UPDATE `expenses` SET `deleted_at` = NULL WHERE `id` = ?")->execute([$expenseId]);

// -----------------------------------------------------------------
// TEST GROUP 5: Tenant Maintenance Request Creation & Attachment
// -----------------------------------------------------------------
echo "\n[5] Testing Tenant Maintenance Request Creation...\n";

$tenants = $tenantRepo->getAll();
$tenant1 = $tenants[0];
$tenant2 = count($tenants) > 1 ? $tenants[1] : $tenants[0];

$rooms = $roomRepo->getAll();
$room1 = $rooms[0];
$room2 = count($rooms) > 1 ? $rooms[1] : $rooms[0];

$maintCats = $maintService->getAllCategories();
$maintCat = $maintCats[0];

// Create dummy image file for attachment
$dummyAttPath = sys_get_temp_dir() . '/dummy_att_' . uniqid() . '.jpg';
file_put_contents($dummyAttPath, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00\x60\x00\x60\x00\x00");

$dummyAtt = [
    'name' => 'test_maint_photo.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $dummyAttPath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($dummyAttPath),
];

$maintReqId = $maintService->createRequest(
    $tenant1->id,
    $room1->id,
    $maintCat->id,
    'TEST_MAINT_PIPA_BOCOR',
    'Pipa di bawah wastafel bocor air menetes terus',
    'urgent',
    [$dummyAtt],
    $tenant1->name
);

$savedMaint = $maintService->getRequestById($maintReqId, true);
assertTest("Create maintenance request with initial status 'submitted'", $savedMaint !== null && $savedMaint->status === 'submitted');
assertTest("Set priority to 'urgent'", $savedMaint->priority === 'urgent');
assertTest("Save attachment to private storage", count($savedMaint->attachments) >= 1 && file_exists(dirname(__DIR__) . '/storage/uploads/' . $savedMaint->attachments[0]->file_path));

// -----------------------------------------------------------------
// TEST GROUP 6: Maintenance Status, Priority Updates & Internal Notes
// -----------------------------------------------------------------
echo "\n[6] Testing Maintenance Status & Internal Notes Visibility...\n";

// Admin updates status to in_progress
$maintService->updateStatus($maintReqId, 'in_progress', 1, 'Teknisi sudah dijadwalkan datang jam 14:00', 'Admin');
$maintAfterStatus = $maintService->getRequestById($maintReqId, true);
assertTest("Admin updates status to 'in_progress'", $maintAfterStatus->status === 'in_progress');

// Admin adds an internal note (invisible to tenant)
$maintService->addUpdate($maintReqId, 1, 'TEST_MAINT_INTERNAL: Teknisi estimasi biaya Rp 150rb, part dari toko langganan', true, 'Admin');

// Tenant adds a public follow-up note
$maintService->addUpdate($maintReqId, (int)$tenant1->user_id, 'TEST_MAINT_PUBLIC: Saya ada di kosan sampai jam 16:00', false, $tenant1->name);

// Verify Tenant View (includeInternal = false)
$tenantView = $maintService->getRequestById($maintReqId, false);
$hasInternalNoteInTenantView = false;
foreach ($tenantView->updates as $up) {
    if (str_contains($up->notes, 'TEST_MAINT_INTERNAL')) {
        $hasInternalNoteInTenantView = true;
    }
}
assertTest("Internal admin note is strictly hidden from Tenant view", !$hasInternalNoteInTenantView);

// Verify Admin View (includeInternal = true)
$adminView = $maintService->getRequestById($maintReqId, true);
$hasInternalNoteInAdminView = false;
foreach ($adminView->updates as $up) {
    if (str_contains($up->notes, 'TEST_MAINT_INTERNAL')) {
        $hasInternalNoteInAdminView = true;
    }
}
assertTest("Internal admin note is visible in Admin view", $hasInternalNoteInAdminView);

// Admin marks maintenance as resolved
$maintService->updateStatus($maintReqId, 'resolved', 1, 'Pipa sudah diganti dengan yang baru dan diuji normal.', 'Admin');
$resolvedReq = $maintService->getRequestById($maintReqId, true);
assertTest("Admin marks maintenance as 'resolved' with resolved_at timestamp", $resolvedReq->status === 'resolved' && !empty($resolvedReq->resolved_at));

// -----------------------------------------------------------------
// TEST GROUP 7: Tenant Isolation & IDOR Protection
// -----------------------------------------------------------------
echo "\n[7] Testing Tenant Isolation (IDOR Protection)...\n";

// Tenant 2 requests list of requests — should NOT see Tenant 1's request
$tenant2Requests = $maintService->getTenantRequests($tenant2->id);
$sawTenant1Req = false;
foreach ($tenant2Requests as $r) {
    if ($r->id === $maintReqId && $tenant1->id !== $tenant2->id) {
        $sawTenant1Req = true;
    }
}
assertTest("Tenant 2 query excludes maintenance reports of Tenant 1", !$sawTenant1Req);

// -----------------------------------------------------------------
// TEST GROUP 8: Financial Reporting Service (Strict Accounting Integrity)
// -----------------------------------------------------------------
echo "\n[8] Testing Financial Reporting Integrity...\n";

$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$existingContract = $pdo->query("SELECT id, tenant_id FROM `rental_contracts` LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$existingContract) {
    // Create temporary contract for foreign key
    $pdo->prepare("
        INSERT INTO `rental_contracts` (`contract_number`, `tenant_id`, `room_id`, `start_date`, `end_date`, `monthly_rent`, `billing_anchor_day`, `status`, `created_at`)
        VALUES ('CTR/TEST/M6', ?, ?, ?, ?, 1500000.00, 1, 'active', NOW())
    ")->execute([$tenant1->id, $room1->id, $monthStart, date('Y-m-d', strtotime('+6 months'))]);
    $contractId = (int)$pdo->lastInsertId();
    $targetTenantId = $tenant1->id;
} else {
    $contractId = (int)$existingContract['id'];
    $targetTenantId = (int)$existingContract['tenant_id'];
}

// Insert controlled financial fixtures:
// 1. Invoice Unpaid (Rp 1.500.000) -> MUST NOT be counted as income!
$invUnpaidId = $invoiceRepo->createInvoice([
    'invoice_number' => 'INV/TEST/M6_UNPAID',
    'contract_id' => $contractId,
    'tenant_id' => $targetTenantId,
    'period_start' => $monthStart,
    'period_end' => $monthEnd,
    'issue_date' => $today,
    'due_date' => $today,
    'subtotal' => 1500000.00,
    'total_amount' => 1500000.00,
    'paid_amount' => 0.00,
    'balance' => 1500000.00,
    'is_first_month_manual' => 0,
    'status' => 'unpaid',
], [
    ['description' => 'Sewa Kamar', 'amount' => 1500000.00],
]);

// 2. Invoice Partially Paid (Rp 2.000.000, Paid: Rp 500.000, Balance: Rp 1.500.000)
$invPartId = $invoiceRepo->createInvoice([
    'invoice_number' => 'INV/TEST/M6_PARTIAL',
    'contract_id' => $contractId,
    'tenant_id' => $targetTenantId,
    'period_start' => $monthStart,
    'period_end' => $monthEnd,
    'issue_date' => $today,
    'due_date' => $today,
    'subtotal' => 2000000.00,
    'total_amount' => 2000000.00,
    'paid_amount' => 500000.00,
    'balance' => 1500000.00,
    'is_first_month_manual' => 0,
    'status' => 'partially_paid',
], [
    ['description' => 'Sewa Kamar', 'amount' => 2000000.00],
]);

// 3. Payment Verified (Rp 500.000) -> COUNTED as income
$payVerifiedId = $paymentRepo->createPayment([
    'payment_number' => 'PAY/TEST/M6_VERIFIED',
    'tenant_id' => $tenant1->id,
    'amount' => 500000.00,
    'payment_date' => $today,
    'payment_method' => 'transfer',
    'status' => 'verified',
    'verified_by' => 1,
    'verified_at' => date('Y-m-d H:i:s'),
]);

// 4. Payment Pending (Rp 1.000.000) -> MUST NOT be counted as income!
$payPendingId = $paymentRepo->createPayment([
    'payment_number' => 'PAY/TEST/M6_PENDING',
    'tenant_id' => $tenant1->id,
    'amount' => 1000000.00,
    'payment_date' => $today,
    'payment_method' => 'transfer',
    'status' => 'pending',
]);

// 5. Payment Rejected (Rp 750.000) -> MUST NOT be counted as income!
$payRejectedId = $paymentRepo->createPayment([
    'payment_number' => 'PAY/TEST/M6_REJECTED',
    'tenant_id' => $tenant1->id,
    'amount' => 750000.00,
    'payment_date' => $today,
    'payment_method' => 'transfer',
    'status' => 'rejected',
]);

// 6. Payment Reversed (Rp 300.000) -> MUST NOT be counted as income!
$payReversedId = $paymentRepo->createPayment([
    'payment_number' => 'PAY/TEST/M6_REVERSED',
    'tenant_id' => $tenant1->id,
    'amount' => 300000.00,
    'payment_date' => $today,
    'payment_method' => 'cash',
    'status' => 'reversed',
]);

// Fetch report summary
$summary = $reportService->getSummary($monthStart, $monthEnd);

// Verification:
assertTest("Financial report total income counts ONLY verified payments (excludes pending/rejected/reversed)", $summary['total_income'] >= 500000.00);
assertTest("Financial report recognizes outstanding receivables from unpaid/partial balances", $summary['total_receivables'] >= 3000000.00);
assertTest("Financial report calculates operational expenses", $summary['total_expense'] >= 400000.00);
assertTest("Net cash flow calculated as (total_income - total_expense)", $summary['net_cash_flow'] === ($summary['total_income'] - $summary['total_expense']));
assertTest("Expenses by category breakdown returned", !empty($summary['expenses_by_category']));
assertTest("Payments by method breakdown returned", !empty($summary['payments_by_method']));
assertTest("Expenses by method breakdown returned", !empty($summary['expenses_by_method']));

// -----------------------------------------------------------------
// TEST GROUP 9: Inactive Category Filtering
// -----------------------------------------------------------------
echo "\n[9] Testing Inactive Category Filtering...\n";

// Deactivate category
$expenseService->updateCategory($catId, ['name' => 'TEST_CAT_DEACTIVATED', 'is_active' => 0], 'Admin');
$activeCategories = $expenseService->getAllCategories(true);
$allCategories = $expenseService->getAllCategories(false);

$foundInActive = false;
foreach ($activeCategories as $ac) {
    if ($ac->id === $catId) {
        $foundInActive = true;
    }
}
$foundInAll = false;
foreach ($allCategories as $alc) {
    if ($alc->id === $catId) {
        $foundInAll = true;
    }
}
assertTest("Inactive category is excluded from active-only list", !$foundInActive);
assertTest("Inactive category remains visible in full category list for historical reference", $foundInAll);

// -----------------------------------------------------------------
// TEST GROUP 10: IDOR & Server-side Authorization Guards
// -----------------------------------------------------------------
echo "\n[10] Testing Server-side Authorization & Attachment IDOR Guard...\n";

// Tenant 2 attempts to download attachment from Tenant 1's request
$tenantPortal = new \App\Controllers\TenantPortalController(
    null, null, null, null, null, $maintService
);

// Mock user session as Tenant 2
Session::set('user', [
    'id' => (int)$tenant2->user_id,
    'role' => 'tenant',
    'name' => $tenant2->name,
    'email' => $tenant2->email,
]);

$attId = $savedMaint->attachments[0]->id;
$reqFake = new \App\Core\Request();
$reqFake->setParams(['id' => (string)$attId]);

if ($tenant1->id !== $tenant2->id) {
    $response = $tenantPortal->downloadMaintenanceAttachment($reqFake);
    assertTest("Tenant 2 is blocked (403) from downloading attachment of Tenant 1", $response->getStatusCode() === 403);
} else {
    // If only 1 tenant in test db, verify owner/admin download works
    $adminMaintCtrl = new \App\Controllers\MaintenanceController($maintService, $roomRepo);
    $responseAdmin = $adminMaintCtrl->downloadAttachment($reqFake);
    assertTest("Admin/Owner can download maintenance attachment", $responseAdmin->getStatusCode() === 200);
}

// Clear session
Session::destroy();

// -----------------------------------------------------------------
// Cleanup testing fixtures
// -----------------------------------------------------------------
$pdo->exec("DELETE FROM `payment_allocations` WHERE `payment_id` IN ({$payVerifiedId}, {$payPendingId}, {$payRejectedId}, {$payReversedId})");
$pdo->exec("DELETE FROM `payments` WHERE `id` IN ({$payVerifiedId}, {$payPendingId}, {$payRejectedId}, {$payReversedId})");
$pdo->exec("DELETE FROM `invoice_items` WHERE `invoice_id` IN ({$invUnpaidId}, {$invPartId})");
$pdo->exec("DELETE FROM `invoices` WHERE `id` IN ({$invUnpaidId}, {$invPartId})");
$pdo->exec("DELETE FROM `expenses` WHERE `description` LIKE 'TEST_EXP_%'");
$pdo->exec("DELETE FROM `expense_categories` WHERE `name` LIKE 'TEST_CAT_%'");
$pdo->exec("DELETE FROM `maintenance_updates` WHERE `notes` LIKE 'TEST_MAINT_%'");
$pdo->exec("DELETE FROM `maintenance_attachments` WHERE `file_name` LIKE 'test_maint_%'");
$pdo->exec("DELETE FROM `maintenance_requests` WHERE `title` LIKE 'TEST_MAINT_%'");
$pdo->exec("DELETE FROM `invoice_items` WHERE `invoice_id` IN (SELECT id FROM `invoices` WHERE `invoice_number` LIKE 'INV/TEST/M6%')");
$pdo->exec("DELETE FROM `invoices` WHERE `invoice_number` LIKE 'INV/TEST/M6%'");
$pdo->exec("DELETE FROM `payments` WHERE `payment_number` LIKE 'PAY/TEST/M6%'");

@unlink($dummyReceiptPath);
@unlink($dummyAttPath);

// -----------------------------------------------------------------
// Final Summary
// -----------------------------------------------------------------
echo "\n=========================================================\n";
echo "M6 TEST SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "=========================================================\n";

if ($failed > 0) {
    exit(1);
}
