<?php

declare(strict_types=1);

namespace App\Core;

class Logger
{
    private static ?string $logPath = null;

    public static function setLogPath(string $path): void
    {
        self::$logPath = $path;
    }

    private static function getLogPath(): string
    {
        if (self::$logPath !== null) {
            return self::$logPath;
        }

        $dir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        return $dir . '/app-' . date('Y-m-d') . '.log';
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $sanitizedContext = self::sanitize($context);
        $contextStr = !empty($sanitizedContext) ? ' ' . json_encode($sanitizedContext, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $timestamp = date('Y-m-d H:i:s');
        $formatted = sprintf("[%s] [%s] %s%s%s", $timestamp, strtoupper($level), $message, $contextStr, PHP_EOL);

        @file_put_contents(self::getLogPath(), $formatted, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function audit(string $actor, string $action, string $entity, mixed $entityId = null, array $metadata = []): void
    {
        $message = sprintf("Audit: User [%s] performed [%s] on [%s:%s]", $actor, $action, $entity, (string)$entityId);
        self::log('AUDIT', $message, $metadata);
    }

    private static function sanitize(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'raw_token', 'secret', 'api_key', 'authorization'];
        $clean = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $clean[$key] = self::sanitize($value);
            } elseif (in_array(strtolower((string)$key), $sensitiveKeys, true)) {
                $clean[$key] = '***REDACTED***';
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}

