# Deployment Checklist (Hostinger Shared Hosting)

Dokumen ini fokus ke minimum viable production untuk memastikan alur DISC stabil.

## 1) Persiapan Environment

- PHP: `8.2+` (sesuai `composer.json`)
- Database MySQL sudah dibuat di hPanel
- SSH diaktifkan agar bisa jalankan `artisan`

## 2) Struktur Folder Aman

Target paling aman:

- source Laravel di luar `public_html`
- isi folder `public/` yang diekspos lewat `public_html`

Jika harus mengikuti pola Hostinger manual deploy:

- upload source ke level di atas `public_html`
- pindahkan isi folder `public/` ke `public_html`
- sesuaikan path `index.php` dan `.htaccess` sesuai dokumentasi Hostinger

## 3) Konfigurasi `.env` Production

Wajib:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-anda`
- konfigurasi `DB_*` sesuai hPanel
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`

Lalu jalankan:

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\DiscQuestionSeeder --force
php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4) Permission Folder

Pastikan writable:

- `storage/`
- `bootstrap/cache/`

## 5) Smoke Test Wajib (DISC)

1. Masukkan kode sesi aktif di halaman `/`
2. Isi form metadata responden
3. Kerjakan minimal 1-2 soal
4. Submit sampai hasil keluar
5. Cek data masuk di admin:
   - `/admin/sessions`
   - `/admin/analytics`

## 6) Fallback Manual Scoring DISC (Excel)

Login admin, buka `Analytics & Export`, lalu download:

- `Export Bank Soal DISC`
- `Export Jawaban DISC (Manual)`

Atau per sesi:

- `Export Manual DISC` pada baris sesi DISC.

File CSV bisa langsung dibuka di Excel untuk scoring manual jika engine hasil otomatis bermasalah.

