<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        $user = Session::get('user');

        if (!$user || !isset($user['id'])) {
            if ($request->isJson()) {
                return Response::json([
                    'success' => false,
                    'error' => 'Silakan masuk terlebih dahulu untuk mengakses layanan ini.',
                ], 401);
            }

            Session::set('intended_url', $request->uri());
            Session::flash('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
            return Response::redirect('/login');
        }

        return $next($request);
    }
}

