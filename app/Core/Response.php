<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    private string $content;
    private int $statusCode;
    private array $headers = [];
    private ?string $filePath = null;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function html(string $html, int $statusCode = 200, array $headers = []): self
    {
        $headers['Content-Type'] = $headers['Content-Type'] ?? 'text/html; charset=UTF-8';
        return new self($html, $statusCode, $headers);
    }

    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json; charset=UTF-8';
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new self($encoded ?: '{}', $statusCode, $headers);
    }

    public static function redirect(string $url, int $statusCode = 302, array $headers = []): self
    {
        $headers['Location'] = $url;
        return new self('', $statusCode, $headers);
    }

    public static function download(string $filePath, ?string $filename = null, array $headers = []): self
    {
        $response = new self('', 200, $headers);
        $response->filePath = $filePath;
        $filename = $filename ?? basename($filePath);

        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Type', mime_content_type($filePath) ?: 'application/octet-stream');
        $response->header('Content-Disposition', 'attachment; filename="' . str_replace('"', '', $filename) . '"');
        $response->header('Content-Length', (string)filesize($filePath));
        $response->header('Pragma', 'public');
        $response->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');

        return $response;
    }

    public static function file(string $filePath, ?string $mimeType = null): self
    {
        $response = new self('', 200);
        $response->filePath = $filePath;
        $mime = $mimeType ?: (mime_content_type($filePath) ?: 'application/octet-stream');

        $response->header('Content-Type', $mime);
        $response->header('Content-Length', (string)filesize($filePath));
        $response->header('Cache-Control', 'private, max-age=86400');

        return $response;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function status(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        if ($this->filePath !== null) {
            readfile($this->filePath);
            return;
        }

        echo $this->content;
    }
}

