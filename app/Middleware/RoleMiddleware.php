<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class RoleMiddleware implements Middleware
{
    private array $allowedRoles;

    public function __construct(array|string $roles = [])
    {
        $this->allowedRoles = is_array($roles) ? $roles : func_get_args();
    }

    public function handle(Request $request, callable $next): Response
    {
        $user = Session::get('user');

        if (!$user) {
            return Response::redirect('/login');
        }

        $userRole = strtolower((string)($user['role'] ?? ''));

        if (!empty($this->allowedRoles)) {
            $allowed = array_map('strtolower', $this->allowedRoles);
            if (!in_array($userRole, $allowed, true)) {
                if ($request->isJson()) {
                    return Response::json([
                        'success' => false,
                        'error' => 'Akses ditolak. Anda tidak memiliki izin untuk tindakan ini.',
                    ], 403);
                }

                return View::renderError(403, 'Akses ditolak: Anda tidak memiliki izin untuk halaman ini.');
            }
        }

        return $next($request);
    }
}

