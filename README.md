# Website Resmi PPLG SMKN 1 Bangsri

Website resmi program keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) SMKN 1 Bangsri. Dibangun dengan [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com), dan [Alpine.js](https://alpinejs.dev).

## Fitur

- Halaman publik: profil sekolah, kegiatan, prestasi, galeri, karya siswa, unit usaha, FAQ
- Dashboard Guru: kelola kegiatan, prestasi, dan karya siswa milik sendiri
- Dashboard Admin: kelola seluruh konten, akun guru, banner, mitra industri, dan pengaturan sistem
- Manajemen peran (role) menggunakan `spatie/laravel-permission`
- Log aktivitas pengguna

## Kebutuhan Sistem

- PHP >= 8.3
- Composer
- Node.js & npm
- MySQL (atau database lain yang didukung Laravel)

## Instalasi (Development)

```bash
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan konfigurasi database di file .env, lalu:
php artisan migrate --seed
npm install
npm run dev
```

Setelah seeding, cek output di terminal untuk kredensial akun admin & guru default (dibuat secara acak, bukan hardcoded).

## Build untuk Production

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Pastikan `.env` production memiliki `APP_ENV=production` dan `APP_DEBUG=false`.

## Lisensi

Proyek ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).