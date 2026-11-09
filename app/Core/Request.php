<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $cookie;
    private array $files;
    private ?array $json = null;
    private array $params = [];

    public function __construct(
        array $get = [],
        array $post = [],
        array $server = [],
        array $cookie = [],
        array $files = []
    ) {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->cookie = $cookie;
        $this->files = $files;
    }

    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            if (isset($this->post['_method'])) {
                return strtoupper((string)$this->post['_method']);
            }
            if (isset($this->server['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                return strtoupper((string)$this->server['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }

        return $method;
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return '/' . trim($path ?: '/', '/');
    }

    public function path(): string
    {
        return $this->uri();
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->get;
        }
        return $this->get[$key] ?? $default;
    }

    public function get(?string $key = null, mixed $default = null): mixed
    {
        return $this->query($key, $default);
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        $all = $this->all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public function all(): array
    {
        $json = $this->json();
        return array_merge($this->get, $this->post, $json);
    }

    public function json(): array
    {
        if ($this->json !== null) {
            return $this->json;
        }

        if ($this->isJson()) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $this->json = is_array($decoded) ? $decoded : [];
        } else {
            $this->json = [];
        }

        return $this->json;
    }

    public function isJson(): bool
    {
        $contentType = $this->header('Content-Type') ?? '';
        return str_contains(strtolower($contentType), 'application/json');
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        $file = $this->file($key);
        return $file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
    }

    public function header(string $key, ?string $default = null): ?string
    {
        $normalizedKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        if (isset($this->server[$normalizedKey])) {
            return (string)$this->server[$normalizedKey];
        }

        // Special headers like CONTENT_TYPE, CONTENT_LENGTH
        $directKey = strtoupper(str_replace('-', '_', $key));
        if (isset($this->server[$directKey])) {
            return (string)$this->server[$directKey];
        }

        return $default;
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['REMOTE_ADDR']
            ?? '127.0.0.1';
    }

    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || ($this->server['SERVER_PORT'] ?? '') == 443;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function params(): array
    {
        return $this->params;
    }
}

