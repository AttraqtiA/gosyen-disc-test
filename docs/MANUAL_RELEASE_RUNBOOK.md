# Manual Release Runbook (Hostinger Shared Hosting)

Tujuan: update aplikasi tanpa merusak data MySQL produksi.

## Prinsip Wajib

1. Jangan edit migration lama yang sudah pernah dijalankan di production.
2. Selalu buat migration baru untuk perubahan schema.
3. Migration harus bersifat forward-only (ada `down()`, tapi rencana utama tetap maju, bukan rollback di production).
4. Backup database sebelum deploy.

## Alur Aman per Release

1. **Siapkan release branch**
   - Pastikan perubahan lolos test lokal.
   - Pastikan migration baru bisa dijalankan di salinan DB lokal.

2. **Backup production DB (wajib)**
   - Export DB dari hPanel sebelum release.
   - Simpan nama file backup dengan timestamp.

3. **Aktifkan maintenance mode**
   - `php artisan down --retry=60`

4. **Update kode**
   - via Git Deploy / pull manual / upload file terkontrol.

5. **Install dependency dan migrate**
   - `composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction`
   - `php artisan migrate --force`

6. **Refresh cache aplikasi**
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`

7. **Smoke test singkat**
   - akses `/`
   - jalankan 1 alur tes DISC sampai hasil
   - cek admin export CSV

8. **Buka maintenance**
   - `php artisan up`

9. **Catat release untuk audit**
   - `php artisan release:note v1.2.0 --status=success --migrated=1 --commit=abc123 --operator=nama-operator --target-env=production --notes="Release DISC export fix"`

## Jika Gagal di Tengah Deploy

1. Tetap mode maintenance.
2. Restore kode ke release sebelumnya.
3. Restore DB dari backup (jika migration sudah sempat mengubah data/schema penting).
4. `php artisan optimize:clear && php artisan up`

## Catatan untuk Hostinger Premium

- Biasanya tersedia SSH, Git deployment, dan auto-deploy webhook.
- Ini membantu otomatisasi deploy, tapi bukan pengganti CI/CD penuh (test/build/quality gate tetap perlu Anda jalankan dari sisi repo/dev machine).
