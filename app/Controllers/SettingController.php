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
            
            // Hero Landing
            'landing_hero_badge' => ['group' => 'landing', 'desc' => 'Teks Badge Hero Beranda'],
            'landing_hero_title' => ['group' => 'landing', 'desc' => 'Judul Headline Utama Hero'],
            'landing_hero_subtitle' => ['group' => 'landing', 'desc' => 'Deskripsi / Subtitle Hero Beranda'],

            // Alur Reservasi (3 Langkah)
            'landing_steps_title' => ['group' => 'landing', 'desc' => 'Judul Alur Reservasi'],
            'landing_steps_subtitle' => ['group' => 'landing', 'desc' => 'Deskripsi Alur Reservasi'],
            'landing_step1_title' => ['group' => 'landing', 'desc' => 'Judul Langkah 1'],
            'landing_step1_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Langkah 1'],
            'landing_step2_title' => ['group' => 'landing', 'desc' => 'Judul Langkah 2'],
            'landing_step2_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Langkah 2'],
            'landing_step3_title' => ['group' => 'landing', 'desc' => 'Judul Langkah 3'],
            'landing_step3_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Langkah 3'],

            // Fasilitas Bersama (4 Item)
            'landing_fac_title' => ['group' => 'landing', 'desc' => 'Judul Fasilitas Bersama'],
            'landing_fac_subtitle' => ['group' => 'landing', 'desc' => 'Deskripsi Fasilitas Bersama'],
            'landing_fac1_title' => ['group' => 'landing', 'desc' => 'Judul Fasilitas 1'],
            'landing_fac1_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Fasilitas 1'],
            'landing_fac2_title' => ['group' => 'landing', 'desc' => 'Judul Fasilitas 2'],
            'landing_fac2_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Fasilitas 2'],
            'landing_fac3_title' => ['group' => 'landing', 'desc' => 'Judul Fasilitas 3'],
            'landing_fac3_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Fasilitas 3'],
            'landing_fac4_title' => ['group' => 'landing', 'desc' => 'Judul Fasilitas 4'],
            'landing_fac4_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Fasilitas 4'],

            // Tata Tertib & Aturan Kos
            'landing_rules_title' => ['group' => 'landing', 'desc' => 'Judul Tata Tertib'],
            'landing_rules_subtitle' => ['group' => 'landing', 'desc' => 'Deskripsi Tata Tertib'],
            'landing_rule1_title' => ['group' => 'landing', 'desc' => 'Judul Aturan 1'],
            'landing_rule1_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Aturan 1'],
            'landing_rule2_title' => ['group' => 'landing', 'desc' => 'Judul Aturan 2'],
            'landing_rule2_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Aturan 2'],
            'landing_rule3_title' => ['group' => 'landing', 'desc' => 'Judul Aturan 3'],
            'landing_rule3_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Aturan 3'],
            'landing_rule4_title' => ['group' => 'landing', 'desc' => 'Judul Aturan 4'],
            'landing_rule4_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Aturan 4'],
            'landing_rule5_title' => ['group' => 'landing', 'desc' => 'Judul Aturan 5'],
            'landing_rule5_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Aturan 5'],
            'landing_rule6_title' => ['group' => 'landing', 'desc' => 'Judul Aturan 6'],
            'landing_rule6_desc' => ['group' => 'landing', 'desc' => 'Deskripsi Aturan 6'],

            // Rekening & Kebijakan
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
