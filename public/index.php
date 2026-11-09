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
use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Request;
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
Router::get('/login', [AuthController::class, 'showLogin'], [], 'login');
Router::post('/login', [AuthController::class, 'login'], [], 'login.post');
Router::post('/logout', [AuthController::class, 'logout'], [], 'logout');

// Public Tenant Activation Routes
Router::get('/activate', [TenantController::class, 'showActivationForm'], [], 'tenant.activate');
Router::post('/activate', [TenantController::class, 'activate'], [], 'tenant.activate.post');

// Owner Protected Routes
Router::group([
    'prefix' => '/owner',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['owner'])],
], function () {
    Router::get('/dashboard', [HomeController::class, 'ownerDashboard'], [], 'owner.dashboard');
});

// Admin & Owner Shared Operational Routes
Router::group([
    'prefix' => '/admin',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['admin', 'owner'])],
], function () {
    Router::get('/dashboard', [HomeController::class, 'adminDashboard'], [], 'admin.dashboard');

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
    Router::post('/facilities/{id}/delete', [FacilityController::class, 'delete'], [], 'admin.facilities.delete');

    // Tenants Management
    Router::get('/tenants', [TenantController::class, 'index'], [], 'admin.tenants');
    Router::get('/tenants/create', [TenantController::class, 'create'], [], 'admin.tenants.create');
    Router::post('/tenants', [TenantController::class, 'store'], [], 'admin.tenants.store');

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
});

// 6. Capture Incoming Request and Dispatch through Router Pipeline
$request = Request::createFromGlobals();
$response = Router::dispatch($request);

// 7. Send Response to Client
$response->send();
