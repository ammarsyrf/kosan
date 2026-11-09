# Security Checklist & Hardening Guide — Kost Management System

Gunakan checklist ini sebelum merilis sistem Kost Management System ke server produksi.

---

## 1. Environment & Credential Security

- [x] **File `.env` tidak masuk Git:** Pastikan `.env` terdaftar di `.gitignore` dan tidak terlacak di Git repository.
- [x] **`.env.example` bersih dari rahasia:** File `.env.example` hanya berisi placeholder tanpa token API atau password database asli.
- [x] **APP_DEBUG dinonaktifkan:** Pastikan `APP_DEBUG=false` di `.env` produksi agar stack trace error tidak bocor ke publik.
- [x] **APP_ENV disetel ke production:** Nilai `APP_ENV=production`.
- [x] **APP_KEY unik dan kuat:** Kunci minimal 32 karakter acak.
- [x] **Fonnte API Token rahasia:** Token WhatsApp Fonnte disimpan di `.env` dan tidak pernah ditulis (*hardcoded*) di dalam script atau template.

---

## 2. Server & Web Root Boundary

- [x] **Document Root Terisolasi:** VirtualHost mengarah langsung ke `/var/www/kost/public/`. Folder `app/`, `config/`, `cron/`, `database/`, `storage/`, dan `resources/` berada di luar akses web publik.
- [x] **Directory Browsing Dinonaktifkan:** Server web tidak mengizinkan directory listing (`Options -Indexes`).
- [x] **File Tersembunyi Diblokir:** Akses ke `.env`, `.git`, `.htaccess` diblokir oleh web server (`deny all`).
- [x] **Eksekusi Script di Upload Diblokir:** Folder `storage/uploads/` tidak dapat mengeksekusi file PHP, Perl, atau Shell script.

---

## 3. Session & Transport Security

- [x] **HTTPS Diwajibkan:** Seluruh traffic HTTP dialihkan otomatis ke HTTPS (SSL/TLS).
- [x] **Cookie HttpOnly Aktif:** Cookie session menggunakan flag `HttpOnly=true` untuk mencegah pencurian session via XSS.
- [x] **Cookie Secure Aktif di HTTPS:** Cookie session menggunakan flag `Secure=true` saat koneksi HTTPS aktif.
- [x] **Cookie SameSite:** Menggunakan `SameSite=Lax` untuk mitigasi Cross-Site Request Forgery (CSRF).
- [x] **Session Timeout:** Otomatis memutus sesi pengguna yang tidak aktif selama durasi `SESSION_LIFETIME` (default: 7200 detik / 2 jam).

---

## 4. Application Security Controls

- [x] **Proteksi CSRF Global:** Seluruh request mutasi (`POST`, `PUT`, `DELETE`) divalidasi dengan token CSRF 64-karakter SHA-256 via `CsrfMiddleware`.
- [x] **Pencegahan SQL Injection:** Seluruh query database menggunakan PDO Prepared Statements dengan parameter binding.
- [x] **Pencegahan Cross-Site Scripting (XSS):** Semua output teks dinamis di-escape menggunakan helper `e()` (berbasis `htmlspecialchars` UTF-8).
- [x] **Rate Limiting Login:** Pembatasan maksimal 5 kali percobaan gagal dalam rentang 15 menit per kombinasi email dan IP.
- [x] **Proteksi IDOR & Isolasi Data:**
  - Tenant portal hanya dapat mengakses data dengan filter eksplisit `tenant_id` milik akun yang sedang login.
  - Unduhan bukti transfer dan lampiran pemeliharaan divalidasi kepemilikannya di controller sebelum disajikan.
- [x] **Validasi File Upload:** Memeriksa ukuran file maksimum (`UPLOAD_MAX_SIZE`), ekstensi, dan MIME type berbasis fileinfo (`image/jpeg`, `image/png`, `image/webp`, `application/pdf`). Nama file digenerate ulang dengan hash acak untuk mencegah *path traversal*.
- [x] **CLI Guard pada Cron Script:** Setiap file di direktori `cron/` wajib memeriksa `php_sapi_name() === 'cli'`.

---

## 5. Database Hardening

- [x] **User Non-Root:** Aplikasi berjalan menggunakan user database khusus (`kost_prod_user`) dengan hak akses terbatas pada database kost saja.
- [x] **Password Default Diganti:** Akun seed Owner (`owner@kost.com`) dan Admin (`admin@kost.com`) wajib diganti kata sandinya sebelum go-live.
- [x] **Database Port Non-Public:** Port MySQL (3306) hanya mendengarkan pada `127.0.0.1` (localhost) atau dilindungi firewall UFW.

