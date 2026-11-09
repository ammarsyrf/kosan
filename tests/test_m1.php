<?php

declare(strict_types=1);

// Test Runner for Milestone M1 (MVC Foundation)

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Models\User;
use App\Services\AuthService;

// Start session before any echo output
Session::start();

class TestRunner
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
        echo "TEST SUMMARY: {$this->passed} Passed, {$this->failed} Failed\n";
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

$t = new TestRunner();

echo "Running Milestone M1 Test Suite...\n\n";

// -----------------------------------------------------------------------------
// 1. Config & Environment Loader
// -----------------------------------------------------------------------------
echo "Test Suite 1: Config & Environment\n";
Config::loadEnv(BASE_PATH . '/.env');

$t->assert('Config loads APP_NAME correctly', config('app.name') === 'Kost Management System');
$t->assert('Config loads APP_ENV correctly', in_array(config('app.env'), ['local', 'production', 'testing'], true));
$t->assert('Config loads Database host', config('database.connections.mysql.host') === '127.0.0.1');
$t->assert('Config loads Database name', config('database.connections.mysql.database') === 'kost_management');
$t->assert('Config default helper returns default on missing key', config('non.existent.key', 'fallback') === 'fallback');

// -----------------------------------------------------------------------------
// 2. Session Management & Flash Messages
// -----------------------------------------------------------------------------
echo "\nTest Suite 2: Session Management\n";
Session::set('test_key', 'test_value');
$t->assert('Session::set and Session::get work', Session::get('test_key') === 'test_value');
$t->assert('Session::has detects existing key', Session::has('test_key') === true);
$t->assert('Session::has returns false on missing key', Session::has('unknown_key') === false);

Session::flash('notice', 'Operasi berhasil');
$t->assert('Session::flash stores message', Session::getFlash('notice') === 'Operasi berhasil');
$t->assert('Session::getFlash clears message after retrieval', Session::getFlash('notice') === null);

// -----------------------------------------------------------------------------
// 3. CSRF Protection
// -----------------------------------------------------------------------------
echo "\nTest Suite 3: CSRF Protection\n";
$token = Csrf::token();
$t->assert('CSRF token is generated and 64 hex characters', strlen($token) === 64 && ctype_xdigit($token));
$t->assert('CSRF validation passes with correct token', Csrf::validate($token) === true);
$t->assert('CSRF validation fails with invalid token', Csrf::validate('invalid_token_12345') === false);
$t->assert('CSRF validation fails with null token', Csrf::validate(null) === false);

$getReq = new Request([], [], ['REQUEST_METHOD' => 'GET']);
$t->assert('CSRF request validation allows safe GET request', Csrf::validateRequest($getReq) === true);

$postReqBad = new Request([], ['_csrf_token' => 'wrong'], ['REQUEST_METHOD' => 'POST']);
$t->assert('CSRF request validation rejects invalid POST token', Csrf::validateRequest($postReqBad) === false);

$postReqGood = new Request([], ['_csrf_token' => $token], ['REQUEST_METHOD' => 'POST']);
$t->assert('CSRF request validation accepts valid POST token', Csrf::validateRequest($postReqGood) === true);

// -----------------------------------------------------------------------------
// 4. Input Validator
// -----------------------------------------------------------------------------
echo "\nTest Suite 4: Validator\n";
$v1 = Validator::make([
    'email' => 'admin@kost.com',
    'password' => 'secret123',
    'password_confirmation' => 'secret123',
    'nominal' => '1500000',
], [
    'email' => 'required|email',
    'password' => 'required|min:6|confirmed',
    'nominal' => 'required|numeric',
]);
$t->assert('Validator passes on valid input', $v1->passes() === true);

$v2 = Validator::make([
    'email' => 'invalid-email',
    'password' => '123',
    'password_confirmation' => 'mismatch',
], [
    'email' => 'required|email',
    'password' => 'required|min:6|confirmed',
]);
$t->assert('Validator fails on invalid email', isset($v2->errors()['email']));
$t->assert('Validator fails on min length', isset($v2->errors()['password']));

// -----------------------------------------------------------------------------
// 5. Custom Router & Middleware Pipeline
// -----------------------------------------------------------------------------
echo "\nTest Suite 5: Custom Router\n";
Router::clear();

Router::get('/test-get', function () {
    return Response::html('OK GET');
});

Router::post('/test-post', function (Request $req) {
    return Response::json(['name' => $req->input('name')]);
});

Router::get('/rooms/{id}', function (Request $req) {
    return Response::json(['room_id' => $req->param('id')]);
});

// Dispatch GET
$res1 = Router::dispatch(new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test-get']));
$t->assert('Router dispatches simple GET route', $res1->getStatusCode() === 200 && $res1->getContent() === 'OK GET');

// Dispatch Dynamic Param
$res2 = Router::dispatch(new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/rooms/101']));
$t->assert('Router captures dynamic route parameters', str_contains($res2->getContent(), '"room_id":"101"'));

// Dispatch 404
$res404 = Router::dispatch(new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/non-existent-page']));
$t->assert('Router returns 404 on undefined route', $res404->getStatusCode() === 404);

// Dispatch 405 Method Not Allowed
$res405 = Router::dispatch(new Request([], [], ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/test-get']));
$t->assert('Router returns 405 on method mismatch', $res405->getStatusCode() === 405);

// -----------------------------------------------------------------------------
// 6. View Rendering & HTML Escaping
// -----------------------------------------------------------------------------
echo "\nTest Suite 6: View Engine & XSS Escaping\n";
$escaped = e("<script>alert('xss')</script>");
$t->assert('Helper e() escapes HTML entities', $escaped === '&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;');

$homeView = View::render('home', ['title' => 'Test Home'], 'layouts/main');
$t->assert('View renders layout and home view without error', $homeView->getStatusCode() === 200 && str_contains($homeView->getContent(), 'Kost Management System'));

// -----------------------------------------------------------------------------
// 7. Authentication & Rate Limiting
// -----------------------------------------------------------------------------
echo "\nTest Suite 7: Authentication & Rate Limiting\n";
$authService = new AuthService();

// Test rate limiting simulation
$testIp = '192.168.1.99';
$testEmail = 'fake_bruteforce@kost.com';
$key = $authService->getThrottleKey($testEmail, $testIp);

for ($i = 0; $i < 5; $i++) {
    $authService->incrementAttempts($key);
}

$t->assert('Auth rate limiter detects too many attempts', $authService->isTooManyAttempts($key) === true);

$blockedAttempt = $authService->attempt($testEmail, 'wrong_pass', $testIp);
$t->assert('Auth attempt returns rate limit message when throttled', $blockedAttempt['success'] === false && str_contains($blockedAttempt['error'], 'Terlalu banyak percobaan'));

$authService->clearAttempts($key);
$t->assert('Auth rate limiter clear attempts works', $authService->isTooManyAttempts($key) === false);

// Test User Model methods
$ownerUser = new User(['id' => 1, 'name' => 'Bapak Kos', 'email' => 'owner@kost.com', 'role' => 'owner']);
$adminUser = new User(['id' => 2, 'name' => 'Admin Kost', 'email' => 'admin@kost.com', 'role' => 'admin', 'permissions' => ['verify_payments']]);
$tenantUser = new User(['id' => 3, 'name' => 'Penyewa 1', 'email' => 'tenant@kost.com', 'role' => 'tenant']);

$t->assert('User::isOwner works', $ownerUser->isOwner() === true && $adminUser->isOwner() === false);
$t->assert('User::isAdmin works', $adminUser->isAdmin() === true && $tenantUser->isAdmin() === false);
$t->assert('User::isTenant works', $tenantUser->isTenant() === true && $ownerUser->isTenant() === false);
$t->assert('Owner automatically has all permissions', $ownerUser->hasPermission('any_random_perm') === true);
$t->assert('Admin has specific assigned permissions', $adminUser->hasPermission('verify_payments') === true && $adminUser->hasPermission('delete_database') === false);

// -----------------------------------------------------------------------------
// 8. Authorization Middleware (Owner, Admin, Tenant)
// -----------------------------------------------------------------------------
echo "\nTest Suite 8: Authorization Middleware\n";
Router::clear();

Router::group([
    'prefix' => '/owner',
    'middleware' => [AuthMiddleware::class, new RoleMiddleware(['owner'])],
], function () {
    Router::get('/secret', function () {
        return Response::html('OWNER SECRET GRANTED');
    });
});

// Test unauthenticated access -> redirects to /login
Session::remove('user');
$unauthReq = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/owner/secret']);
$unauthRes = Router::dispatch($unauthReq);
$t->assert('Unauthenticated user is redirected to /login', $unauthRes->getStatusCode() === 302 && Session::has('intended_url'));

// Test tenant user accessing owner secret -> 403 Forbidden
$authService->login($tenantUser);
$tenantReq = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/owner/secret']);
$tenantRes = Router::dispatch($tenantReq);
$t->assert('Tenant user is denied (403) from Owner area', $tenantRes->getStatusCode() === 403);

// Test owner user accessing owner secret -> 200 OK
$authService->login($ownerUser);
$ownerReq = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/owner/secret']);
$ownerRes = Router::dispatch($ownerReq);
$t->assert('Owner user is granted (200) access to Owner area', $ownerRes->getStatusCode() === 200 && str_contains($ownerRes->getContent(), 'OWNER SECRET GRANTED'));

// -----------------------------------------------------------------------------
// 9. Logger & Sensitive Context Redaction
// -----------------------------------------------------------------------------
echo "\nTest Suite 9: Application Logger\n";
$logTestPath = BASE_PATH . '/storage/logs/test.log';
Logger::setLogPath($logTestPath);
Logger::info("Uji coba logging aman", [
    'user_id' => 123,
    'password' => 'rahasia12345',
    'token' => 'super_secret_token',
]);

$logContent = file_get_contents($logTestPath);
$t->assert('Logger records log entry', str_contains($logContent, 'Uji coba logging aman'));
$t->assert('Logger redacts password from context', !str_contains($logContent, 'rahasia12345') && str_contains($logContent, '***REDACTED***'));
$t->assert('Logger redacts secret token from context', !str_contains($logContent, 'super_secret_token'));
@unlink($logTestPath);

// Summary
$t->summary();
