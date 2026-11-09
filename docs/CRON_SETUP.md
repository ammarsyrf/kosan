# Panduan Konfigurasi Cron & Scheduler — Kost Management System

Aplikasi mengandalkan tugas latar belakang (*background jobs*) berbasis CLI untuk otomatisasi billing, kedaluwarsa booking, pengingat WhatsApp, dan backup berkala.

---

## 1. Persyaratan Eksekusi Cron

Seluruh file cron berada di folder `/var/www/kost/cron/` dan memiliki fitur:
- **CLI Only Protection:** Memeriksa `php_sapi_name() === 'cli'`, menolak eksekusi via URL browser (HTTP 403).
- **Concurrency Locking:** Menggunakan `flock(LOCK_EX | LOCK_NB)` dengan lock file di `storage/cache/*.lock` untuk mencegah *race condition* atau *double processing*.
- **Audit Logging:** Mencatat setiap aktivitas dan error ke `storage/logs/app-YYYY-MM-DD.log`.
- **Dry-run Mode:** Mendukung argumen `--dry-run` untuk pengujian simulasi tanpa mengubah database.

---

## 2. Daftar Script Cron

| File Script | Jadwal Rekomendasi | Deskripsi Pekerjaan |
|---|---|---|
| `cron/booking-expiration.php` | Setiap 5 menit (`*/5 * * * *`) | Memeriksa booking berstatus `pending` yang melewati `expires_at` dan mengubahnya menjadi `expired`. |
| `cron/generate-invoices.php` | Setiap hari pukul 00:05 (`5 0 * * *`) | Menerbitkan invoice sewa bulanan rutin untuk kontrak aktif berdasarkan `billing_anchor_day`. |
| `cron/invoice-overdue-checker.php` | Setiap hari pukul 00:15 (`15 0 * * *`) | Mengubah status invoice `unpaid`/`partially_paid` yang melewati `due_date` menjadi `overdue`. |
| `cron/payment-reminders.php` | Setiap hari pukul 08:00 (`0 8 * * *`) | Mengirimkan reminder WhatsApp jatuh tempo (H-7, H-3, H-1, Hari H, H+1) via Fonnte. |
| `cron/notification-retry.php` | Setiap 15 menit (`*/15 * * * *`) | Mengirim ulang pesan WhatsApp yang gagal (`retrying` / `failed`) dengan batas maksimal 3x percobaan. |
| `cron/backup.php` | Setiap hari pukul 02:00 (`0 2 * * *`) | Melakukan dump database MySQL, zip upload privat, dan memangkas backup lama (> 30 hari). |

---

## 3. Konfigurasi Crontab Server

Edit crontab untuk user web server (`www-data` atau user yang memiliki hak akses ke direktori proyek):

```bash
crontab -e -u www-data
```

Tambahkan baris berikut:

```cron
# ========================================================
# Kost Management System Scheduled Tasks
# ========================================================
SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

# 1. Booking Expiration Check (Setiap 5 menit)
*/5 * * * * php /var/www/kost/cron/booking-expiration.php >> /var/www/kost/storage/logs/cron.log 2>&1

# 2. Routine Invoice Generation (Setiap hari jam 00:05 WIB)
5 0 * * * php /var/www/kost/cron/generate-invoices.php >> /var/www/kost/storage/logs/cron.log 2>&1

# 3. Invoice Overdue Checker (Setiap hari jam 00:15 WIB)
15 0 * * * php /var/www/kost/cron/invoice-overdue-checker.php >> /var/www/kost/storage/logs/cron.log 2>&1

# 4. WhatsApp Payment Reminders (Setiap hari jam 08:00 WIB)
0 8 * * * php /var/www/kost/cron/payment-reminders.php >> /var/www/kost/storage/logs/cron.log 2>&1

# 5. WhatsApp Failed Message Retry (Setiap 15 menit)
*/15 * * * * php /var/www/kost/cron/notification-retry.php >> /var/www/kost/storage/logs/cron.log 2>&1

# 6. Database & Storage Daily Backup (Setiap hari jam 02:00 WIB)
0 2 * * * php /var/www/kost/cron/backup.php >> /var/www/kost/storage/logs/cron.log 2>&1
```

---

## 4. Cara Uji Manual Cron (Dry-Run Mode)

Sebelum mengaktifkan di crontab, jalankan uji simulasi di terminal:

```bash
# Uji invoice generation tanpa menulis ke database
php /var/www/kost/cron/generate-invoices.php --dry-run

# Uji payment reminders tanpa memicu WhatsApp Fonnte
php /var/www/kost/cron/payment-reminders.php --dry-run

# Uji retry WhatsApp tanpa mengirim pesan aktual
php /var/www/kost/cron/notification-retry.php --dry-run

# Uji backup database dan storage tanpa membuat file
php /var/www/kost/cron/backup.php --dry-run
```

