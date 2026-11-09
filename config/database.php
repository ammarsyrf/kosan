<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'default' => Config::env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'host' => Config::env('DB_HOST', '127.0.0.1'),
            'port' => (int)Config::env('DB_PORT', 3306),
            'database' => Config::env('DB_DATABASE', 'kost_management'),
            'username' => Config::env('DB_USERNAME', 'root'),
            'password' => Config::env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],
];

