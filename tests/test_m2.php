<?php

declare(strict_types=1);

// Test Runner for Milestone M2 (Database & Master Data)

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Repositories\FacilityRepository;
use App\Repositories\RoomRepository;
use App\Repositories\TenantRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\RoomService;
use App\Services\TenantService;

Session::start();
Config::loadEnv(BASE_PATH . '/.env');

class TestRunnerM2
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function assert(string $description, bool $condition, string $detail = ''): void
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$description}\n";
        } else {
            $this->failed++;
            $msg = "  [FAIL] {$description}" . ($detail ? " -> {$detail}" : '');
            $this->failures[] = $msg;
            echo "{$msg}\n";
        }
    }

    public function summary(): void
    {
        echo "\n" . str_repeat('=', 60) . "\n";
        echo "TEST M2 SUMMARY: {$this->passed} Passed, {$this->failed} Failed\n";
        echo str_repeat('=', 60) . "\n";

        if ($this->failed > 0) {
            echo "Failures:\n";
            foreach ($this->failures as $fail) {
                echo "{$fail}\n";
            }
            exit(1);
        }
    }
}

$t = new TestRunnerM2();
echo "Running Milestone M2 (Database & Master Data) Test Suite...\n\n";

$pdo = Database::connect();

// -----------------------------------------------------------------------------
// 1. Database Schema & Tables
// -----------------------------------------------------------------------------
echo "Test Suite 1: Database Schema & Tables\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

$expectedTables = [
    'users', 'roles', 'permissions', 'role_user', 'permission_role', 'account_activation_tokens',
    'rooms', 'room_facilities', 'room_facility_assignments', 'tenants',
    'bookings', 'rental_contracts', 'contract_histories', 'contract_extension_requests', 'early_checkout_requests',
    'invoices', 'invoice_items', 'payments', 'payment_proofs', 'payment_allocations', 'payment_adjustments',
    'expense_categories', 'expenses', 'maintenance_categories', 'maintenance_requests', 'maintenance_attachments', 'maintenance_updates',
    'notification_templates', 'whatsapp_logs', 'announcements', 'announcement_reads',
    'settings', 'activity_logs', 'migrations'
];

$allTablesExist = true;
foreach ($expectedTables as $exp) {
    if (!in_array($exp, $tables, true)) {
        $allTablesExist = false;
        $t->assert("Table {$exp} exists", false, "Table missing from database");
    }
}
$t->assert('All 34 required tables exist in database', $allTablesExist);

// -----------------------------------------------------------------------------
// 2. Seeder Data Verification
// -----------------------------------------------------------------------------
echo "\nTest Suite 2: Seeder Data Verification\n";

$rolesCount = (int)$pdo->query("SELECT COUNT(*) FROM `roles`")->fetchColumn();
$t->assert('Roles seeded (owner, admin, tenant)', $rolesCount >= 3);

$roomsCount = (int)$pdo->query("SELECT COUNT(*) FROM `rooms`")->fetchColumn();
$t->assert('Master rooms seeded (8 rooms)', $roomsCount >= 8);

$facCount = (int)$pdo->query("SELECT COUNT(*) FROM `room_facilities`")->fetchColumn();
$t->assert('Room facilities seeded (7 facilities)', $facCount >= 7);

$userRepo = new UserRepository($pdo);
$owner = $userRepo->findByEmail('owner@kost.com');
$t->assert('Owner account seeded and role resolved', $owner !== null && $owner->role === 'owner');

$admin = $userRepo->findByEmail('admin@kost.com');
$t->assert('Admin account seeded and role resolved', $admin !== null && $admin->role === 'admin');

// Verify default authentication against seeded password
$authService = new AuthService($userRepo);
$loginOwner = $authService->attempt('owner@kost.com', 'password123');
$t->assert('Owner can login successfully with seeded password', $loginOwner['success'] === true && $loginOwner['user']->isOwner());

// -----------------------------------------------------------------------------
// 3. Room & Facility Management Logic
// -----------------------------------------------------------------------------
echo "\nTest Suite 3: Room & Facility Operations\n";
$roomRepo = new RoomRepository($pdo);
$facilityRepo = new FacilityRepository($pdo);
$roomService = new RoomService($roomRepo);

// Test add room (room 205)
$testRoomNumber = '205';
// Clean up if leftover
$existing = $roomRepo->findByNumber($testRoomNumber);
if ($existing) {
    $roomRepo->delete($existing->id);
}

$allFacs = $facilityRepo->getAll();
$facIds = array_slice(array_column($allFacs, 'id'), 0, 3);

$newRoom = $roomService->createRoom([
    'room_number' => $testRoomNumber,
    'name' => 'Kamar 205 Test',
    'current_price' => 1600000.00,
    'status' => 'available',
    'description' => 'Kamar pengujian M2',
], $facIds, 'Tester');

$t->assert('Room created successfully', $newRoom->room_number === $testRoomNumber && count($newRoom->facilities) === 3);

// Test capacity constraint (< 10 rooms)
// Currently rooms = 8 seeded + 1 (205) = 9 rooms. Adding 1 more will make it 10, which should be rejected!
$newRoom10 = $roomService->createRoom([
    'room_number' => '206',
    'name' => 'Kamar 206 Max',
    'current_price' => 1500000.00,
    'status' => 'available',
], [], 'Tester'); // Now count is 10

$blocked = false;
try {
    $roomService->createRoom([
        'room_number' => '207',
        'name' => 'Kamar 207 Over Capacity',
        'current_price' => 1500000.00,
        'status' => 'available',
    ]);
} catch (\Throwable $e) {
    $blocked = str_contains($e->getMessage(), 'Kapasitas maksimal');
}
$t->assert('PRD capacity rule: Room creation blocked when reaching 10 rooms', $blocked === true);

// Clean up temporary test rooms
$roomRepo->delete($newRoom->id);
$roomRepo->delete($newRoom10->id);
$t->assert('Temporary test rooms deleted cleanly', $roomRepo->findByNumber($testRoomNumber) === null);

// -----------------------------------------------------------------------------
// 4. Tenant Registration & Self-Activation Workflow
// -----------------------------------------------------------------------------
echo "\nTest Suite 4: Tenant Registration & Secure Activation\n";
$tenantRepo = new TenantRepository($pdo);
$tenantService = new TenantService($tenantRepo, $userRepo);

$testEmail = 'calon_penghuni_' . time() . '@email.com';
$createdTenant = $tenantService->createTenant([
    'name' => 'Calon Penghuni Baru',
    'email' => $testEmail,
    'phone' => '081234567899',
    'id_card_number' => '3201123456780001',
    'occupation' => 'Karyawan Startup',
], 'Admin Tester');

$t->assert('Tenant account created with pending status', isset($createdTenant['user_id']) && isset($createdTenant['raw_token']));
$t->assert('Activation URL formatted with raw token', str_contains($createdTenant['activation_url'], 'token=' . $createdTenant['raw_token']));

// Verify activation token validation
$tokenRecord = $tenantRepo->verifyActivationToken($createdTenant['raw_token']);
$t->assert('Raw token correctly validates against hashed token in database', $tokenRecord !== null && $tokenRecord['email'] === $testEmail);

// Test self-activation: Tenant sets password
$newPassword = 'my_secure_tenant_password_2026';
$activationSuccess = $tenantService->activateTenant($createdTenant['raw_token'], $newPassword);
$t->assert('Tenant activation process succeeds', $activationSuccess === true);

// Verify single-use token constraint (cannot reuse token)
$reusedToken = $tenantRepo->verifyActivationToken($createdTenant['raw_token']);
$t->assert('Single-use token: Used token cannot be used again', $reusedToken === null);

// Verify newly activated tenant can login
$loginTenant = $authService->attempt($testEmail, $newPassword);
$t->assert('Activated tenant can login with newly created password', $loginTenant['success'] === true && $loginTenant['user']->isTenant());

// Summary
$t->summary();

