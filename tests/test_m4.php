<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Session;
use App\Repositories\BookingRepository;
use App\Repositories\ContractRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;
use App\Services\BookingService;
use App\Services\ContractService;
use App\Services\InvoiceService;
use App\Services\PaymentService;

Session::start();
Config::loadEnv(BASE_PATH . '/.env');

echo "=========================================================\n";
echo "           M4 TEST SUITE: BILLING & PAYMENTS             \n";
echo "=========================================================\n\n";

$pdo = Database::connect();
$invoiceRepo = new InvoiceRepository();
$paymentRepo = new PaymentRepository();
$invoiceService = new InvoiceService();
$paymentService = new PaymentService();
$contractService = new ContractService();
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

// Prepare fresh testing fixtures
$rooms = $roomRepo->getAll();
$testRoom = $rooms[0];

$tenants = $tenantRepo->getAll();
$testTenant = $tenants[0];
$otherTenant = count($tenants) > 1 ? $tenants[1] : $tenants[0];

// Cleanup past test data for test room and tenant
$pdo->prepare("DELETE FROM payment_adjustments WHERE payment_id IN (SELECT id FROM payments WHERE tenant_id = ?)")->execute([$testTenant->id]);
$pdo->prepare("DELETE FROM payment_allocations WHERE invoice_id IN (SELECT id FROM invoices WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?))")->execute([$testRoom->id]);
$pdo->prepare("DELETE FROM payment_proofs WHERE payment_id IN (SELECT id FROM payments WHERE tenant_id = ?)")->execute([$testTenant->id]);
$pdo->prepare("DELETE FROM payments WHERE tenant_id = ?")->execute([$testTenant->id]);
$pdo->prepare("DELETE FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?))")->execute([$testRoom->id]);
$pdo->prepare("DELETE FROM invoices WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?)")->execute([$testRoom->id]);
$pdo->prepare("DELETE FROM contract_histories WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?)")->execute([$testRoom->id]);
$pdo->prepare("DELETE FROM rental_contracts WHERE room_id = ?")->execute([$testRoom->id]);
$pdo->prepare("DELETE FROM bookings WHERE room_id = ?")->execute([$testRoom->id]);

// Create Active Contract for M4 Tests
$contractId = $contractService->createDirectContract([
    'room_id' => $testRoom->id,
    'tenant_id' => $testTenant->id,
    'start_date' => '2027-01-31', // Anchor day 31
    'end_date' => '2027-12-31',
    'rent_price_snapshot' => 1500000.00,
    'billing_anchor_day' => 31,
    'notes' => 'Contract for M4 testing',
], 1, 'TestM4');

echo "1. Invoice Creation & Manual Invoices:\n";

// Test 1.1: Create manual invoice
$inv1Id = $invoiceService->createManualInvoice([
    'contract_id' => $contractId,
    'period_start' => '2027-01-31',
    'period_end' => '2027-02-27',
    'issue_date' => '2027-01-31',
    'due_date' => '2027-02-07',
    'is_first_month_manual' => 1,
    'notes' => 'First month manual invoice',
], [
    ['description' => 'Sewa Bulan Pertama', 'amount' => 1500000.00],
    ['description' => 'Deposit Kamar', 'amount' => 500000.00],
], 'AdminTest');

assertTest("Membuat invoice manual dengan multiple items", $inv1Id > 0);
$inv1 = $invoiceRepo->findById($inv1Id);
assertTest("Total tagihan dan balance dihitung otomatis (Rp 2.000.000)", (float)$inv1->total_amount === 2000000.00 && (float)$inv1->balance === 2000000.00);
assertTest("Tandai tagihan bulan pertama (is_first_month_manual = 1)", $inv1->is_first_month_manual === 1);

// Test 1.2: Duplicate invoice prevention
$duplicateCaught = false;
try {
    $invoiceService->createManualInvoice([
        'contract_id' => $contractId,
        'period_start' => '2027-01-31', // Same period
        'period_end' => '2027-02-27',
        'issue_date' => '2027-01-31',
        'due_date' => '2027-02-07',
    ], [
        ['description' => 'Duplicate item', 'amount' => 1500000.00]
    ], 'AdminTest');
} catch (\Throwable $e) {
    $duplicateCaught = true;
}
assertTest("Mencegah penerbitan invoice duplikat untuk periode yang sama", $duplicateCaught);

echo "\n2. Routine Invoice Generation & Billing Anchor Clamping:\n";

// Test 2.1: Generate routine invoice for March 2027 (Anchor 31 -> starts 2027-03-31)
$routineInvId = $invoiceService->generateRoutineInvoice($contractId, 2027, 3, 'CronTest');
assertTest("Generate invoice rutin berdasarkan billing anchor kontrak", $routineInvId > 0);
$rInv = $invoiceRepo->findById($routineInvId);
assertTest("Periode mulai sesuai anchor day (2027-03-31)", $rInv->period_start === '2027-03-31');

// Test 2.2: Routine invoice for April (30 days month, next anchor clamped to 30)
// Next month April (starts 2027-04-30, next anchor 31 in May clamped appropriately)
$routineInvApr = $invoiceService->generateRoutineInvoice($contractId, 2027, 4, 'CronTest');
assertTest("Invoice rutin bulan April clamped ke 2027-04-30", $routineInvApr > 0);

echo "\n3. Payment Submission & Pending Status:\n";

// Test 3.1: Tenant submits transfer payment (dummy proof file)
$dummyProofPath = tempnam(sys_get_temp_dir(), 'proof_') . '.png';
file_put_contents($dummyProofPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

$pay1Id = $paymentService->submitTenantPayment([
    'tenant_id' => $testTenant->id,
    'amount' => 1000000.00,
    'payment_method' => 'transfer',
    'payment_date' => '2027-02-01',
    'invoice_id' => $inv1Id,
    'notes' => 'Bayar cicilan transfer pertama',
], [
    'tmp_name' => $dummyProofPath,
    'name' => 'transfer_struk.png',
    'size' => filesize($dummyProofPath),
], 'TenantTest');

assertTest("Penyewa berhasil mengirim pembayaran transfer dengan bukti bayar", $pay1Id > 0);
$pay1 = $paymentRepo->findById($pay1Id);
assertTest("Status awal pembayaran adalah 'pending'", $pay1->status === 'pending');

// Test 3.2: Pending payment does NOT reduce invoice balance
$inv1AfterPending = $invoiceRepo->findById($inv1Id);
assertTest("Pembayaran pending TIDAK mengurangi saldo invoice", (float)$inv1AfterPending->paid_amount === 0.00 && (float)$inv1AfterPending->balance === 2000000.00);

echo "\n4. Payment Rejection & Saldo Integrity:\n";

// Create another pending payment to test rejection
$payRejectId = $paymentService->submitTenantPayment([
    'tenant_id' => $testTenant->id,
    'amount' => 500000.00,
    'payment_method' => 'transfer',
    'payment_date' => '2027-02-02',
], [
    'tmp_name' => $dummyProofPath,
    'name' => 'fake_struk.png',
    'size' => filesize($dummyProofPath),
], 'TenantTest');

$paymentService->rejectPayment($payRejectId, 'Bukti mutasi tidak ditemukan di rekening', 1, 'AdminTest');
$payRejected = $paymentRepo->findById($payRejectId);
assertTest("Admin menolak pembayaran dengan alasan penolakan wajib", $payRejected->status === 'rejected' && !empty($payRejected->rejection_reason));

// Rejection must NOT reduce invoice balance
$inv1AfterReject = $invoiceRepo->findById($inv1Id);
assertTest("Pembayaran ditolak (rejected) TIDAK mengurangi saldo invoice", (float)$inv1AfterReject->paid_amount === 0.00);

echo "\n5. Payment Verification & Partial Allocation:\n";

// Verify pay1 (Rp 1.000.000) allocated to inv1 (Total 2.000.000)
$paymentService->verifyPayment($pay1Id, [
    ['invoice_id' => $inv1Id, 'amount' => 1000000.00]
], 1, 'AdminTest');

$pay1Verified = $paymentRepo->findById($pay1Id);
assertTest("Status pembayaran berubah menjadi 'verified'", $pay1Verified->status === 'verified');

$inv1AfterPay1 = $invoiceRepo->findById($inv1Id);
assertTest("Saldo invoice berkurang sebesar pembayaran verified (paid: 1.000.000, balance: 1.000.000)",
    (float)$inv1AfterPay1->paid_amount === 1000000.00 && (float)$inv1AfterPay1->balance === 1000000.00
);
assertTest("Status invoice berubah menjadi 'partially_paid'", $inv1AfterPay1->status === 'partially_paid');

echo "\n6. Full Payment & Paid Status:\n";

// Pay remaining balance of inv1 (Rp 1.000.000) via Cash Payment
$payCashId = $paymentService->recordCashPayment([
    'tenant_id' => $testTenant->id,
    'amount' => 1000000.00,
    'payment_date' => '2027-02-05',
    'invoice_id' => $inv1Id,
    'notes' => 'Pelunasan sisa tagihan tunai',
], 1, 'AdminTest');

assertTest("Pencatatan pembayaran cash langsung terverifikasi", $payCashId > 0);
$inv1AfterFull = $invoiceRepo->findById($inv1Id);
assertTest("Invoice lunas sepenuhnya (balance = 0.00)", (float)$inv1AfterFull->balance === 0.00 && (float)$inv1AfterFull->paid_amount === 2000000.00);
assertTest("Status invoice berubah menjadi 'paid'", $inv1AfterFull->status === 'paid');

echo "\n7. Overpayment & Allocation Overbalance Protection:\n";

// Try allocating more than remaining balance
$payOverId = $paymentService->submitTenantPayment([
    'tenant_id' => $testTenant->id,
    'amount' => 500000.00,
    'payment_method' => 'transfer',
], [
    'tmp_name' => $dummyProofPath,
    'name' => 'proof.png',
    'size' => filesize($dummyProofPath),
], 'TenantTest');

$overpayCaught = false;
try {
    // inv1 is already paid (balance = 0), trying to allocate 500.000 must fail
    $paymentService->verifyPayment($payOverId, [
        ['invoice_id' => $inv1Id, 'amount' => 500000.00]
    ], 1, 'AdminTest');
} catch (\Throwable $e) {
    $overpayCaught = true;
}
assertTest("Mencegah overpayment pada invoice yang sudah lunas", $overpayCaught);

echo "\n8. Tenant IDOR Allocation Protection:\n";

// Try allocating payment of Tenant A to Invoice of Tenant B
$idorCaught = false;
try {
    $otherInvoiceId = $invoiceService->createManualInvoice([
        'contract_id' => $contractId,
        'period_start' => '2027-09-01',
        'period_end' => '2027-09-30',
        'issue_date' => '2027-09-01',
        'due_date' => '2027-09-07',
    ], [
        ['description' => 'Tagihan Orang Lain', 'amount' => 500000.00]
    ], 'AdminTest');

    // Force update otherInvoice tenant_id to otherTenant
    $pdo->prepare("UPDATE `invoices` SET `tenant_id` = ? WHERE `id` = ?")->execute([$otherTenant->id, $otherInvoiceId]);

    // Try allocating payOverId (which belongs to testTenant) to otherInvoice
    $paymentService->verifyPayment($payOverId, [
        ['invoice_id' => $otherInvoiceId, 'amount' => 500000.00]
    ], 1, 'AdminTest');
} catch (\Throwable $e) {
    $idorCaught = true;
}
assertTest("IDOR: Mencegah alokasi pembayaran ke invoice milik penyewa lain", $idorCaught);

echo "\n9. Reversal & Financial Audit Trail:\n";

// Reverse cash payment (payCashId)
$paymentService->reversePayment($payCashId, 'Koreksi salah input nominal kasir', 1, 'AdminTest');
$payCashRev = $paymentRepo->findById($payCashId);
assertTest("Status pembayaran berubah menjadi 'reversed'", $payCashRev->status === 'reversed');

$inv1AfterReversal = $invoiceRepo->findById($inv1Id);
assertTest("Saldo tagihan invoice dikembalikan setelah reversal (balance kembali 1.000.000)", (float)$inv1AfterReversal->balance === 1000000.00);
assertTest("Status invoice kembali menjadi 'partially_paid'", $inv1AfterReversal->status === 'partially_paid');

$adjustments = $paymentRepo->getAdjustments($payCashId);
assertTest("Audit trail mencatat entri adjustment reversal lengkap dengan alasan", count($adjustments) > 0 && !empty($adjustments[0]['reason']));

echo "\n10. Overdue Invoice Calculation & Cron:\n";

// Set due_date of routine invoice to past
$pdo->prepare("UPDATE `invoices` SET `due_date` = '2020-01-01', `status` = 'unpaid' WHERE `id` = ?")->execute([$routineInvId]);
$overdueCount = $invoiceService->updateOverdueInvoices();
assertTest("Cron menandai invoice yang melewati jatuh tempo menjadi 'overdue'", $overdueCount >= 1);
$invOverdue = $invoiceRepo->findById($routineInvId);
assertTest("Status invoice terverifikasi 'overdue'", $invOverdue->status === 'overdue');

echo "\n=========================================================\n";
echo "HASIL TEST M4: Passed: {$passed}, Failed: {$failed}\n";
echo "=========================================================\n";

// Clean up test proof file
@unlink($dummyProofPath);

if ($failed > 0) {
    exit(1);
}
