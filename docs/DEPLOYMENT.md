# Panduan Deployment Produksi — Kost Management System

Dokumen ini memandu proses deployment aplikasi Kost Management System pada lingkungan server produksi (Linux/VPS/Shared Hosting dengan akses SSH).

---

## 1. Persyaratan Server

- **Operating System:** Linux (Ubuntu 22.04 LTS / Debian 12 / RHEL 9 direkomendasikan)
- **Web Server:** Nginx 1.20+ atau Apache 2.4+
- **PHP Version:** PHP 8.2 atau 8.3
- **Database:** MySQL 8.0+ atau MariaDB 10.11+ (Engine: InnoDB)
- **Ekstensi PHP Wajib:**
  - `pdo` & `pdo_mysql`
  - `mbstring`
  - `curl` (untuk Fonnte WhatsApp API)
  - `openssl` (untuk enkripsi dan CSRF)
  - `fileinfo` (untuk validasi MIME type upload yang aman)
  - `zip` (untuk cron backup arsip lampiran)
  - `json`

---

## 2. Struktur Folder & Document Root

Aplikasi menggunakan pola arsitektur aman di mana **hanya folder `public/` yang diekspos ke publik**.

```
/var/www/kost/
├── app/               (Non-public: Core, Models, Services, Controllers)
├── config/            (Non-public: Konfigurasi aplikasi)
├── cron/              (Non-public: Script cron CLI)
├── database/          (Non-public: Migrasi & Seeder)
├── docs/              (Non-public: Dokumentasi operasional)
├── public/            <--- DOCUMENT ROOT WEB SERVER
│   ├── index.php      (Front Controller)
│   ├── .htaccess      (Jika menggunakan Apache)
│   └── assets/        (CSS, JS, Favicon)
├── resources/         (Non-public: Template views HTML/PHP)
├── storage/           (Non-public: File upload privat, logs, cache, backups)
│   ├── backups/       (SQL dump & Zip arsip lampiran)
│   ├── cache/         (Lock files & session)
│   ├── logs/          (File log aplikasi)
│   └── uploads/       (Bukti transfer, kuitansi, tiket maintenance)
├── tests/             (Non-public: Automated tests)
├── .env               (Non-public: Environment credentials)
└── composer.json
```

---

## 3. Konfigurasi Web Server

### A. Nginx Configuration (`/etc/nginx/sites-available/kost.conf`)

```nginx
server {
    listen 80;
    server_name kosan.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name kosan.example.com;

    ssl_certificate /etc/letsencrypt/live/kosan.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/kosan.example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # PENTING: Root harus mengarah ke folder public
    root /var/www/kost/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # Blokir akses ke file sensitif dan file tersembunyi
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Blokir eksekusi PHP di luar front controller
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
    }
}
```

### B. Apache VirtualHost Configuration

Pastikan modul `mod_rewrite` dan `mod_headers` aktif:
```apache
<VirtualHost *:443>
    ServerName kosan.example.com
    DocumentRoot /var/www/kost/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/kosan.example.com/fullchain.pem
    SSLCertificateKey /etc/letsencrypt/live/kosan.example.com/privkey.pem

    <Directory /var/www/kost/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Blokir akses langsung ke folder storage dari web
    <Directory /var/www/kost/storage>
        Require all denied
    </Directory>
</VirtualHost>
```

---

## 4. Langkah-Langkah Deployment

### 1. Clone & Permissions
```bash
cd /var/www/kost
git clone <repository_url> .
chown -R www-data:www-data /var/www/kost
chmod -R 750 /var/www/kost
chmod -R 770 /var/www/kost/storage
```

### 2. Install Composer Dependencies (No Dev)
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Setup File Environment (`.env`)
Salin template `.env.example` ke `.env`:
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi produksi:
```ini
APP_NAME="Kost Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kosan.example.com
APP_KEY=<generate_random_32_characters_string>

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kost_management
DB_USERNAME=kost_prod_user
DB_PASSWORD=<strong_unique_db_password>

FONNTE_API_TOKEN=<token_fonnte_resmi>
FONNTE_API_URL=https://api.fonnte.com/send
FONNTE_ENABLED=true

SESSION_LIFETIME=7200
UPLOAD_MAX_SIZE=5242880
BACKUP_RETENTION_DAYS=30
```

### 4. Database Setup & Migration
Buat database dan user dengan hak akses minimum di MySQL:
```sql
CREATE DATABASE kost_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kost_prod_user'@'localhost' IDENTIFIED BY '<strong_unique_db_password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON kost_management.* TO 'kost_prod_user'@'localhost';
FLUSH PRIVILEGES;
```

Jalankan script migrasi otomatis:
```bash
php database/migrate.php
```

Jalankan seeder awal untuk akun default dan role:
```bash
php database/seed.php
```

> **PERINGATAN KEAMANAN:** Segera login sebagai Owner (`owner@kost.com` / `password123`) dan Admin (`admin@kost.com` / `password123`), lalu ubah password default melalui profil sebelum sistem dibuka untuk umum!

---

## 5. Smoke Test Pasca Deployment

Setelah deployment, lakukan verifikasi cepat:
1. Buka URL `https://kosan.example.com/` (harus diarahkan ke HTTPS otomatis).
2. Login menggunakan akun Owner.
3. Buka halaman Dashboard Eksekutif (`/owner/dashboard`).
4. Buka halaman Kamar (`/admin/rooms`).
5. Uji download laporan operasional CSV (`/admin/reports/export/operational`).
6. Jalankan automated test di lingkungan lokal / staging sebelum membuka akses publik.

