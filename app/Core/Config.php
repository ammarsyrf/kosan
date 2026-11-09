<?php

declare(strict_types=1);

namespace App\Core;

class Config
{
    private static array $config = [];
    private static array $env = [];
    private static bool $envLoaded = false;

    public static function loadEnv(string $path): void
    {
        if (self::$envLoaded || !file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Strip enclosing quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            $resolvedValue = match (strtolower($value)) {
                'true', '(true)' => true,
                'false', '(false)' => false,
                'null', '(null)' => null,
                'empty', '(empty)' => '',
                default => $value,
            };

            self::$env[$key] = $resolvedValue;
            $_ENV[$key] = $resolvedValue;
            putenv("{$key}={$value}");
        }

        self::$envLoaded = true;
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        if (!self::$envLoaded) {
            self::loadEnv(dirname(__DIR__, 2) . '/.env');
        }

        return self::$env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);

        if (!isset(self::$config[$file])) {
            $filePath = dirname(__DIR__, 2) . "/config/{$file}.php";
            if (file_exists($filePath)) {
                self::$config[$file] = require $filePath;
            } else {
                self::$config[$file] = [];
            }
        }

        $value = self::$config[$file];
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);

        if (!isset(self::$config[$file])) {
            self::$config[$file] = [];
        }

        $current = &self::$config[$file];
        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }
        $current = $value;
    }
}

