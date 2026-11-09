<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    private static string $basePath = '';

    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, '/');
    }

    private static function getBasePath(): string
    {
        if (self::$basePath === '') {
            self::$basePath = dirname(__DIR__, 2) . '/resources/views';
        }
        return self::$basePath;
    }

    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        $viewFile = self::resolvePath($view);
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file tidak ditemukan: [{$view}]");
        }

        // Render child view into output buffer
        $content = self::evaluateView($viewFile, $data);

        // If layout is specified and exists, render layout with $content
        if ($layout !== null) {
            $layoutFile = self::resolvePath($layout);
            if (file_exists($layoutFile)) {
                $layoutData = array_merge($data, ['content' => $content]);
                $content = self::evaluateView($layoutFile, $layoutData);
            }
        }

        return Response::html($content);
    }

    public static function renderPartial(string $view, array $data = []): string
    {
        $viewFile = self::resolvePath($view);
        if (!file_exists($viewFile)) {
            throw new RuntimeException("Partial view tidak ditemukan: [{$view}]");
        }

        return self::evaluateView($viewFile, $data);
    }

    public static function renderError(int $code, string $message = ''): Response
    {
        $errorView = "errors/{$code}";
        $defaultFile = self::getBasePath() . "/errors/{$code}.php";

        if (file_exists($defaultFile)) {
            return self::render($errorView, ['code' => $code, 'message' => $message], 'layouts/main')
                ->status($code);
        }

        // Fallback HTML if error view file is missing
        $safeMessage = htmlspecialchars($message ?: "Terjadi kesalahan (HTTP {$code})", ENT_QUOTES, 'UTF-8');
        $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Error {$code}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen text-gray-800">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center">
        <h1 class="text-6xl font-bold text-gray-900 mb-2">{$code}</h1>
        <p class="text-gray-600 mb-6">{$safeMessage}</p>
        <a href="/" class="inline-block px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">Kembali ke Beranda</a>
    </div>
</body>
</html>
HTML;
        return Response::html($html, $code);
    }

    private static function resolvePath(string $view): string
    {
        // Sanitize view name to prevent directory traversal
        $cleaned = str_replace(['../', '..\\'], '', $view);
        $path = self::getBasePath() . '/' . ltrim($cleaned, '/') . '.php';
        return $path;
    }

    private static function evaluateView(string $filePath, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();

        try {
            require $filePath;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string)ob_get_clean();
    }
}

