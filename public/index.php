<?php

declare(strict_types=1);

// Front Controller — Kost Management System (PHP Native Custom MVC)

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\FacilityController;
use App\Controllers\HomeController;
use App\Controllers\RoomController;
use App\Controllers\TenantController;
use App\Controllers\UserController;
use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RoleMiddleware;

// 1. Load Environment Configuration
Config::loadEnv(BASE_PATH . '/.env');

// 2. Register Centralized Error & Exception Handler
ErrorHandler::register();

// 3. Initialize Secure Session
Session::start();

// 4. Attach Global Middleware (CSRF Verification on mutating requests)
Router::use(new CsrfMiddleware());

// 5. Define Application Routes
// Public Routes
Router::get('/', [HomeController::class, 'index'], [], 'home');
Router::get('/katalog', [HomeController::class, 'catalog'], [], 'catalog');
Router::get('/catalog', [HomeController::class, 'catalog'], [], 'catalog.alt');
Router::get('/login', [AuthController::class, 'showLogin'], [], 'login');
Router::post('/login', [AuthController::class, 'login'], [], 'login.post');
Router::post('/logout', [AuthController::class, 'logout'], [], 'logout');

// Public Tenant Activation Routes
Router::get('/activate', [TenantController::class, 'showActivationForm'], [], 'tenant.activate');
Router::post('/activate', [TenantController::class, 'activate'], [], 'tenant.activate.post');

// Convenient Root Aliases / Redirects to Admin equivalents
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
    '/settings' => '/admin/settings',
];
foreach ($rootAliases as $from => $to) {
    Router::get($from, function (Request $req) use ($to, $from) {
        $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        if ($from === '/bookings' && !empty($_GET['room_id'])) {
            return Response::redirect('/admin/bookings/create' . $qs);
        }
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

// Admin & Owner Shared Operational Routes
Router::group([
    'prefix' => '/admin',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['admin', 'owner'])],
], function () {
    Router::get('/dashboard', [\App\Controllers\DashboardController::class, 'admin'], [], 'admin.dashboard');
    Router::get('/reports/financial', [\App\Controllers\DashboardController::class, 'financialReport'], [], 'admin.reports.financial');
    Router::get('/reports/export/financial', [\App\Controllers\ExportController::class, 'exportFinancial'], [], 'admin.reports.export.financial');
    Router::get('/reports/export/operational', [\App\Controllers\ExportController::class, 'exportOperational'], [], 'admin.reports.export.operational');

    // Rooms Management
    Router::get('/rooms', [RoomController::class, 'index'], [], 'admin.rooms');
    Router::get('/rooms/create', [RoomController::class, 'create'], [], 'admin.rooms.create');
    Router::post('/rooms', [RoomController::class, 'store'], [], 'admin.rooms.store');
    Router::get('/rooms/{id}/edit', [RoomController::class, 'edit'], [], 'admin.rooms.edit');
    Router::put('/rooms/{id}', [RoomController::class, 'update'], [], 'admin.rooms.update');
    Router::post('/rooms/{id}/delete', [RoomController::class, 'delete'], [], 'admin.rooms.delete');

    // Facilities Management
    Router::get('/facilities', [FacilityController::class, 'index'], [], 'admin.facilities');
    Router::post('/facilities', [FacilityController::class, 'store'], [], 'admin.facilities.store');
    Router::get('/facilities/{id}/edit', [FacilityController::class, 'edit'], [], 'admin.facilities.edit');
    Router::post('/facilities/{id}', [FacilityController::class, 'update'], [], 'admin.facilities.update');
    Router::put('/facilities/{id}', [FacilityController::class, 'update'], [], 'admin.facilities.put');
    Router::post('/facilities/{id}/delete', [FacilityController::class, 'delete'], [], 'admin.facilities.delete');

    // Tenants Management
    Router::get('/tenants', [TenantController::class, 'index'], [], 'admin.tenants');
    Router::get('/tenants/create', [TenantController::class, 'create'], [], 'admin.tenants.create');
    Router::post('/tenants', [TenantController::class, 'store'], [], 'admin.tenants.store');
    Router::get('/tenants/{id}/edit', [TenantController::class, 'edit'], [], 'admin.tenants.edit');
    Router::post('/tenants/{id}', [TenantController::class, 'update'], [], 'admin.tenants.update');
    Router::put('/tenants/{id}', [TenantController::class, 'update'], [], 'admin.tenants.put');
    Router::post('/tenants/{id}/reset-password', [TenantController::class, 'resetPassword'], [], 'admin.tenants.reset_password');
    Router::post('/tenants/{id}/delete', [TenantController::class, 'delete'], [], 'admin.tenants.delete');

    // Users & Access Management (Admin & Staff Accounts)
    Router::get('/users', [UserController::class, 'index'], [], 'admin.users');
    Router::get('/users/create', [UserController::class, 'create'], [], 'admin.users.create');
    Router::post('/users', [UserController::class, 'store'], [], 'admin.users.store');
    Router::get('/users/{id}/edit', [UserController::class, 'edit'], [], 'admin.users.edit');
    Router::post('/users/{id}', [UserController::class, 'update'], [], 'admin.users.update');
    Router::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'], [], 'admin.users.reset_password');
    Router::post('/users/{id}/delete', [UserController::class, 'delete'], [], 'admin.users.delete');

    // M3: Bookings Management
    Router::get('/bookings', [\App\Controllers\BookingController::class, 'index'], [], 'admin.bookings');
    Router::get('/bookings/create', [\App\Controllers\BookingController::class, 'create'], [], 'admin.bookings.create');
    Router::post('/bookings', [\App\Controllers\BookingController::class, 'store'], [], 'admin.bookings.store');
    Router::post('/bookings/{id}/confirm', [\App\Controllers\BookingController::class, 'confirm'], [], 'admin.bookings.confirm');
    Router::post('/bookings/{id}/cancel', [\App\Controllers\BookingController::class, 'cancel'], [], 'admin.bookings.cancel');
    Router::post('/bookings/{id}/convert', [\App\Controllers\BookingController::class, 'convert'], [], 'admin.bookings.convert');

    // M3: Contracts Management
    Router::get('/contracts', [\App\Controllers\ContractController::class, 'index'], [], 'admin.contracts');
    Router::get('/contracts/create', [\App\Controllers\ContractController::class, 'create'], [], 'admin.contracts.create');
    Router::post('/contracts', [\App\Controllers\ContractController::class, 'store'], [], 'admin.contracts.store');
    Router::get('/contracts/{id}', [\App\Controllers\ContractController::class, 'show'], [], 'admin.contracts.show');
    Router::post('/contracts/{id}/transfer-room', [\App\Controllers\ContractController::class, 'transferRoom'], [], 'admin.contracts.transfer_room');
    Router::post('/contracts/{id}/terminate', [\App\Controllers\ContractController::class, 'terminate'], [], 'admin.contracts.terminate');
    Router::post('/contracts/extensions/{id}/review', [\App\Controllers\ContractController::class, 'reviewExtension'], [], 'admin.contracts.review_extension');
    Router::post('/contracts/early-checkouts/{id}/review', [\App\Controllers\ContractController::class, 'reviewEarlyCheckout'], [], 'admin.contracts.review_early_checkout');

    // M4: Invoices Management
    Router::get('/invoices', [\App\Controllers\InvoiceController::class, 'index'], [], 'admin.invoices');
    Router::get('/invoices/create', [\App\Controllers\InvoiceController::class, 'create'], [], 'admin.invoices.create');
    Router::post('/invoices', [\App\Controllers\InvoiceController::class, 'store'], [], 'admin.invoices.store');
    Router::get('/invoices/{id}', [\App\Controllers\InvoiceController::class, 'show'], [], 'admin.invoices.show');
    Router::post('/invoices/{id}/cancel', [\App\Controllers\InvoiceController::class, 'cancel'], [], 'admin.invoices.cancel');

    // M4: Payments Management
    Router::get('/payments', [\App\Controllers\PaymentController::class, 'index'], [], 'admin.payments');
    Router::get('/payments/cash', [\App\Controllers\PaymentController::class, 'recordCash'], [], 'admin.payments.cash');
    Router::post('/payments/cash', [\App\Controllers\PaymentController::class, 'storeCash'], [], 'admin.payments.cash.store');
    Router::get('/payments/{id}', [\App\Controllers\PaymentController::class, 'show'], [], 'admin.payments.show');
    Router::get('/payments/{id}/proof', [\App\Controllers\PaymentController::class, 'downloadProof'], [], 'admin.payments.proof');
    Router::post('/payments/{id}/verify', [\App\Controllers\PaymentController::class, 'verify'], [], 'admin.payments.verify');
    Router::post('/payments/{id}/reject', [\App\Controllers\PaymentController::class, 'reject'], [], 'admin.payments.reject');
    Router::post('/payments/{id}/reverse', [\App\Controllers\PaymentController::class, 'reverse'], [], 'admin.payments.reverse');

    // M5: WhatsApp Notifications & Settings
    Router::get('/notifications', [\App\Controllers\NotificationController::class, 'index'], [], 'admin.notifications');
    Router::get('/notifications/templates', [\App\Controllers\NotificationController::class, 'templates'], [], 'admin.notifications.templates');
    Router::post('/notifications/templates', [\App\Controllers\NotificationController::class, 'updateTemplate'], [], 'admin.notifications.templates.update');
    Router::get('/notifications/settings', [\App\Controllers\NotificationController::class, 'settings'], [], 'admin.notifications.settings');
    Router::post('/notifications/settings', [\App\Controllers\NotificationController::class, 'updateSettings'], [], 'admin.notifications.settings.update');
    Router::post('/notifications/retry/{id}', [\App\Controllers\NotificationController::class, 'retry'], [], 'admin.notifications.retry');

    // M6: Operational Expenses & Categories
    Router::get('/expenses', [\App\Controllers\ExpenseController::class, 'index'], [], 'admin.expenses');
    Router::get('/expenses/create', [\App\Controllers\ExpenseController::class, 'create'], [], 'admin.expenses.create');
    Router::post('/expenses', [\App\Controllers\ExpenseController::class, 'store'], [], 'admin.expenses.store');
    Router::get('/expenses/categories', [\App\Controllers\ExpenseController::class, 'categories'], [], 'admin.expenses.categories');
    Router::post('/expenses/categories', [\App\Controllers\ExpenseController::class, 'storeCategory'], [], 'admin.expenses.categories.store');
    Router::post('/expenses/categories/{id}', [\App\Controllers\ExpenseController::class, 'updateCategory'], [], 'admin.expenses.categories.update');
    Router::post('/expenses/categories/{id}/delete', [\App\Controllers\ExpenseController::class, 'deleteCategory'], [], 'admin.expenses.categories.delete');
    Router::get('/expenses/{id}', [\App\Controllers\ExpenseController::class, 'show'], [], 'admin.expenses.show');
    Router::get('/expenses/{id}/edit', [\App\Controllers\ExpenseController::class, 'edit'], [], 'admin.expenses.edit');
    Router::post('/expenses/{id}', [\App\Controllers\ExpenseController::class, 'update'], [], 'admin.expenses.update');
    Router::post('/expenses/{id}/delete', [\App\Controllers\ExpenseController::class, 'delete'], [], 'admin.expenses.delete');
    Router::get('/expenses/{id}/receipt', [\App\Controllers\ExpenseController::class, 'downloadReceipt'], [], 'admin.expenses.receipt');

    // M6: Maintenance Management
    Router::get('/maintenance', [\App\Controllers\MaintenanceController::class, 'index'], [], 'admin.maintenance');
    Router::get('/maintenance/{id}', [\App\Controllers\MaintenanceController::class, 'show'], [], 'admin.maintenance.show');
    Router::post('/maintenance/{id}/status', [\App\Controllers\MaintenanceController::class, 'updateStatus'], [], 'admin.maintenance.status');
    Router::post('/maintenance/{id}/priority', [\App\Controllers\MaintenanceController::class, 'updatePriority'], [], 'admin.maintenance.priority');
    Router::post('/maintenance/{id}/updates', [\App\Controllers\MaintenanceController::class, 'addUpdate'], [], 'admin.maintenance.updates');
    Router::get('/maintenance/attachments/{id}', [\App\Controllers\MaintenanceController::class, 'downloadAttachment'], [], 'admin.maintenance.attachment');

    // General & Landing Settings
    Router::get('/settings', [\App\Controllers\SettingController::class, 'index'], [], 'admin.settings');
    Router::post('/settings', [\App\Controllers\SettingController::class, 'update'], [], 'admin.settings.update');
});

// Tenant Protected Routes
Router::group([
    'prefix' => '/tenant',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['tenant'])],
], function () {
    Router::get('/dashboard', [\App\Controllers\TenantPortalController::class, 'dashboard'], [], 'tenant.dashboard');
    Router::get('/extension', [\App\Controllers\TenantPortalController::class, 'extensionForm'], [], 'tenant.extension');
    Router::post('/extension', [\App\Controllers\TenantPortalController::class, 'submitExtension'], [], 'tenant.extension.submit');
    Router::get('/early-checkout', [\App\Controllers\TenantPortalController::class, 'earlyCheckoutForm'], [], 'tenant.checkout');
    Router::post('/early-checkout', [\App\Controllers\TenantPortalController::class, 'submitEarlyCheckout'], [], 'tenant.checkout.submit');

    // M4: Tenant Payments
    Router::get('/payment', [\App\Controllers\TenantPortalController::class, 'paymentForm'], [], 'tenant.payment');
    Router::post('/payments', [\App\Controllers\TenantPortalController::class, 'submitPayment'], [], 'tenant.payments.submit');

    // M6: Tenant Maintenance Reports
    Router::get('/maintenance', [\App\Controllers\TenantPortalController::class, 'maintenanceIndex'], [], 'tenant.maintenance');
    Router::get('/maintenance/create', [\App\Controllers\TenantPortalController::class, 'maintenanceCreate'], [], 'tenant.maintenance.create');
    Router::post('/maintenance', [\App\Controllers\TenantPortalController::class, 'maintenanceStore'], [], 'tenant.maintenance.store');
    Router::get('/maintenance/{id}', [\App\Controllers\TenantPortalController::class, 'maintenanceShow'], [], 'tenant.maintenance.show');
    Router::post('/maintenance/{id}/updates', [\App\Controllers\TenantPortalController::class, 'maintenanceAddUpdate'], [], 'tenant.maintenance.updates');
    Router::get('/maintenance/attachments/{id}', [\App\Controllers\TenantPortalController::class, 'downloadMaintenanceAttachment'], [], 'tenant.maintenance.attachment');
});

// 6. Capture Incoming Request and Dispatch through Router Pipeline
$request = Request::createFromGlobals();
$response = Router::dispatch($request);

// 7. Send Response to Client
$response->send();
