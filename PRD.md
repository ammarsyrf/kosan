# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Kost Management System — Personal

**Versi:** 1.0  
**Tanggal:** 4 November 2026  
**Status:** Final untuk dasar pengembangan  
**Platform:** Web Application  
**Target penggunaan:** Pengelolaan satu usaha kos milik pribadi

---

## 1. Ringkasan Produk

Kost Management System adalah aplikasi web internal untuk membantu Owner mengelola operasional satu lokasi kos dengan jumlah kamar kurang dari 10. Sistem mencakup pengelolaan kamar, calon penghuni, booking, kontrak sewa, tagihan, pembayaran, pengeluaran, laporan keuangan, pemeliharaan, pengumuman, dan pengingat WhatsApp.

Aplikasi dibuat khusus untuk satu bisnis dan satu lokasi. Sistem bukan SaaS, tidak menyediakan multi-organization, paket langganan, atau registrasi publik.

## 2. Tujuan Produk

1. Memusatkan data kamar, penghuni, booking, kontrak, dan transaksi.
2. Mengurangi pencatatan manual dan risiko data tercecer.
3. Memastikan tagihan, pembayaran, dan pemasukan tercatat secara akurat.
4. Memberikan Owner gambaran kondisi usaha melalui dashboard dan laporan.
5. Memudahkan penghuni melihat tagihan serta mengajukan permintaan.
6. Mengotomatisasi pengingat tagihan melalui WhatsApp.
7. Menyediakan histori perubahan dan audit aktivitas penting.

## 3. Ruang Lingkup

### 3.1 Termasuk dalam versi awal

- Autentikasi dan otorisasi tiga role: Owner, Admin, Tenant.
- Dashboard Owner dan dashboard Admin.
- Manajemen kamar dan fasilitas.
- Manajemen data penghuni.
- Booking kamar gratis.
- Aktivasi akun Tenant oleh Admin melalui tautan aman.
- Kontrak sewa dan histori kontrak.
- Permintaan perpanjangan kontrak.
- Permintaan checkout lebih awal.
- Invoice dan tagihan berkala.
- Pembayaran tunai dan transfer bank.
- Pembayaran parsial/cicilan.
- Upload bukti transfer dan verifikasi pembayaran.
- Alokasi pembayaran ke invoice.
- Pencatatan pengeluaran secara detail.
- Laporan keuangan dan okupansi.
- Pengingat WhatsApp melalui Fonnte.
- Permintaan maintenance.
- Pengumuman dan status telah dibaca.
- Activity log dan audit trail.
- Backup database dan file.

### 3.2 Tidak termasuk dalam versi awal

- Multi-cabang atau multi-lokasi.
- Multi-tenant SaaS dan sistem subscription.
- Payment gateway otomatis.
- Deposit atau uang jaminan.
- Denda keterlambatan otomatis.
- Tagihan listrik dan air terpisah.
- Marketplace atau pencarian kos publik.
- Booking berbayar atau booking fee.
- Aplikasi mobile native.
- Integrasi akuntansi pihak ketiga.

## 4. Pengguna dan Hak Akses

### 4.1 Owner

Owner adalah pemilik usaha dan super admin.

Hak akses:
- Akses penuh ke seluruh modul dan data.
- Mengelola akun Admin dan permission.
- Mengatur konfigurasi bisnis, masa booking, dan template notifikasi.
- Melihat dashboard dan laporan keuangan.
- Menyetujui atau menolak keputusan operasional yang menjadi kewenangannya.
- Melihat activity log dan audit trail.
- Mengelola backup dan konfigurasi sistem.

### 4.2 Admin

Admin menjalankan kegiatan operasional sehari-hari.

Hak akses:
- Mengelola kamar, fasilitas, data Tenant, booking, kontrak, invoice, pembayaran, pengeluaran, maintenance, dan pengumuman sesuai permission.
- Memverifikasi pembayaran jika diberi izin.
- Memproses permintaan perpanjangan dan checkout jika diberi izin.
- Tidak boleh mengubah permission Owner atau mengambil alih hak super admin.
- Semua akses dibatasi melalui pemeriksaan backend.

### 4.3 Tenant

Tenant hanya dapat mengakses data miliknya sendiri.

Hak akses:
- Melihat profil dan informasi kamar.
- Melihat kontrak dan histori kontrak sendiri.
- Melihat invoice, saldo tagihan, dan pembayaran sendiri.
- Mengunggah bukti transfer.
- Mengajukan perpanjangan kontrak.
- Mengajukan checkout lebih awal.
- Mengirim laporan maintenance.
- Melihat pengumuman dan menandai telah dibaca.

## 5. Keputusan Bisnis Final

| Area | Keputusan |
|---|---|
| Identitas aplikasi | Personal, untuk satu usaha kos |
| Lokasi | Satu lokasi |
| Kapasitas | Kurang dari 10 kamar |
| Penghuni | Satu Tenant per kamar |
| Periode sewa | Bulanan |
| Pembayaran sewa | Advance / dibayar di muka |
| Jatuh tempo | Mengikuti anchor tanggal mulai kontrak Tenant |
| Bulan pertama | Invoice dibuat manual oleh Admin |
| Harga kamar | Contract-based; harga kontrak menjadi snapshot |
| Booking | Gratis, tanpa booking fee |
| Masa booking | Dapat diatur Admin; expiry disimpan saat booking dibuat |
| Akun Tenant | Dibuat Admin, Tenant aktivasi melalui tautan |
| Perpanjangan | Tenant mengajukan, Admin menyetujui/menolak |
| Pembayaran | Transfer bank dan tunai |
| Cicilan | Diperbolehkan |
| Bukti transfer | Wajib untuk pembayaran transfer |
| Pengakuan pembayaran | Setelah diverifikasi Admin |
| Deposit | Tidak ada |
| Denda telat | Tidak ada |
| Listrik dan air | Termasuk harga sewa |
| Checkout lebih awal | Pengajuan Tenant, keputusan finansial oleh Admin |
| Pengeluaran | Dicatat detail |
| Dashboard Owner | Wajib tersedia |
| WhatsApp | Melalui Fonnte API |

## 6. Aturan Bisnis Detail

### 6.1 Kamar dan ketersediaan

- Data kamar memiliki kode/nama, deskripsi, harga saat ini, fasilitas, status operasional, dan catatan.
- Satu kamar hanya boleh memiliki satu penghuni aktif pada periode yang sama.
- Ketersediaan ditentukan dari rentang booking dan kontrak serta status operasional kamar.
- Status kamar yang disimpan tidak boleh menjadi satu-satunya sumber kebenaran ketersediaan.
- Perubahan harga kamar hanya berlaku untuk booking/kontrak baru atau harga yang ditetapkan pada perpanjangan.
- Harga kontrak dan invoice yang sudah dibuat tidak berubah ketika harga master kamar diubah.

### 6.2 Booking

- Booking tidak dikenakan biaya.
- Status booking: `pending`, `confirmed`, `cancelled`, `expired`, `converted`.
- Admin dapat mengatur durasi default booking.
- Saat booking dibuat, sistem menyimpan `expires_at` dan snapshot durasi yang digunakan.
- Perubahan konfigurasi durasi tidak mengubah booking yang sudah ada.
- Booking yang telah `confirmed` tidak boleh kedaluwarsa otomatis tanpa aturan eksplisit.
- Booking berbeda dari kontrak aktif.
- Konversi booking menjadi kontrak harus dilakukan secara transaksional dan memeriksa ketersediaan kamar.

### 6.3 Kontrak sewa

- Kontrak menyimpan Tenant, kamar, periode mulai dan selesai, harga sewa, anchor tanggal tagihan, status, dan catatan.
- Harga sewa disimpan sebagai snapshot kontrak.
- Sistem harus mencegah kontrak aktif yang tumpang tindih untuk kamar yang sama.
- Histori perubahan kontrak wajib disimpan.
- Perpanjangan dilakukan melalui permintaan Tenant dan keputusan Admin.
- Saat perpanjangan disetujui, Admin menetapkan periode dan harga baru.
- Kontrak lama tidak dihapus; histori harus tetap tersedia.

### 6.4 Anchor tanggal tagihan

- Tanggal jatuh tempo mengikuti tanggal anchor kontrak.
- Anchor harus disimpan secara eksplisit dan tidak dihitung ulang dari tanggal yang telah mengalami penyesuaian bulan.
- Contoh: kontrak mulai tanggal 31 Januari. Jatuh tempo Februari menggunakan hari terakhir Februari, tetapi periode berikutnya kembali menggunakan tanggal 31 jika bulan memungkinkan.
- Jika bulan tidak memiliki tanggal anchor, gunakan hari terakhir bulan tersebut untuk periode itu saja.
- Perhitungan bulan berikutnya tetap mengacu pada anchor asli.

### 6.5 Invoice bulan pertama

- Invoice pertama dibuat manual oleh Admin.
- Admin menentukan nominal, periode awal, periode akhir, tanggal terbit, tanggal jatuh tempo, dan alasan/catatan.
- Sistem tidak boleh menghitung prorata otomatis untuk invoice pertama.
- Invoice pertama tidak boleh mengubah anchor tagihan reguler kontrak.
- Invoice reguler berikutnya mengikuti aturan anchor kontrak.

### 6.6 Invoice reguler

- Invoice dibuat berdasarkan periode kontrak dan jadwal tagihan.
- Invoice memiliki nomor unik, periode, tanggal terbit, jatuh tempo, total, jumlah terbayar, saldo, dan status.
- Status minimal: `draft`, `unpaid`, `partially_paid`, `paid`, `void`.
- Hindari invoice ganda untuk kontrak dan periode yang sama.
- Invoice yang telah memiliki pembayaran terverifikasi tidak boleh dihapus sembarangan; gunakan void/koreksi dengan alasan dan audit trail.

### 6.7 Pembayaran

- Metode: transfer bank dan tunai.
- Pembayaran parsial diperbolehkan.
- Pembayaran transfer wajib memiliki bukti.
- Tenant dapat mengirim pembayaran dan bukti; pembayaran tetap berstatus menunggu verifikasi.
- Admin memverifikasi atau menolak pembayaran.
- Pembayaran pending atau rejected tidak mengurangi saldo invoice dan tidak dihitung sebagai pemasukan resmi.
- Pembayaran terverifikasi dialokasikan ke invoice.
- Jumlah alokasi tidak boleh melebihi nilai pembayaran atau saldo invoice.
- Transaksi verifikasi dan alokasi harus memakai database transaction dan row locking.
- Pembayaran yang sudah diverifikasi tidak boleh dihapus langsung; gunakan reversal atau adjustment dengan audit trail.
- Pemasukan diakui ketika pembayaran telah diverifikasi dan dialokasikan.

### 6.8 Checkout lebih awal

- Tenant mengirim permintaan checkout lebih awal.
- Admin menentukan tanggal efektif checkout.
- Kebijakan finansial diputuskan per kasus: tanpa refund, prorata, nominal refund, atau penyesuaian khusus.
- Sistem tidak boleh otomatis mengasumsikan Tenant berhak atas prorata/refund.
- Catat keputusan, approver, tanggal, nominal, alasan, catatan, dan histori.
- Invoice dan pembayaran sebelumnya tetap dipertahankan.

### 6.9 Pengeluaran

Setiap pengeluaran dapat mencatat:
- Kategori.
- Tanggal transaksi.
- Nominal.
- Deskripsi.
- Vendor (opsional).
- Metode pembayaran.
- Bukti/struk (opsional).
- Pembuat transaksi.
- Catatan.

Contoh kategori: perbaikan, kebersihan, perawatan, internet, perlengkapan, operasional, dan kategori lain yang dapat dikelola.

### 6.10 Laporan keuangan

Dashboard dan laporan wajib membedakan:
- Total invoice/tagihan.
- Pemasukan aktual dari pembayaran terverifikasi.
- Piutang/sisa tagihan.
- Total pengeluaran.
- Arus kas bersih.
- Okupansi.
- Pendapatan per kamar.

Invoice yang belum dibayar bukan pemasukan. Forecast atau proyeksi harus diberi label estimasi.

### 6.11 Pengingat WhatsApp

- Integrasi menggunakan Fonnte API.
- Jadwal default yang disarankan: H-7, H-3, H-1, hari jatuh tempo, dan H+1 jika masih ada saldo.
- Jadwal dapat dikonfigurasi.
- Pengingat didasarkan pada saldo invoice tersisa, bukan total invoice awal.
- Invoice lunas tidak boleh menerima pengingat.
- Gunakan idempotency key untuk mencegah pesan ganda.
- Kegagalan WhatsApp tidak boleh menggagalkan proses invoice atau pembayaran.
- Simpan log, status, response, retry count, dan waktu pengiriman.

### 6.12 Maintenance dan pengumuman

- Tenant dapat membuat laporan maintenance dengan kategori, deskripsi, foto/lampiran, dan prioritas.
- Admin dapat mengubah status, menambahkan update, dan menutup laporan.
- Owner/Admin dapat membuat pengumuman.
- Tenant dapat melihat pengumuman dan status baca.

## 7. Kebutuhan Fungsional

| ID | Modul | Kebutuhan |
|---|---|---|
| FR-001 | Auth | Login, logout, session management, password hashing |
| FR-002 | Role | Owner, Admin, Tenant dan permission berbasis backend |
| FR-003 | Rooms | CRUD kamar, fasilitas, status operasional |
| FR-004 | Tenants | Data Tenant dan akun terkait |
| FR-005 | Activation | Aktivasi akun dengan token aman, sekali pakai, kedaluwarsa |
| FR-006 | Booking | Buat, konfirmasi, batalkan, kedaluwarsa, konversi booking |
| FR-007 | Contract | Buat dan kelola kontrak serta histori |
| FR-008 | Extension | Pengajuan dan persetujuan perpanjangan |
| FR-009 | Checkout | Pengajuan checkout dan keputusan finansial manual |
| FR-010 | Billing | Invoice pertama manual dan invoice reguler |
| FR-011 | Payment | Transfer, tunai, cicilan, bukti, verifikasi, alokasi |
| FR-012 | Expense | Pencatatan pengeluaran detail |
| FR-013 | Dashboard | Ringkasan Owner dan Admin |
| FR-014 | Reports | Laporan keuangan, piutang, okupansi, pendapatan kamar |
| FR-015 | WhatsApp | Pengingat dan log pengiriman Fonnte |
| FR-016 | Maintenance | Laporan, lampiran, update status |
| FR-017 | Announcement | Pengumuman dan status baca |
| FR-018 | Audit | Activity log untuk aksi penting |
| FR-019 | Settings | Konfigurasi bisnis dan notifikasi |
| FR-020 | Backup | Backup database dan file serta prosedur restore |

## 8. Kebutuhan Non-Fungsional

### Keamanan
- Gunakan PDO prepared statements.
- Password memakai `password_hash()` dan `password_verify()`.
- Token aktivasi disimpan dalam bentuk hash.
- Terapkan CSRF protection pada request yang mengubah data.
- Escape output untuk mencegah XSS.
- Validasi input di server.
- Regenerasi session ID setelah login dan atur session timeout.
- Terapkan rate limit login.
- Validasi upload berdasarkan MIME, ukuran, ekstensi, dan nama file acak.
- Simpan bukti pembayaran dan lampiran di storage privat, bukan direktori publik.
- Rahasia aplikasi berada di `.env`.
- Otorisasi Tenant harus diperiksa di backend untuk setiap resource.

### Integritas data
- Gunakan InnoDB dan foreign key.
- Gunakan transaksi database untuk proses booking, kontrak, pembayaran, dan alokasi.
- Gunakan `SELECT ... FOR UPDATE` untuk proses yang rentan race condition.
- Gunakan unique constraint dan index yang relevan.
- Cegah overlap rentang booking/kontrak melalui locking dan pengecekan transaksional; unique index biasa tidak cukup untuk semua rentang tanggal.
- Simpan nominal dengan `DECIMAL(15,2)`.

### Kinerja dan keandalan
- Arsitektur Modular Monolith.
- Controller tipis; business logic berada di Service.
- WhatsApp berjalan secara independen dari transaksi finansial.
- Cron memiliki logging, idempotency, dan pencegahan eksekusi bersamaan.
- Sediakan backup harian dan uji restore.

### Kompatibilitas
- PHP 8.2 atau lebih baru.
- MySQL 8.
- Browser modern desktop dan mobile.
- UI responsif.

## 9. Teknologi dan Arsitektur

### Stack
- PHP Native 8.2+ dengan custom MVC.
- MySQL 8, InnoDB, utf8mb4.
- PDO.
- PHP Sessions.
- HTML5, Tailwind CSS, Vanilla JavaScript, Fetch API.
- Alpine.js opsional.
- Chart.js.
- Lucide Icons.
- Dompdf.
- PhpSpreadsheet.
- Fonnte API.
- Cron jobs.
- Composer hanya untuk dependency eksternal.

**Tidak menggunakan:** Laravel, CodeIgniter, React, Vue, atau backend framework lain kecuali keputusan proyek diubah.

### Pola arsitektur
Modular Monolith dengan pemisahan:
- Controllers: menerima request dan mengatur response.
- Services: business logic dan transaksi.
- Models/Repositories: akses data.
- Middleware: autentikasi dan otorisasi.
- Validators: validasi input.
- Views: tampilan.
- Core: Router, Database, Request, Response, Session, View renderer, dan komponen inti.

### Struktur direktori yang disarankan

```text
kost-management/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   ├── Middleware/
│   ├── Core/
│   ├── Helpers/
│   └── Validators/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema.sql
├── resources/
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── auth/
│       ├── owner/
│       ├── admin/
│       └── tenant/
├── public/
│   ├── index.php
│   └── assets/
├── storage/
│   ├── logs/
│   ├── uploads/
│   ├── cache/
│   └── exports/
├── cron/
├── vendor/
├── .env
├── .env.example
├── .gitignore
└── README.md
```

Semua request web masuk melalui `public/index.php`. Direktori `app`, `config`, `database`, dan `storage` tidak boleh dapat diakses langsung dari web.

## 10. Rancangan Database

Tabel inti yang direncanakan:

### Identity & access
- `users`
- `roles`
- `permissions`
- `role_user`
- `permission_role`
- `account_activation_tokens`

### Rooms
- `rooms`
- `room_facilities`
- `room_facility_assignments`

### Tenant
- `tenants`

### Booking & contracts
- `bookings`
- `rental_contracts`
- `contract_histories`
- `contract_extension_requests`
- `early_checkout_requests`

### Billing
- `invoices`
- `invoice_items`

### Payments
- `payments`
- `payment_proofs`
- `payment_allocations`
- `payment_adjustments`

### Finance
- `expense_categories`
- `expenses`

### Maintenance
- `maintenance_categories`
- `maintenance_requests`
- `maintenance_attachments`
- `maintenance_updates`

### Notifications & announcements
- `notification_templates`
- `whatsapp_logs`
- `announcements`
- `announcement_reads`

### System
- `settings`
- `activity_logs`

Standar database:
- Primary key `BIGINT UNSIGNED`.
- Uang `DECIMAL(15,2)`.
- Foreign key dan index.
- `created_at` dan `updated_at`.
- Soft delete hanya untuk entitas yang memang membutuhkannya.
- Hindari tabel yang memiliki tanggung jawab tumpang tindih.
- Simpan histori dan snapshot untuk data finansial/kontrak yang tidak boleh berubah secara retroaktif.

## 11. Dashboard dan Laporan

### Owner Dashboard
Menampilkan:
- Total kamar, kamar terisi, kamar tersedia, dan okupansi.
- Pemasukan aktual pada periode terpilih.
- Total pengeluaran dan arus kas bersih.
- Piutang dan invoice jatuh tempo.
- Pembayaran menunggu verifikasi.
- Booking aktif dan booking yang mendekati expiry.
- Kontrak yang mendekati akhir.
- Permintaan perpanjangan dan checkout.
- Laporan maintenance.
- Pendapatan per kamar.
- Grafik pemasukan dan pengeluaran.

### Admin Dashboard
Menampilkan pekerjaan operasional:
- Booking baru.
- Tenant aktif.
- Invoice jatuh tempo.
- Pembayaran yang perlu diverifikasi.
- Permintaan perpanjangan/checkout.
- Maintenance terbuka.
- Pengumuman dan aktivitas terbaru.

### Tenant Portal
Menampilkan:
- Ringkasan kamar dan kontrak.
- Tagihan berjalan dan riwayat invoice.
- Saldo tagihan.
- Riwayat pembayaran dan status verifikasi.
- Form upload bukti pembayaran.
- Form perpanjangan dan checkout.
- Form maintenance.
- Pengumuman.

## 12. Cron Jobs

Sediakan script CLI terpisah:
- `generate-invoices.php`
- `payment-reminders.php`
- `booking-expiration.php`
- `notification-retry.php`

Setiap cron wajib:
- Bootstrap aplikasi secara konsisten.
- Menulis log.
- Memiliki idempotency.
- Mencegah eksekusi bersamaan.
- Mendukung mode test/dry-run bila relevan.
- Menangani error tanpa merusak proses lain.

## 13. Audit Trail

Catat minimal:
- Login/logout dan aktivitas keamanan penting.
- Pembuatan/perubahan booking.
- Perubahan kontrak dan harga.
- Pembuatan invoice.
- Pengiriman pembayaran.
- Verifikasi/penolakan/reversal pembayaran.
- Pengeluaran.
- Persetujuan perpanjangan.
- Keputusan checkout dan refund/adjustment.
- Perubahan permission dan settings.

Log menyimpan aktor, aksi, entitas, ID entitas, waktu, dan metadata perubahan yang relevan. Hindari menyimpan password, token mentah, atau rahasia.

## 14. Kriteria Penerimaan Utama

1. Tenant tidak dapat melihat data Tenant lain meskipun mengubah URL atau parameter request.
2. Dua booking/kontrak tidak dapat mengambil kamar yang sama pada rentang waktu yang tumpang tindih.
3. Perubahan harga kamar tidak mengubah kontrak atau invoice yang sudah dibuat.
4. Invoice pertama hanya dibuat berdasarkan input manual Admin dan tidak mengubah anchor invoice reguler.
5. Anchor tanggal 31 tetap kembali ke tanggal 31 setelah melewati bulan yang lebih pendek.
6. Pembayaran pending/rejected tidak mengurangi saldo invoice.
7. Pembayaran terverifikasi tidak dapat dialokasikan melebihi saldo invoice.
8. Invoice lunas tidak menerima pengingat WhatsApp.
9. Perubahan durasi booking default tidak mengubah expiry booking yang telah dibuat.
10. Keputusan checkout dan refund selalu melalui persetujuan Admin dan tercatat.
11. Owner dapat melihat ringkasan keuangan yang membedakan invoice, pemasukan aktual, piutang, dan pengeluaran.
12. Kegagalan Fonnte tidak membatalkan proses invoice atau pembayaran.
13. Aktivasi akun menggunakan token satu kali, ter-hash, dan memiliki masa berlaku.
14. Backup database dan file dapat dipulihkan melalui prosedur yang terdokumentasi.

## 15. Tahapan Pengembangan

| Milestone | Fokus | Output |
|---|---|---|
| M0 | Audit repository | Laporan kondisi, gap, risiko, rekomendasi; tanpa mengubah kode |
| M1 | Fondasi MVC | Router, env, database, controller, view, middleware, auth, authorization, logger |
| M2 | Database & master data | Schema/migration, seeder, kamar, fasilitas, Tenant |
| M3 | Booking & kontrak | Booking, expiry, availability, kontrak, extension, early checkout |
| M4 | Billing & payment | Invoice, billing engine, cicilan, verifikasi, alokasi, histori |
| M5 | WhatsApp | Fonnte, template, reminder, log, retry |
| M6 | Finance & maintenance | Pengeluaran, laporan dasar, maintenance, pengumuman |
| M7 | Dashboard & UI | Owner/Admin/Tenant dashboard, responsive UI, UX polish |
| M8 | QA & deployment | Testing, security review, backup/restore, dokumentasi deployment |

## 16. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Double booking | Transaction, lock row kamar, overlap validation |
| Double invoice | Unique constraint dan idempotent invoice generator |
| Salah hitung tanggal bulanan | Simpan anchor date terpisah |
| Pembayaran tercatat ganda | Idempotency, row locking, audit trail |
| Bukti pembayaran bocor | Private storage dan authorized download |
| Tenant mengakses data orang lain | Backend ownership checks |
| Reminder terkirim berulang | Idempotency key dan WhatsApp log |
| API WhatsApp gagal | Retry terpisah, jangan gagalkan transaksi finansial |
| Perubahan harga merusak histori | Snapshot harga pada kontrak dan invoice |
| Kehilangan data | Backup database dan file serta uji restore |

## 17. Definition of Done

Sebuah milestone dianggap selesai jika:
- Fitur berjalan sesuai aturan bisnis.
- Validasi dilakukan di backend.
- Permission diuji.
- Tidak ada perubahan data finansial tanpa transaksi dan audit yang sesuai.
- Error ditangani dan dicatat.
- UI responsif dan memiliki loading, empty, success, dan error state.
- Test skenario utama dan edge case lulus.
- Dokumentasi penggunaan/teknis diperbarui.
- Tidak ada secret atau file privat di public directory.

---
**Dokumen ini adalah sumber keputusan produk utama. Jika ada konflik antara asumsi implementasi dan PRD, ikuti PRD dan minta konfirmasi untuk perubahan aturan bisnis.**
