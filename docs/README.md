# SPMI — Dokumentasi

Sistem Penjaminan Mutu Internal (SPMI) untuk ITATS. Aplikasi ini mewujudkan
siklus **PPEPP** (Penetapan → Pelaksanaan → Evaluasi → Pengendalian → Peningkatan)
untuk pengelolaan standar mutu perguruan tinggi.

## Daftar Isi

- [Arsitektur](./arsitektur.md) — stack teknologi, struktur, alur domain, role, dan model data.
- [Pengembangan](./pengembangan.md) — setup, perintah, alur kerja, dan gotcha.

## Ringkasan Cepat

| Aspek | Nilai |
| --- | --- |
| Framework | Laravel 13 (PHP 8.4) |
| Frontend | Inertia v3 + Vue 3.5 + TypeScript + Tailwind v4 + PrimeVue 4 |
| Autentikasi | Laravel Fortify (registrasi publik dinonaktifkan) |
| Routing sisi frontend | Laravel Wayfinder (bukan Ziggy) |
| Testing | Pest 5, PHPStan (larastan) level 7, Pint |
| Excel | maatwebsite/laravel-excel (server-side) |
| Database | SQLite (dev & test), MySQL siap untuk produksi |
| Bahasa | Indonesia (`APP_LOCALE=id`) |

## Akun Demo

Setelah `php artisan migrate:fresh --seed`, tersedia 4 akun dengan password `password`:

| Email | Role |
| --- | --- |
| admin@spmi.ac.id | Admin/LPM |
| rektor@spmi.ac.id | Pimpinan |
| auditor@spmi.ac.id | Auditor |
| auditee@spmi.ac.id | Auditee |