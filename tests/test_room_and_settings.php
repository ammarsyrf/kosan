<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repositories\RoomRepository;

$db = Database::connect();
$roomRepo = new RoomRepository($db);

// 1. Verify Room 101 has floor, size, photo_url
$room101 = $roomRepo->findById(1);
assert($room101 !== null, 'Room 101 must exist');
assert($room101->floor === 1, 'Room 101 floor must be 1');
assert($room101->size === '3x4 m²', 'Room 101 size must be 3x4 m²');
echo "[PASS] Room 101 properties: Floor {$room101->floor}, Size {$room101->size}, Photo {$room101->photo_url}\n";

// 2. Test updating room 103
$room103 = $roomRepo->findById(3);
assert($room103 !== null, 'Room 103 must exist');
$roomRepo->update(3, [
    'room_number' => $room103->room_number,
    'floor' => 1,
    'name' => 'Kamar 103 (Deluxe Modern)',
    'size' => '3.5x4 m²',
    'current_price' => 1550000.0,
    'photo_url' => 'images/rooms/room-103.jpg',
    'status' => 'available',
    'description' => 'Kamar lantai 1 dengan pencahayaan alami & ventilasi lega.',
    'notes' => null,
]);
$updated103 = $roomRepo->findById(3);
assert($updated103->name === 'Kamar 103 (Deluxe Modern)', 'Room 103 name must be updated');
assert($updated103->size === '3.5x4 m²', 'Room 103 size must be updated');
echo "[PASS] Room 103 successfully updated in DB: {$updated103->name}, {$updated103->size}\n";

// Restore room 103 name
$roomRepo->update(3, [
    'room_number' => $room103->room_number,
    'floor' => 1,
    'name' => 'Kamar 103 (Standard)',
    'size' => '3x4 m²',
    'current_price' => 1500000.0,
    'photo_url' => 'images/rooms/room-103.jpg',
    'status' => 'available',
    'description' => 'Kamar lantai 1, dekat pintu masuk',
    'notes' => null,
]);

// 3. Test settings table
$stmt = $db->prepare("
    INSERT INTO `settings` (`key`, `value`, `group`, `description`)
    VALUES (:key, :value, :group, :description)
    ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
");
$stmt->execute([
    'key' => 'landing_hero_badge',
    'value' => 'Kost Management Monolith • High-Performance Edition',
    'group' => 'landing',
    'description' => 'Teks Badge Hero Beranda',
]);
$stmt->execute([
    'key' => 'landing_hero_title',
    'value' => "Otomasi Penuh Kos Modern\nTanpa Repot, Rapi, & Real-Time",
    'group' => 'landing',
    'description' => 'Judul Headline Utama Hero',
]);
$val = setting('landing_hero_badge');
assert($val === 'Kost Management Monolith • High-Performance Edition', 'Setting landing_hero_badge must match');
echo "[PASS] Settings helper works correctly: '{$val}'\n";

echo "\n[ALL TESTS PASSED] Rooms & Landing Settings are fully dynamic and verified!\n";
