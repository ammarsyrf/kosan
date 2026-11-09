<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Config::env($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        return View::render($view, $data, $layout);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }
}

if (!function_exists('json_response')) {
    function json_response(mixed $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return Session::all();
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return Session::get('user');
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return Session::has('user');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $baseUrl = rtrim((string)config('app.url', ''), '/');
        return $baseUrl . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $baseUrl = rtrim((string)config('app.url', ''), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

