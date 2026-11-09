<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (!$token || !is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = Session::get(self::SESSION_KEY);
        if (!$sessionToken || !$token || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function validateRequest(Request $request): bool
    {
        // Safe methods do not require CSRF token
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $token = $request->post('_csrf_token')
            ?? $request->header('X-CSRF-TOKEN')
            ?? $request->header('X-XSRF-TOKEN');

        return self::validate($token);
    }
}

