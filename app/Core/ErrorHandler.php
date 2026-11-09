<?php

declare(strict_types=1);

namespace App\Core;

use ErrorException;
use Throwable;

class ErrorHandler
{
    public static function register(): void
    {
        error_reporting(E_ALL);

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(Throwable $e): void
    {
        Logger::error($e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ]);

        $debug = (bool)Config::get('app.debug', false);
        $isJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

        if (!headers_sent()) {
            http_response_code(500);
        }

        if ($isJson) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => false,
                'error' => $debug ? $e->getMessage() : 'Terjadi kesalahan pada server.',
                'code' => 500,
                'file' => $debug ? $e->getFile() : null,
                'line' => $debug ? $e->getLine() : null,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        if ($debug) {
            self::renderDebugPage($e);
        } else {
            View::renderError(500, 'Terjadi kesalahan sistem internal. Tim kami sedang menanganinya.')->send();
        }
        exit;
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleException(new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }

    private static function renderDebugPage(Throwable $e): void
    {
        $class = htmlspecialchars(get_class($e));
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile());
        $line = $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString());

        echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Unhandled Exception: {$class}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 p-6 sm:p-10 font-sans">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="border-b border-slate-700 pb-4">
            <span class="inline-block px-3 py-1 bg-red-600/30 text-red-400 border border-red-500/40 text-xs font-semibold rounded-full uppercase tracking-wider mb-2">500 Server Error</span>
            <h1 class="text-2xl sm:text-3xl font-bold text-white break-words">{$class}</h1>
            <p class="text-lg text-red-300 mt-2 font-mono break-words">{$message}</p>
            <p class="text-sm text-slate-400 mt-2 font-mono">Location: <span class="text-slate-200">{$file}</span>:{$line}</p>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-300 mb-2">Stack Trace</h2>
            <pre class="bg-slate-950 p-4 rounded-lg border border-slate-800 text-xs sm:text-sm font-mono text-slate-300 overflow-x-auto whitespace-pre-wrap">{$trace}</pre>
        </div>
    </div>
</body>
</html>
HTML;
    }
}

