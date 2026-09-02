# Pengembangan

## Setup Awal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install
npm run build
```

Alternatif sekali jalan: `composer setup`.

## Menjalankan Aplikasi

```bash
composer run dev        # jalankan Vite + server PHP bersamaan
# atau terpisah:
npm run dev             # Vite dev server (hot reload)
php artisan serve       # server PHP
```

## Perintah Penting

| Aksi | Perintah |
| --- | --- |
| Jalankan satu test | `php artisan test --compact --filter=testName` |
| Jalankan semua test | `php artisan test --compact` |
| CI penuh (lint + format + types + test) | `composer ci:check` |
| PHPStan | `composer types:check` |
| Format PHP | `vendor/bin/pint --dirty --format agent` |
| Lint frontend | `npm run lint` (fix) / `npm run lint:check` |
| Type-check frontend | `npm run types:check` (vue-tsc) |
| Format frontend | `npm run format` / `npm run format:check` |
| Reset database | `php artisan migrate:fresh --seed` |
| Regenerate route TS | `php artisan wayfinder:generate` |
| Membuat test | `php artisan make:test --pest NamaTest` |

## Alur Kerja (perubahan route/controller)

1. Ubah route/controller.
2. `php artisan wayfinder:generate` agar fungsi TypeScript di `@/actions`/`@/routes` diperbarui.
3. `vendor/bin/pint --dirty --format agent` (PHP) dan `npm run lint` + `npm run format` (frontend) jika menyentuh file terkait.
4. Tambah/perbarui test Pest, lalu `php artisan test --compact --filter=...`.

## Gotcha

- **`.env` vs config cache:** `composer test` menjalankan `config:clear` lebih dulu.
  Setelah mengubah `.env`, jalankan `php artisan config:clear`. Jangan `config:cache` saat development.
- **Database test** memakai SQLite in-memory (`phpunit.xml`); gunakan factory (tersedia untuk semua model).
- **Bahasa:** `APP_LOCALE=id`. String UI dan pesan (validasi, flash) berbahasa Indonesia.
- **Konvensi model:** `User` memakai atribut PHP 8 (`#[Fillable]`, `#[Hidden]`); model lain memakai `$fillable` tradisional. Kolom timestamp pakai `CarbonImmutable` (di-set global di `AppServiceProvider`).
- **PrimeVue:** komponen di-auto-import — tidak perlu `import` eksplisit.
- **Wayfinder import:** gunakan `import { ... } from '@/actions/...'` atau `@/routes/...`; jangan hardcode URL. Helper `route(..., params)` tersedia di `@/lib/route`.
- **Inertia shared props:** `auth.user` sudah mengandung `role` dan `unitKerja` eager-loaded; `flash.success` / `flash.error` tersedia untuk toast.
- **Vite manifest error** ("Unable to locate file in Vite manifest") → jalankan `npm run build` atau `npm run dev`.