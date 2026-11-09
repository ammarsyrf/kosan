<?php

declare(strict_types=1);

// CLI Cron Script: Booking Expiration Runner
// Runs periodically (e.g. hourly or minutely) to expire pending bookings that exceeded expires_at.

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Logger;
use App\Services\BookingService;

Config::loadEnv(BASE_PATH . '/.env');

$lockFile = BASE_PATH . '/storage/cache/booking-expiration.lock';
$lockFp = fopen($lockFile, 'c');

if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "[SKIP] Process is already running.\n";
    exit(0);
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starting booking expiration check...\n";

    $bookingService = new BookingService();
    $expiredCount = $bookingService->expirePendingBookings();

    echo "[INFO] Total bookings expired: {$expiredCount}\n";
    if ($expiredCount > 0) {
        Logger::info("Cron booking-expiration: Successfully expired {$expiredCount} pending bookings.");
    }

    echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
} catch (\Throwable $e) {
    Logger::error("Cron booking-expiration failed: " . $e->getMessage());
    echo "[ERROR] " . $e->getMessage() . "\n";
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
    @unlink($lockFile);
}

