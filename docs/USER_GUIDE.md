# Panduan Pengguna (User Guide) — Kost Management System

Kost Management System dirancang khusus untuk pengelolaan kos personal dengan 3 role utama: **Owner (Pemilik)**, **Admin (Pengelola Operasional)**, dan **Tenant (Penghuni Kos)**.

---

## 1. Alur Kerja Pemilik Kos (Owner)

Pemilik kos memiliki hak akses penuh ke seluruh fitur sistem, termasuk laporan eksekutif dan keuangan.

### A. Memantau Kinerja Bisnis via Executive Dashboard
1. Login ke aplikasi menggunakan akun Owner.
2. Anda akan otomatis diarahkan ke **Executive Owner Dashboard** (`/owner/dashboard`).
3. Pada halaman ini, Anda dapat memantau:
   - **Arus Kas Bersih (Net Cash Flow):** Total pendapatan masuk terverifikasi dikurangi total pengeluaran operasional.
   - **Pendapatan & Beban:** Perbandingan performa dengan periode sebelumnya (persentase naik/turun).
   - **Tingkat Okupansi Kamar:** Jumlah kamar terisi vs kosong beserta progress bar.
   - **Alert Perhatian:** Notifikasi pembayaran menunggu verifikasi, tagihan jatuh tempo, dan kerusakan prioritas tinggi.
   - **Tren Keuangan 6 Bulan:** Grafik visual pendapatan vs pengeluaran historis.
   - **Filter Periode:** Pilih bulan ini, bulan lalu, hari ini, tahun ini, atau rentang tanggal kustom.

### B. Mengunduh Laporan Keuangan & Operasional
1. Di pojok kanan atas Executive Dashboard, klik tombol **"Export Keuangan (CSV)"**.
2. File CSV yang diunduh mencakup ringkasan eksekutif, daftar seluruh transaksi pembayaran terverifikasi, dan rincian pengeluaran per kategori.
3. Klik tombol **"Export Operasional (CSV)"** untuk mengunduh status inventaris kamar, kontrak aktif, dan tiket pemeliharaan.

---

## 2. Alur Kerja Admin Operasional (Admin)

Admin bertanggung jawab menangani operasional harian kamar, pemesanan, kontrak sewa, tagihan, pembayaran, dan pemeliharaan kos.

### A. Mengelola Kamar & Fasilitas
- Buka menu **Kamar** (`/admin/rooms`).
- Klik **Tambah Kamar** untuk mendaftarkan kamar baru (maksimal 10 kamar sesuai kapasitas PRD).
- Kelola fasilitas kamar pada menu **Fasilitas** (`/admin/facilities`).

### B. Mendaftarkan Penghuni Baru
- Buka menu **Penghuni** (`/admin/tenants`).
- Klik **Tambah Penghuni** dan masukkan nama, nomor WhatsApp aktif, NIK, dan kontak darurat.
- Sistem akan menerbitkan tautan aktivasi satu kali pakai (*single-use activation token*) yang dikirimkan ke penghuni untuk membuat password akun.

### C. Menangani Booking & Konversi Kontrak
- Buka menu **Booking** (`/bookings`).
- Saat ada permohonan kamar baru, statusnya adalah `pending`. Admin dapat:
  - Mengonfirmasi booking (`confirm`).
  - Mengonversi booking yang telah dikonfirmasi menjadi **Kontrak Sewa Aktif** (`convert to contract`).
  - Menetapkan tanggal mulai sewa, tanggal berakhir sewa, dan tanggal jatuh tempo bulanan (*billing anchor day*).

### D. Verifikasi Pembayaran & Pencatatan Tunai
- Buka menu **Pembayaran** (`/admin/payments`).
- Pada tab **Menunggu Verifikasi**, klik pembayaran untuk melihat bukti transfer yang diunggah oleh penghuni.
- Klik **Verifikasi Pembayaran** jika transfer valid, atau **Tolak** dengan alasan penolakan jika tidak valid.
- Jika penghuni membayar langsung secara tunai, klik tombol **Catat Bayar Tunai** (`/admin/payments/cash`).

### E. Pencatatan Pengeluaran Operasional
- Buka menu **Pengeluaran** (`/admin/expenses`).
- Klik **Catat Pengeluaran Baru**, pilih kategori beban (Listrik/Air/Internet/Kebersihan/Perbaikan), masukkan nominal, tanggal transaksi, nama vendor, dan upload bukti kuitansi (opsional).

### F. Menangani Laporan Kerusakan Kamar
- Buka menu **Maintenance** (`/admin/maintenance`).
- Tinjau tiket kerusakan yang dilaporkan penghuni.
- Ubah status menjadi `in_progress`, ubah tingkat prioritas (`urgent`, `high`, `medium`, `low`), dan tambahkan catatan perkembangan (catatan internal hanya terlihat oleh tim admin/owner).
- Setelah perbaikan selesai, tandai tiket sebagai `resolved`.

---

## 3. Alur Kerja Penghuni Kos (Tenant Portal)

Penghuni kos mengakses portal mandiri ramah seluler untuk mengelola kebutuhan sewa kamar mereka.

### A. Aktivasi Akun Pertama Kali
1. Buka tautan aktivasi yang diterima dari pengelola kos (format: `/activate?token=...`).
2. Buat password pribadi yang kuat dan konfirmasikan.
3. Setelah aktif, login ke aplikasi menggunakan email dan password yang baru dibuat.

### B. Dashboard Penyewa
Setelah login, penyewa diarahkan ke **Portal Penyewa** (`/tenant/dashboard`) yang menampilkan:
- Nomor kamar dan nomor kontrak aktif.
- Masa sewa aktif dan sisa hari masa tinggal.
- Tanggal jatuh tempo tagihan rutin bulanan (*billing anchor day*).
- Tiga kartu status keuangan: Total Ditagihkan, Total Pembayaran Terverifikasi, dan Sisa Tagihan yang Belum Lunas.
- Tombol aksi cepat: **Ajukan Perpanjangan** sewa dan **Ajukan Early Checkout**.

### C. Mengirim Bukti Pembayaran Sewa
1. Klik menu **Bayar Sewa** atau tombol **Kirim Bukti Pembayaran** (`/tenant/payment`).
2. Masukkan nominal pembayaran yang ditransfer.
3. Pilih metode pembayaran (Transfer Bank).
4. Masukkan catatan atau nomor referensi transaksi.
5. Unggah foto bukti transfer (JPG, PNG, atau PDF).
6. Klik **Kirim Pembayaran**. Status pembayaran akan tercatat sebagai `pending` hingga diverifikasi oleh admin kos.

### D. Melaporkan Kerusakan Fasilitas Kamar
1. Klik menu **Lapor Kerusakan** (`/tenant/maintenance`).
2. Klik **Buat Laporan Kerusakan Baru** (`/tenant/maintenance/create`).
3. Pilih kategori kerusakan (Kelistrikan, Plumbing/Air, AC/Elektronik, Furnitur, atau Fasilitas Umum).
4. Tuliskan judul keluhan dan deskripsi detail kendala yang dialami.
5. Pilih prioritas kendala dan lampirkan foto kerusakan jika ada.
6. Klik **Kirim Laporan Kerusakan**.
7. Penyewa dapat memantau respons dan perkembangan perbaikan langsung dari halaman tiket.

