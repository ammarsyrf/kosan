<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'name' => Config::env('APP_NAME', 'Kost Management System'),
    'env' => Config::env('APP_ENV', 'production'),
    'debug' => (bool)Config::env('APP_DEBUG', false),
    'url' => rtrim((string)Config::env('APP_URL', 'http://localhost:8000'), '/'),
    'key' => Config::env('APP_KEY', ''),
    'session_lifetime' => (int)Config::env('SESSION_LIFETIME', 7200),
    'upload_max_size' => (int)Config::env('UPLOAD_MAX_SIZE', 5242880),
];

