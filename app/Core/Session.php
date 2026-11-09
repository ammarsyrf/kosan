<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            self::checkTimeout();
            return;
        }

        if (!headers_sent()) {
            $lifetime = (int)Config::get('app.session_lifetime', 7200);
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        if (session_status() === PHP_SESSION_NONE) {
            if (@session_start()) {
                self::$started = true;
                self::checkTimeout();
            }
        } else {
            self::$started = true;
        }
    }

    private static function checkTimeout(): void
    {
        $lifetime = (int)Config::get('app.session_lifetime', 7200);
        $now = time();

        if (isset($_SESSION['_last_activity']) && ($now - (int)$_SESSION['_last_activity'] > $lifetime)) {
            self::destroy();
            return;
        }

        $_SESSION['_last_activity'] = $now;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        if (str_contains($key, '.')) {
            $array = $_SESSION;
            foreach (explode('.', $key) as $segment) {
                if (!is_array($array) || !array_key_exists($segment, $array)) {
                    return $default;
                }
                $array = $array[$segment];
            }
            return $array;
        }
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        if (str_contains($key, '.')) {
            $array = $_SESSION;
            foreach (explode('.', $key) as $segment) {
                if (!is_array($array) || !array_key_exists($segment, $array)) {
                    return false;
                }
                $array = $array[$segment];
            }
            return true;
        }
        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        self::start();
        if (str_contains($key, '.')) {
            $segments = explode('.', $key);
            $target = &$_SESSION;
            while (count($segments) > 1) {
                $seg = array_shift($segments);
                if (!isset($target[$seg]) || !is_array($target[$seg])) {
                    return;
                }
                $target = &$target[$seg];
            }
            unset($target[array_shift($segments)]);
            return;
        }
        unset($_SESSION[$key]);
    }

    public static function all(): array
    {
        self::start();
        return $_SESSION ?? [];
    }

    public static function regenerate(bool $deleteOldSession = true): bool
    {
        self::start();
        if (session_status() === PHP_SESSION_ACTIVE) {
            return @session_regenerate_id($deleteOldSession);
        }
        return false;
    }

    public static function flash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
        return $default;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (!headers_sent() && ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            @session_destroy();
            self::$started = false;
        }
    }
}
