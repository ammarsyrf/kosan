<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use PDO;

class SettingController extends Controller
{
    public function index(Request $request): Response
    {
        $db = Database::connect();
        $rows = $db->query("SELECT `key`, `value` FROM `settings`")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        return $this->render('admin/settings/index', [
            'title' => 'Pengaturan Beranda & Profil Kos',
            'settings' => $rows,
            'user' => Session::get('user'),
        ]);
    }

    public function update(Request $request): Response
    {
        $db = Database::connect();

        $keys = [
            'business_name' => ['group' => 'general', 'desc' => 'Nama Usaha / Properti Kos'],
            'business_phone' => ['group' => 'general', 'desc' => 'Nomor WhatsApp / Kontak Utama'],
            'business_address' => ['group' => 'general', 'desc' => 'Alamat Lengkap Kos'],
            'landing_hero_badge' => ['group' => 'landing', 'desc' => 'Teks Badge Hero Beranda'],
            'landing_hero_title' => ['group' => 'landing', 'desc' => 'Judul Headline Utama Hero'],
            'landing_hero_subtitle' => ['group' => 'landing', 'desc' => 'Deskripsi / Subtitle Hero Beranda'],
            'bank_name' => ['group' => 'payment', 'desc' => 'Nama Bank Pembayaran'],
            'bank_account_number' => ['group' => 'payment', 'desc' => 'Nomor Rekening Pembayaran'],
            'bank_account_holder' => ['group' => 'payment', 'desc' => 'Atas Nama Rekening'],
            'booking_duration_hours' => ['group' => 'booking', 'desc' => 'Batas Waktu Booking (Jam)'],
        ];

        try {
            $stmt = $db->prepare("
                INSERT INTO `settings` (`key`, `value`, `group`, `description`)
                VALUES (:key, :value, :group, :description)
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
            ");

            foreach ($keys as $k => $meta) {
                $val = trim((string)$request->post($k, ''));
                $stmt->execute([
                    'key' => $k,
                    'value' => $val,
                    'group' => $meta['group'],
                    'description' => $meta['desc'],
                ]);
            }

            Session::flash('success', 'Pengaturan sistem & konten beranda berhasil diperbarui secara real-time.');
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }

        return $this->redirect('/admin/settings');
    }
}
