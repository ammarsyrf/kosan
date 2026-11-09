# Kost Management System — Personal

Aplikasi web manajemen kos internal khusus untuk satu lokasi usaha kos personal dengan kapasitas kurang dari 10 kamar. Dibangun menggunakan arsitektur **Modular Monolith Custom MVC berbasis PHP Native 8.2+** tanpa framework backend.

---

## 1. Persyaratan Sistem
- **PHP:** Versi 8.2 atau lebih baru (CLI & Web).
- **Ekstensi PHP:** `pdo`, `pdo_mysql`, `session`, `openssl`, `sodium`, `fileinfo`, `gd`, `mbstring`, `curl`, `json`.
- **Database:** MySQL 8.0+ / MariaDB (InnoDB, utf8mb4).
- **Composer:** Dependency manager untuk autoloading PSR-4 (`App\`).
- **Web Server:** Laragon (Apache/Nginx) atau PHP Built-in Web Server.

---

## 2. Struktur Direktori

```text
kosan/
├── app/
│   ├── Controllers/         # Menerima request HTTP & mengembalikan response
│   ├── Core/                # Router, Request, Response, Database, Session, Csrf, View, Logger, ErrorHandler
│   ├── Helpers/             # Helper functions (view, redirect, url, e, csrf_field, dll)
│   ├── Middleware/          # AuthMiddleware, RoleMiddleware, CsrfMiddleware
│   ├── Models/              # Model data dasar & entitas
│   ├── Repositories/        # Akses query SQL via PDO prepared statements
│   ├── Services/            # Business logic, autentikasi, transaksi finansial
│   └── Validators/          # Validasi input server-side
├── config/                  # Konfigurasi aplikasi (app.php, database.php)
├── database/
│   └── migrations/          # File referensi skema SQL
├── public/                  # Document root web server
│   ├── index.php            # Front controller tunggal
│   └── .htaccess            # URL rewriting Apache
├── resources/
│   └── views/               # Tampilan UI (layouts, components, auth, errors)
├── storage/                 # Private storage (terproteksi dari web publik)
│   ├── logs/                # Application & audit logs harian
│   ├── uploads/             # Bukti pembayaran & lampiran maintenance
│   ├── cache/               # Cache aplikasi & session
│   └── exports/             # File export laporan
├── tests/                   # Automated test suites
├── .env.example             # Template variabel lingkungan
├── .gitignore               # Proteksi file rahasia & dynamic storage
├── composer.json            # PSR-4 Autoloading
├── MASTER_PROMPT.md         # Standar teknis arsitektur & milestone
└── PRD.md                   # Spesifikasi produk & aturan bisnis
```

---

## 3. Panduan Instalasi & Menjalankan Aplikasi

### A. Clone & Setup Dependensi
```bash
git clone https://github.com/ammarsyrf/kosan.git
cd kosan
composer dump-autoload
```

### B. Konfigurasi Environment
Salin template konfigurasi dan sesuaikan kredensial lokal:
```bash
cp .env.example .env
```
Pastikan pengaturan database di `.env` sudah sesuai:
```ini
APP_NAME="Kost Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kost_management
DB_USERNAME=root
DB_PASSWORD=
```

### C. Menjalankan Aplikasi

#### Opsi 1: Menggunakan PHP Built-in Server
Arahkan document root ke folder `public/`:
```bash
php -S localhost:8000 -t public
```
Buka browser dan akses: `http://localhost:8000`

#### Opsi 2: Menggunakan Laragon (Apache / Nginx)
1. Letakkan project di direktori Laragon (misal `C:\laragon\www\kosan` atau buat virtual host yang mengarah ke `C:\laravel10\kosan\public`).
2. Pastikan Document Root diarahkan ke subdirektori `public/`.
3. Akses via URL virtual host Laragon (misal `http://kosan.test`).

---

## 4. Menjalankan Pengujian Otomatis (M1 Test Suite)

Jalankan test suite dari terminal:
```bash
php tests/test_m1.php
```
Test suite memverifikasi:
- Environment & Config loader.
- Session management & flash messages.
- CSRF protection (GET bypass, POST enforcement, token tampering).
- Input validation (required, email, min, max, confirmed).
- Custom Router (GET, POST, dynamic parameters, 404, 405).
- View engine & HTML escaping (`e()`).
- Autentikasi, session fixation protection, dan login rate limiter.
- Otorisasi berbasis role (`owner`, `admin`, `tenant`).
- Centralized logger & data redaction (password/token).

---

## 5. Fitur Keamanan yang Diterapkan pada M1
1. **Pencegahan SQL Injection:** Seluruh query database menggunakan PDO prepared statements dengan parameter binding.
2. **Pencegahan Cross-Site Scripting (XSS):** Seluruh output pada view di-escape menggunakan helper `e()`.
3. **Pencegahan CSRF:** Token unik 64-karakter divalidasi pada seluruh request mutasi (POST, PUT, PATCH, DELETE).
4. **Pencegahan Session Fixation & Hijacking:**
   - Regenerasi ID session secara atomik saat login (`session_regenerate_id(true)`).
   - Pengaturan cookie session dengan flag `HttpOnly`, `SameSite=Lax`, dan `Secure` (pada HTTPS).
   - Auto session timeout tracking.
5. **Brute Force Protection:** Login rate limiter memblokir upaya login setelah 5 kali kegagalan selama 15 menit.
6. **Proteksi File Rahasia & Storage:**
   - Direktori `app`, `config`, `database`, dan `storage` berada di luar `public/` dan tidak dapat diakses langsung oleh web browser.
   - File `.env` terabaikan secara ketat oleh `.gitignore`.
