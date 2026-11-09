<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Session;
use App\Models\RentalContract;
use App\Services\BookingService;
use App\Services\ContractService;
use App\Repositories\BookingRepository;
use App\Repositories\ContractRepository;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;

Session::start();
Config::loadEnv(BASE_PATH . '/.env');

echo "=========================================================\n";
echo "           M3 TEST SUITE: BOOKINGS & CONTRACTS           \n";
echo "=========================================================\n\n";

$pdo = Database::connect();
$bookingService = new BookingService();
$contractService = new ContractService();
$bookingRepo = new BookingRepository();
$contractRepo = new ContractRepository();
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
$testRoomId = (int)$rooms[0]->id;

$tenants = $tenantRepo->getAll();
$testTenantId = (int)$tenants[0]->id;

// Clean up previous test booking/contract data for this room to ensure clean isolation
$pdo->prepare("DELETE FROM contract_histories WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?)")->execute([$testRoomId]);
$pdo->prepare("DELETE FROM contract_extension_requests WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?)")->execute([$testRoomId]);
$pdo->prepare("DELETE FROM early_checkout_requests WHERE contract_id IN (SELECT id FROM rental_contracts WHERE room_id = ?)")->execute([$testRoomId]);
$pdo->prepare("DELETE FROM rental_contracts WHERE room_id = ?")->execute([$testRoomId]);
$pdo->prepare("DELETE FROM bookings WHERE room_id = ?")->execute([$testRoomId]);

echo "1. Room Availability & Booking Creation:\n";

// Test 1.1: Create first booking
$book1 = $bookingService->createBooking([
    'tenant_id' => $testTenantId,
    'room_id' => $testRoomId,
    'start_date' => '2026-12-01',
    'end_date' => '2026-12-15',
    'notes' => 'Test booking 1'
], 'Tester');

assertTest("Membuat booking pertama valid", $book1->id > 0 && $book1->status === 'pending');
$book1Id = (int)$book1->id;

// Test 1.2: Overlapping booking on same room must be rejected
$overlapCaught = false;
try {
    $bookingService->createBooking([
        'tenant_id' => $testTenantId,
        'room_id' => $testRoomId,
        'start_date' => '2026-12-10', // Overlaps with 2026-12-01 - 2026-12-15
        'end_date' => '2026-12-20',
        'notes' => 'Overlapping booking'
    ], 'Tester');
} catch (\Throwable $e) {
    $overlapCaught = true;
}
assertTest("Mencegah double booking (overlap tanggal)", $overlapCaught);

// Test 1.3: Non-overlapping booking on same room must succeed
$bookNonOverlap = $bookingService->createBooking([
    'tenant_id' => $testTenantId,
    'room_id' => $testRoomId,
    'start_date' => '2026-12-16', // Starts after Dec 15
    'end_date' => '2026-12-31',
    'notes' => 'Non-overlapping booking'
], 'Tester');

assertTest("Mengizinkan booking kamar yang sama di periode berbeda (non-overlapping)", $bookNonOverlap->id > 0);

echo "\n2. Expiry Duration Snapshot & Expiration:\n";

// Verify snapshot expiry configuration
$bookingRow = $bookingRepo->findById($book1Id);
assertTest("Snapshot konfigurasi kedaluwarsa tersimpan di booking", $bookingRow->duration_hours_snapshot > 0 && !empty($bookingRow->expires_at));

// Set expires_at to past to test expiration
$pdo->prepare("UPDATE bookings SET expires_at = '2020-01-01 00:00:00' WHERE id = ?")->execute([$book1Id]);
$expiredCount = $bookingRepo->expirePendingBookings();
assertTest("Status pending kedaluwarsa otomatis berubah menjadi 'expired'", $expiredCount >= 1);

$expiredBooking = $bookingRepo->findById($book1Id);
assertTest("Verifikasi status booking telah expired", $expiredBooking->status === 'expired');

echo "\n3. Confirmed Booking Behavior:\n";

// Confirm the second booking
$book2Id = (int)$bookNonOverlap->id;
$confirmRes = $bookingService->confirmBooking($book2Id, 'Tester');
assertTest("Konfirmasi booking pending berhasil", $confirmRes === true);

// Set expires_at to past for confirmed booking; it should NOT expire!
$pdo->prepare("UPDATE bookings SET expires_at = '2020-01-01 00:00:00' WHERE id = ?")->execute([$book2Id]);
$bookingRepo->expirePendingBookings();

$confirmedBooking = $bookingRepo->findById($book2Id);
assertTest("Booking yang sudah Confirmed TIDAK kedaluwarsa otomatis", $confirmedBooking->status === 'confirmed');

echo "\n4. Conversion of Booking to Contract:\n";

$contractId = $bookingService->convertToContract($book2Id, [
    'notes' => 'Converted during test',
], 1, 'Tester');

assertTest("Konversi booking Confirmed menjadi kontrak sewa", $contractId > 0);

$bookingAfterConvert = $bookingRepo->findById($book2Id);
assertTest("Status booking berubah menjadi 'converted'", $bookingAfterConvert->status === 'converted');

$contractRow = $contractRepo->findById($contractId);
assertTest("Kontrak baru memiliki status 'active'", $contractRow->isActive());
assertTest("Snapshot harga dan billing_anchor_day tersimpan", (float)$contractRow->rent_price_snapshot > 0 && !empty($contractRow->billing_anchor_day));

echo "\n5. Billing Anchor Day Calculation (PRD 6.4):\n";

// Anchor day 31: Feb (non-leap) -> Feb 28, Feb (leap) -> Feb 29, April -> April 30, Jan -> Jan 31
$dueDateFeb2025 = RentalContract::calculateDueDateForMonth(31, 2025, 2);
assertTest("Anchor 31 pada Feb 2025 menghasilkan 2025-02-28", $dueDateFeb2025 === '2025-02-28');

$dueDateFeb2024 = RentalContract::calculateDueDateForMonth(31, 2024, 2);
assertTest("Anchor 31 pada Feb 2024 (kabisat) menghasilkan 2024-02-29", $dueDateFeb2024 === '2024-02-29');

$dueDateApr = RentalContract::calculateDueDateForMonth(31, 2026, 4);
assertTest("Anchor 31 pada April (30 hari) menghasilkan 2026-04-30", $dueDateApr === '2026-04-30');

$dueDateJan = RentalContract::calculateDueDateForMonth(31, 2026, 1);
assertTest("Anchor 31 kembali ke 31 pada Januari menghasilkan 2026-01-31", $dueDateJan === '2026-01-31');

echo "\n6. Contract Extension Workflow:\n";

// Tenant requests extension (e.g. 1 month)
$reqId = $contractService->requestExtension($contractId, 1, 'TesterTenant');
assertTest("Tenant dapat mengajukan perpanjangan sewa", $reqId > 0);

// Admin reviews and approves extension with custom price
$reviewExt = $contractService->reviewExtension($reqId, true, 1500000.00, 1, 'Disetujui dengan tarif baru', 'TesterAdmin');
assertTest("Admin menyetujui perpanjangan dan memperbarui tarif serta tanggal", $reviewExt === true);

$updatedContract = $contractRepo->findById($contractId);
assertTest("Tarif sewa bulanan diperbarui menjadi Rp 1.500.000", (float)$updatedContract->rent_price_snapshot === 1500000.00);

echo "\n7. Early Checkout Workflow & Financial Decisions:\n";

// Tenant requests early checkout
$coReqId = $contractService->requestEarlyCheckout($contractId, '2026-12-25', 'Pindah kerja mendadak', 'TesterTenant');
assertTest("Tenant dapat mengajukan early checkout", $coReqId > 0);

// Admin approves with custom refund
$reviewCo = $contractService->reviewEarlyCheckout($coReqId, true, 'custom_refund', 250000.00, null, 1, 'Disetujui dengan pengembalian sebagian', 'TesterAdmin');
assertTest("Admin menyetujui early checkout dengan keputusan finansial custom_refund", $reviewCo === true);

$terminatedContract = $contractRepo->findById($contractId);
assertTest("Status kontrak berubah menjadi 'terminated' dan tanggal akhir disesuaikan", $terminatedContract->status === 'terminated' && $terminatedContract->end_date === '2026-12-25');

echo "\n8. Contract Audit History Integrity:\n";

$histories = $contractRepo->getHistories($contractId);
assertTest("Audit history mencatat riwayat perubahan kontrak", count($histories) >= 3);
$actions = array_map(fn($h) => $h->action, $histories);
assertTest("Histori mencakup CREATED, EXTENDED, dan EARLY_CHECKOUT",
    in_array('CREATED', $actions, true) &&
    in_array('EXTENDED', $actions, true) &&
    in_array('EARLY_CHECKOUT', $actions, true)
);

echo "\n=========================================================\n";
echo "HASIL TEST M3: Passed: {$passed}, Failed: {$failed}\n";
echo "=========================================================\n";

if ($failed > 0) {
    exit(1);
}
