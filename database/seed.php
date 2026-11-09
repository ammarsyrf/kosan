<?php

declare(strict_types=1);

// Database Seeder — Kost Management System

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

Config::loadEnv(BASE_PATH . '/.env');

echo "==================================================\n";
echo "  Kost Management System — Database Seeder\n";
echo "==================================================\n\n";

try {
    $pdo = Database::connect();
    echo "[INFO] Terhubung ke database: " . config('database.connections.mysql.database') . "\n";
} catch (\Throwable $e) {
    echo "[ERROR] Gagal terhubung ke database: " . $e->getMessage() . "\n";
    exit(1);
}

Database::transaction(function (PDO $pdo) {
    // 1. Seed Roles
    echo "[SEED] Roles...\n";
    $roles = [
        ['name' => 'owner', 'label' => 'Pemilik Kos (Super Admin)'],
        ['name' => 'admin', 'label' => 'Admin Operasional'],
        ['name' => 'tenant', 'label' => 'Penghuni Kos'],
    ];

    $roleStmt = $pdo->prepare("INSERT INTO `roles` (`name`, `label`) VALUES (:name, :label) ON DUPLICATE KEY UPDATE `label` = VALUES(`label`)");
    foreach ($roles as $r) {
        $roleStmt->execute($r);
    }

    $roleIds = $pdo->query("SELECT `name`, `id` FROM `roles`")->fetchAll(PDO::FETCH_KEY_PAIR);

    // 2. Seed Permissions
    echo "[SEED] Permissions...\n";
    $permissions = [
        ['name' => 'manage_rooms', 'label' => 'Mengelola Kamar & Fasilitas'],
        ['name' => 'manage_tenants', 'label' => 'Mengelola Data Penghuni'],
        ['name' => 'manage_bookings', 'label' => 'Mengelola Booking'],
        ['name' => 'manage_contracts', 'label' => 'Mengelola Kontrak Sewa'],
        ['name' => 'manage_invoices', 'label' => 'Mengelola Tagihan / Invoice'],
        ['name' => 'verify_payments', 'label' => 'Verifikasi Pembayaran'],
        ['name' => 'manage_expenses', 'label' => 'Mencatat Pengeluaran'],
        ['name' => 'manage_maintenance', 'label' => 'Mengelola Laporan Maintenance'],
        ['name' => 'manage_announcements', 'label' => 'Membuat Pengumuman'],
        ['name' => 'manage_settings', 'label' => 'Mengubah Pengaturan Sistem'],
        ['name' => 'view_reports', 'label' => 'Melihat Laporan Keuangan & Okupansi'],
    ];

    $permStmt = $pdo->prepare("INSERT INTO `permissions` (`name`, `label`) VALUES (:name, :label) ON DUPLICATE KEY UPDATE `label` = VALUES(`label`)");
    foreach ($permissions as $p) {
        $permStmt->execute($p);
    }

    $permIds = $pdo->query("SELECT `name`, `id` FROM `permissions`")->fetchAll(PDO::FETCH_KEY_PAIR);

    // Assign all operational permissions to Admin role
    $assignPermStmt = $pdo->prepare("INSERT IGNORE INTO `permission_role` (`permission_id`, `role_id`) VALUES (?, ?)");
    foreach ($permIds as $pName => $pId) {
        $assignPermStmt->execute([$pId, $roleIds['admin']]);
        $assignPermStmt->execute([$pId, $roleIds['owner']]);
    }

    // 3. Seed Users (Owner & Admin)
    echo "[SEED] Default Users (Owner & Admin)...\n";
    $defaultPasswordHash = password_hash('password123', PASSWORD_DEFAULT);

    $userStmt = $pdo->prepare("
        INSERT INTO `users` (`name`, `email`, `password`, `phone`, `status`)
        VALUES (:name, :email, :password, :phone, 'active')
        ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `password` = VALUES(`password`)
    ");

    $userStmt->execute([
        'name' => 'Owner Kos',
        'email' => 'owner@kost.com',
        'password' => $defaultPasswordHash,
        'phone' => '081234567890',
    ]);
    $ownerId = $pdo->query("SELECT `id` FROM `users` WHERE `email` = 'owner@kost.com'")->fetchColumn();

    $userStmt->execute([
        'name' => 'Admin Operasional',
        'email' => 'admin@kost.com',
        'password' => $defaultPasswordHash,
        'phone' => '081298765432',
    ]);
    $adminId = $pdo->query("SELECT `id` FROM `users` WHERE `email` = 'admin@kost.com'")->fetchColumn();

    // Map role_user
    $roleUserStmt = $pdo->prepare("INSERT IGNORE INTO `role_user` (`user_id`, `role_id`) VALUES (?, ?)");
    $roleUserStmt->execute([$ownerId, $roleIds['owner']]);
    $roleUserStmt->execute([$adminId, $roleIds['admin']]);

    // 4. Seed Facilities
    echo "[SEED] Room Facilities...\n";
    $facilities = [
        ['name' => 'Air Conditioner (AC)', 'icon' => 'wind', 'description' => 'AC hemat listrik 1/2 PK'],
        ['name' => 'Kamar Mandi Dalam', 'icon' => 'bath', 'description' => 'Kloset duduk & shower'],
        ['name' => 'WiFi Berkecepatan Tinggi', 'icon' => 'wifi', 'description' => 'Internet fiber optic unlimited'],
        ['name' => 'Kasur Springbed', 'icon' => 'bed', 'description' => 'Kasur ukuran 120x200 lengkap dengan sprei'],
        ['name' => 'Lemari Pakaian', 'icon' => 'archive', 'description' => 'Lemari 2 pintu dengan cermin'],
        ['name' => 'Meja & Kursi Belajar', 'icon' => 'armchair', 'description' => 'Set meja kerja minimalis'],
        ['name' => 'Water Heater', 'icon' => 'flame', 'description' => 'Pemanas air otomatis'],
    ];

    $facStmt = $pdo->prepare("INSERT INTO `room_facilities` (`name`, `icon`, `description`) VALUES (:name, :icon, :description) ON DUPLICATE KEY UPDATE `description` = VALUES(`description`)");
    foreach ($facilities as $f) {
        $facStmt->execute($f);
    }
    $facilityList = $pdo->query("SELECT `id` FROM `room_facilities`")->fetchAll(PDO::FETCH_COLUMN);

    // 5. Seed Rooms (< 10 rooms)
    echo "[SEED] Master Rooms (8 kamar)...\n";
    $rooms = [
        ['room_number' => '101', 'name' => 'Kamar 101 (Standard)', 'current_price' => 1500000.00, 'description' => 'Kamar lantai 1, sirkulasi udara lancar'],
        ['room_number' => '102', 'name' => 'Kamar 102 (Standard)', 'current_price' => 1500000.00, 'description' => 'Kamar lantai 1, dekat area dapur'],
        ['room_number' => '103', 'name' => 'Kamar 103 (Standard)', 'current_price' => 1500000.00, 'description' => 'Kamar lantai 1, dekat pintu masuk'],
        ['room_number' => '104', 'name' => 'Kamar 104 (Deluxe)', 'current_price' => 1700000.00, 'description' => 'Kamar lantai 1 lebih luas'],
        ['room_number' => '201', 'name' => 'Kamar 201 (Standard)', 'current_price' => 1500000.00, 'description' => 'Kamar lantai 2, tenang & nyaman'],
        ['room_number' => '202', 'name' => 'Kamar 202 (Deluxe)', 'current_price' => 1700000.00, 'description' => 'Kamar lantai 2 dengan jendela luas'],
        ['room_number' => '203', 'name' => 'Kamar 203 (VIP)', 'current_price' => 1850000.00, 'description' => 'Kamar lantai 2, water heater & smart TV'],
        ['room_number' => '204', 'name' => 'Kamar 204 (Executive)', 'current_price' => 2000000.00, 'description' => 'Kamar lantai 2 ukuran terbesar dengan balkon privat'],
    ];

    $roomStmt = $pdo->prepare("
        INSERT INTO `rooms` (`room_number`, `name`, `current_price`, `description`, `status`)
        VALUES (:room_number, :name, :current_price, :description, 'available')
        ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `current_price` = VALUES(`current_price`)
    ");

    $assignFacStmt = $pdo->prepare("INSERT IGNORE INTO `room_facility_assignments` (`room_id`, `facility_id`) VALUES (?, ?)");

    foreach ($rooms as $rm) {
        $roomStmt->execute($rm);
        $roomId = $pdo->query("SELECT `id` FROM `rooms` WHERE `room_number` = '{$rm['room_number']}'")->fetchColumn();

        // Assign standard facilities to all rooms
        foreach ($facilityList as $facId) {
            $assignFacStmt->execute([$roomId, $facId]);
        }
    }

    // 6. Seed Expense Categories
    echo "[SEED] Expense Categories...\n";
    $expenseCategories = [
        ['name' => 'Perbaikan & Servis', 'description' => 'Perbaikan AC, pipa bocor, kelistrikan, dsb.'],
        ['name' => 'Kebersihan', 'description' => 'Iuran sampah, alat kebersihan, jasa bersih lingkungan'],
        ['name' => 'Listrik & Air Operasional', 'description' => 'Tagihan PLN & PDAM / token bersama'],
        ['name' => 'Internet / WiFi', 'description' => 'Tagihan internet bulanan kos'],
        ['name' => 'Perlengkapan & Operasional', 'description' => 'Gas dapur, air minum galon, bohlam, dsb.'],
    ];

    $expCatStmt = $pdo->prepare("INSERT INTO `expense_categories` (`name`, `description`) VALUES (:name, :description) ON DUPLICATE KEY UPDATE `description` = VALUES(`description`)");
    foreach ($expenseCategories as $ec) {
        $expCatStmt->execute($ec);
    }

    // 7. Seed Maintenance Categories
    echo "[SEED] Maintenance Categories...\n";
    $maintenanceCategories = [
        ['name' => 'AC & Pendingin Ruangan', 'description' => 'AC kurang dingin, bocor, atau mati'],
        ['name' => 'Sanitasi & Saluran Air', 'description' => 'Pipa tersumbat, kran rusak, shower mampet'],
        ['name' => 'Kelistrikan & Penerangan', 'description' => 'Saklar rusak, stopkontak, bohlam putus'],
        ['name' => 'Furnitur & Kusen Pintu', 'description' => 'Kunci rusak, engsel patah, lemari goyang'],
        ['name' => 'Kebersihan & Hama', 'description' => 'Rayap, kecoa, tikus, atau kotoran luar biasa'],
        ['name' => 'Lain-lain', 'description' => 'Keluhan dan kerusakan lainnya'],
    ];

    $maintCatStmt = $pdo->prepare("INSERT INTO `maintenance_categories` (`name`, `description`) VALUES (:name, :description) ON DUPLICATE KEY UPDATE `description` = VALUES(`description`)");
    foreach ($maintenanceCategories as $mc) {
        $maintCatStmt->execute($mc);
    }

    // 8. Seed Notification Templates
    echo "[SEED] Notification Templates...\n";
    $templates = [
        [
            'code' => 'REMINDER_H7',
            'name' => 'Pengingat Tagihan H-7',
            'content' => "Halo {tenant_name},\n\nMengingatkan tagihan sewa kamar {room_number} untuk periode {period} sebesar {total_amount} akan jatuh tempo pada {due_date} (7 hari lagi).\n\nSisa tagihan: {balance}.\nSilakan melakukan pembayaran tepat waktu. Terima kasih!\n\n— {business_name}",
        ],
        [
            'code' => 'REMINDER_H3',
            'name' => 'Pengingat Tagihan H-3',
            'content' => "Halo {tenant_name},\n\nMengingatkan tagihan sewa kamar {room_number} sebesar {total_amount} akan jatuh tempo dalam 3 hari pada {due_date}.\n\nSisa tagihan: {balance}.\nTerima kasih atas kerjasamanya!\n\n— {business_name}",
        ],
        [
            'code' => 'REMINDER_H1',
            'name' => 'Pengingat Tagihan H-1 (Besok)',
            'content' => "Halo {tenant_name},\n\nMengingatkan tagihan sewa kamar {room_number} sebesar {total_amount} akan jatuh tempo BESOK ({due_date}).\n\nSisa tagihan: {balance}.\nMohon segera melakukan pembayaran. Terima kasih!\n\n— {business_name}",
        ],
        [
            'code' => 'REMINDER_DUE',
            'name' => 'Pengingat Tagihan Hari Ini',
            'content' => "Halo {tenant_name},\n\nHari ini adalah tanggal jatuh tempo tagihan sewa kamar {room_number} sebesar {total_amount}.\n\nSisa tagihan: {balance}.\nMohon segera konfirmasi atau unggah bukti transfer di portal penghuni. Terima kasih!\n\n— {business_name}",
        ],
        [
            'code' => 'REMINDER_OVERDUE',
            'name' => 'Tagihan Melewati Jatuh Tempo (H+1)',
            'content' => "Halo {tenant_name},\n\nTagihan sewa kamar {room_number} telah melewati tanggal jatuh tempo ({due_date}).\n\nSisa saldo tagihan yang belum terbayar: {balance}.\nMohon segera diselesaikan atau hubungi Admin jika ada kendala. Terima kasih.\n\n— {business_name}",
        ],
    ];

    $notifStmt = $pdo->prepare("INSERT INTO `notification_templates` (`code`, `name`, `content`) VALUES (:code, :name, :content) ON DUPLICATE KEY UPDATE `content` = VALUES(`content`)");
    foreach ($templates as $t) {
        $notifStmt->execute($t);
    }

    // 9. Seed Settings
    echo "[SEED] General Settings...\n";
    $settings = [
        ['key' => 'business_name', 'value' => 'Kost Management Personal', 'group' => 'general', 'description' => 'Nama usaha kos'],
        ['key' => 'business_address', 'value' => 'Jl. Kost Nyaman No. 10', 'group' => 'general', 'description' => 'Alamat usaha kos'],
        ['key' => 'business_phone', 'value' => '081234567890', 'group' => 'general', 'description' => 'Nomor WhatsApp admin/owner'],
        ['key' => 'booking_duration_hours', 'value' => '24', 'group' => 'booking', 'description' => 'Durasi default kedaluwarsa booking (jam)'],
        ['key' => 'bank_name', 'value' => 'BCA', 'group' => 'payment', 'description' => 'Nama bank transfer'],
        ['key' => 'bank_account_number', 'value' => '1234567890', 'group' => 'payment', 'description' => 'Nomor rekening transfer'],
        ['key' => 'bank_account_holder', 'value' => 'Owner Kos', 'group' => 'payment', 'description' => 'Atas nama rekening'],
    ];

    $setStmt = $pdo->prepare("INSERT INTO `settings` (`key`, `value`, `group`, `description`) VALUES (:key, :value, :group, :description) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    foreach ($settings as $s) {
        $setStmt->execute($s);
    }
});

echo "\n[SUCCESS] Seluruh master data & seeders berhasil dimasukkan ke database!\n";
echo "Akun Default:\n";
echo "  - Owner: owner@kost.com | password123\n";
echo "  - Admin: admin@kost.com | password123\n";
