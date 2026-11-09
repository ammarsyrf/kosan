<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Csrf::validateRequest($request)) {
            if ($request->isJson()) {
                return Response::json([
                    'success' => false,
                    'error' => 'Token CSRF tidak valid atau telah kedaluwarsa.',
                ], 419);
            }

            Session::flash('error', 'Sesi formulir Anda telah kedaluwarsa. Silakan coba lagi.');
            return View::renderError(419, 'Sesi keamanan formulir (CSRF) tidak valid atau telah kedaluwarsa.');
        }

        return $next($request);
    }
}

