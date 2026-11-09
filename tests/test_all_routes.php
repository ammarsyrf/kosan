<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\FacilityController;
use App\Controllers\HomeController;
use App\Controllers\RoomController;
use App\Controllers\TenantController;
use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

Config::loadEnv(BASE_PATH . '/.env');
Session::start();

Router::clear();

// Public Routes
Router::get('/', [HomeController::class, 'index'], [], 'home');
Router::get('/login', [AuthController::class, 'showLogin'], [], 'login');
Router::get('/activate', [TenantController::class, 'showActivationForm'], [], 'tenant.activate');

// Convenient Root Aliases / Redirects
$rootAliases = [
    '/rooms' => '/admin/rooms',
    '/rooms/create' => '/admin/rooms/create',
    '/facilities' => '/admin/facilities',
    '/tenants' => '/admin/tenants',
    '/tenants/create' => '/admin/tenants/create',
    '/bookings' => '/admin/bookings',
    '/bookings/create' => '/admin/bookings/create',
    '/contracts' => '/admin/contracts',
    '/contracts/create' => '/admin/contracts/create',
    '/invoices' => '/admin/invoices',
    '/invoices/create' => '/admin/invoices/create',
    '/payments' => '/admin/payments',
    '/payments/cash' => '/admin/payments/cash',
    '/expenses' => '/admin/expenses',
    '/expenses/create' => '/admin/expenses/create',
    '/expenses/categories' => '/admin/expenses/categories',
    '/maintenance' => '/admin/maintenance',
    '/notifications' => '/admin/notifications',
    '/notifications/templates' => '/admin/notifications/templates',
    '/notifications/settings' => '/admin/notifications/settings',
];
foreach ($rootAliases as $from => $to) {
    Router::get($from, function (Request $req) use ($to) {
        $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        return Response::redirect($to . $qs);
    });
}

// Owner Protected Routes
Router::group([
    'prefix' => '/owner',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['owner'])],
], function () {
    Router::get('/dashboard', [\App\Controllers\DashboardController::class, 'owner'], [], 'owner.dashboard');
});

// Admin Protected Routes
Router::group([
    'prefix' => '/admin',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['admin', 'owner'])],
], function () {
    Router::get('/dashboard', [\App\Controllers\DashboardController::class, 'admin'], [], 'admin.dashboard');
    Router::get('/reports/financial', [\App\Controllers\DashboardController::class, 'financialReport'], [], 'admin.reports.financial');
    Router::get('/reports/export/financial', [\App\Controllers\ExportController::class, 'exportFinancial'], [], 'admin.reports.export.financial');
    Router::get('/reports/export/operational', [\App\Controllers\ExportController::class, 'exportOperational'], [], 'admin.reports.export.operational');

    Router::get('/rooms', [RoomController::class, 'index'], [], 'admin.rooms');
    Router::get('/rooms/create', [RoomController::class, 'create'], [], 'admin.rooms.create');

    Router::get('/facilities', [FacilityController::class, 'index'], [], 'admin.facilities');

    Router::get('/tenants', [TenantController::class, 'index'], [], 'admin.tenants');
    Router::get('/tenants/create', [TenantController::class, 'create'], [], 'admin.tenants.create');
    Router::get('/tenants/{id}/edit', [TenantController::class, 'edit'], [], 'admin.tenants.edit');

    Router::get('/bookings', [\App\Controllers\BookingController::class, 'index'], [], 'admin.bookings');
    Router::get('/bookings/create', [\App\Controllers\BookingController::class, 'create'], [], 'admin.bookings.create');

    Router::get('/contracts', [\App\Controllers\ContractController::class, 'index'], [], 'admin.contracts');
    Router::get('/contracts/create', [\App\Controllers\ContractController::class, 'create'], [], 'admin.contracts.create');

    Router::get('/invoices', [\App\Controllers\InvoiceController::class, 'index'], [], 'admin.invoices');
    Router::get('/invoices/create', [\App\Controllers\InvoiceController::class, 'create'], [], 'admin.invoices.create');

    Router::get('/payments', [\App\Controllers\PaymentController::class, 'index'], [], 'admin.payments');
    Router::get('/payments/cash', [\App\Controllers\PaymentController::class, 'recordCash'], [], 'admin.payments.cash');

    Router::get('/notifications', [\App\Controllers\NotificationController::class, 'index'], [], 'admin.notifications');
    Router::get('/notifications/templates', [\App\Controllers\NotificationController::class, 'templates'], [], 'admin.notifications.templates');
    Router::get('/notifications/settings', [\App\Controllers\NotificationController::class, 'settings'], [], 'admin.notifications.settings');

    Router::get('/expenses', [\App\Controllers\ExpenseController::class, 'index'], [], 'admin.expenses');
    Router::get('/expenses/create', [\App\Controllers\ExpenseController::class, 'create'], [], 'admin.expenses.create');
    Router::get('/expenses/categories', [\App\Controllers\ExpenseController::class, 'categories'], [], 'admin.expenses.categories');

    Router::get('/maintenance', [\App\Controllers\MaintenanceController::class, 'index'], [], 'admin.maintenance');
});

Router::group([
    'prefix' => '/tenant',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['tenant'])],
], function () {
    Router::get('/dashboard', [\App\Controllers\TenantPortalController::class, 'dashboard'], [], 'tenant.dashboard');
    Router::get('/extension', [\App\Controllers\TenantPortalController::class, 'extensionForm'], [], 'tenant.extension');
    Router::get('/early-checkout', [\App\Controllers\TenantPortalController::class, 'earlyCheckoutForm'], [], 'tenant.checkout');
    Router::get('/payment', [\App\Controllers\TenantPortalController::class, 'paymentForm'], [], 'tenant.payment');
    Router::get('/maintenance', [\App\Controllers\TenantPortalController::class, 'maintenanceIndex'], [], 'tenant.maintenance');
    Router::get('/maintenance/create', [\App\Controllers\TenantPortalController::class, 'maintenanceCreate'], [], 'tenant.maintenance.create');
});

// Users
$userRepo = new \App\Repositories\UserRepository();
$ownerUser = $userRepo->findByEmail('owner@kost.com');
$adminUser = $userRepo->findByEmail('admin@kost.com');
$tenantUser = $userRepo->findByEmail('budi@gmail.com');

$ownerSession = $ownerUser ? ['id' => $ownerUser->id, 'name' => $ownerUser->name, 'email' => $ownerUser->email, 'role' => 'owner', 'permissions' => []] : null;
$adminSession = $adminUser ? ['id' => $adminUser->id, 'name' => $adminUser->name, 'email' => $adminUser->email, 'role' => 'admin', 'permissions' => []] : null;
$tenantSession = $tenantUser ? ['id' => $tenantUser->id, 'name' => $tenantUser->name, 'email' => $tenantUser->email, 'role' => 'tenant', 'permissions' => []] : null;

$routesToTest = [
    // Guest
    ['uri' => '/', 'role' => null, 'user' => null, 'expected' => 200],
    ['uri' => '/login', 'role' => null, 'user' => null, 'expected' => 200],
    
    // Owner
    ['uri' => '/owner/dashboard', 'role' => 'owner', 'user' => $ownerSession, 'expected' => 200],
    ['uri' => '/admin/reports/financial', 'role' => 'owner', 'user' => $ownerSession, 'expected' => 200],

    // Admin
    ['uri' => '/admin/dashboard', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/rooms', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/rooms/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/facilities', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/tenants', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/tenants/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/tenants/1/edit', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/bookings', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/bookings/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/admin/bookings', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/bookings/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/contracts', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/contracts/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/admin/contracts', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/contracts/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/invoices', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/payments', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/expenses', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/maintenance', 'role' => 'admin', 'user' => $adminSession, 'expected' => 302],
    ['uri' => '/admin/invoices', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/invoices/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/payments', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/payments/cash', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/notifications', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/notifications/templates', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/notifications/settings', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/expenses', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/expenses/create', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/expenses/categories', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],
    ['uri' => '/admin/maintenance', 'role' => 'admin', 'user' => $adminSession, 'expected' => 200],

    // Tenant
    ['uri' => '/tenant/dashboard', 'role' => 'tenant', 'user' => $tenantSession, 'expected' => 200],
    ['uri' => '/tenant/extension', 'role' => 'tenant', 'user' => $tenantSession, 'expected' => 200],
    ['uri' => '/tenant/early-checkout', 'role' => 'tenant', 'user' => $tenantSession, 'expected' => 200],
    ['uri' => '/tenant/payment', 'role' => 'tenant', 'user' => $tenantSession, 'expected' => 200],
    ['uri' => '/tenant/maintenance', 'role' => 'tenant', 'user' => $tenantSession, 'expected' => 200],
    ['uri' => '/tenant/maintenance/create', 'role' => 'tenant', 'user' => $tenantSession, 'expected' => 200],
];

echo "=========================================================\n";
echo "       COMPREHENSIVE HTTP ROUTE VERIFICATION TEST        \n";
echo "=========================================================\n\n";

$passed = 0;
$failed = 0;

foreach ($routesToTest as $item) {
    $uri = $item['uri'];
    $roleName = $item['role'] ?? 'GUEST';
    $user = $item['user'];
    $expected = $item['expected'] ?? 200;

    // Set Session
    $_SESSION = [];
    if ($user) {
        $_SESSION['user'] = $user;
    }

    $request = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => $uri]);

    try {
        $response = Router::dispatch($request);
        $status = $response->getStatusCode();

        if ($status === $expected) {
            echo "  [PASS {$status}] [{$roleName}] {$uri}\n";
            $passed++;
        } else {
            echo "  [FAIL {$status} (expected {$expected})] [{$roleName}] {$uri}\n";
            $failed++;
        }
    } catch (\Throwable $e) {
        echo "  [EXCEPTION] [{$roleName}] {$uri} -> " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
        $failed++;
    }
}

echo "\n=========================================================\n";
echo "RESULTS: {$passed} Passed, {$failed} Failed\n";
echo "=========================================================\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
