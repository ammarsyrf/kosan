# Panduan Backup & Restore — Kost Management System

Dokumen ini menjelaskan strategi pencadangan (*backup*) data, retensi, serta prosedur pemulihan (*disaster recovery*) aplikasi Kost Management System.

---

## 1. Komponen yang Dicadangkan

1. **Database Relasional (MySQL):**
   - Berisi seluruh tabel: pengguna, kamar, booking, kontrak sewa, invoice, pembayaran, pengeluaran, pemeliharaan, template notifikasi, dan log aktivitas.
   - Format: File SQL dump terkompresi dengan format penamaan `db_backup_YYYYMMDD_HHMMSS.sql`.
2. **Private Storage Uploads:**
   - Berisi berkas lampiran privat: bukti transfer pembayaran penyewa, foto kuitansi pengeluaran, dan lampiran tiket kerusakan kamar.
   - Format: File arsip ZIP dengan format penamaan `storage_uploads_backup_YYYYMMDD_HHMMSS.zip`.

---

## 2. Lokasi Penyimpanan & Keamanan Backup

- Direktori lokal: `/var/www/kost/storage/backups/`.
- Direktori ini diproteksi dengan `.htaccess` (`Deny from all`) dan tidak dapat diakses secara publik melalui URL web.
- **Off-site Backup:** Untuk lingkungan produksi berstandar enterprise, disarankan menyinkronkan file backup harian ke cloud storage eksternal (misalnya AWS S3, Google Cloud Storage, atau server rsync cadangan) menggunakan script rsync/cron terpisah.

---

## 3. Kebijakan Retensi (Retention Policy)

- Default retensi: **30 hari**.
- Script `cron/backup.php` secara otomatis memindai dan menghapus file backup yang berumur lebih dari batas retensi yang dikonfigurasi di `.env`:
  ```ini
  BACKUP_RETENTION_DAYS=30
  ```

---

## 4. Cara Menjalankan Backup Manual

Jalankan perintah berikut melalui terminal server:

```bash
# Backup penuh (Database SQL dump + Arsip file uploads)
php /var/www/kost/cron/backup.php --type=all

# Hanya backup database
php /var/www/kost/cron/backup.php --type=db

# Hanya backup file uploads
php /var/www/kost/cron/backup.php --type=files

# Mode simulasi (Dry-run)
php /var/www/kost/cron/backup.php --dry-run
```

---

## 5. Prosedur Pemulihan (Restoration Procedure)

> [!CAUTION]
> **PERINGATAN:** Pemulihan database akan menimpa data yang ada saat ini. Selalu buat backup darurat (*instant snapshot*) database aktif sebelum melakukan pemulihan!

### Langkah 1: Buat Backup Darurat Sebelum Restore
```bash
php /var/www/kost/cron/backup.php --type=db
```

### Langkah 2: Identifikasi File Backup yang Ingin Dipulihkan
Periksa daftar backup yang tersedia:
```bash
ls -lh /var/www/kost/storage/backups/
```
Pilih file database target, misalnya: `db_backup_20261109_020000.sql`.

### Langkah 3: Restore Database MySQL
Jalankan perintah restore MySQL:
```bash
mysql -h 127.0.0.1 -u kost_prod_user -p kost_management < /var/www/kost/storage/backups/db_backup_20261109_020000.sql
```
Masukkan password database saat diminta.

### Langkah 4: Restore File Upload Privat
Jika berkas unggahan juga perlu dipulihkan:
```bash
unzip -o /var/www/kost/storage/backups/storage_uploads_backup_20261109_020000.zip -d /var/www/kost/storage/uploads/
chown -R www-data:www-data /var/www/kost/storage/uploads/
chmod -R 770 /var/www/kost/storage/uploads/
```

### Langkah 5: Verifikasi Pemulihan
1. Buka browser dan login ke portal Admin / Owner.
2. Pastikan daftar kamar, kontrak sewa, dan tagihan invoice kembali sesuai status tanggal backup.
3. Buka halaman pembayaran dan periksa apakah bukti transfer dapat dilihat dengan benar.

