# Software-SPMI

> Sistem Penjaminan Mutu Internal (SPMI) — Monolith Web App berbasis Laravel 11 + Inertia.js + Vue 3

---

## Deskripsi Proyek

Software-SPMI adalah aplikasi web untuk mengelola siklus PPEPP (**P**enetapan, **P**elaksanaan, **E**valuasi, **P**engendalian, **P**eningkatan) dalam proses Penjaminan Mutu Internal perguruan tinggi.

Arsitektur menggunakan **Laravel Monolith + Inertia.js** — tidak ada REST API terpisah. Data dirender langsung dari server ke komponen Vue melalui Inertia props, sehingga navigasi terasa seperti SPA tanpa overhead manajemen token.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11, PHP 8.4 |
| Frontend | Vue 3 (Composition API), Inertia.js v3 |
| UI Library | PrimeVue, Tailwind CSS |
| Package Manager | **Bun** (pengganti npm/pnpm) |
| Build Tool | Vite 5 |
| Database | MySQL (production) / SQLite (testing) |
| Testing | PHPUnit 11 |

---

## Roles & Akses

| Role | Halaman Sheet | Keterangan |
|---|---|---|
| `Pelaksanaan` | ✅ Input bukti pelaksanaan | Tahap pertama PPEPP |
| `Evaluasi` | ✅ Input evaluasi & adjusment | Berdasarkan bukti pelaksanaan |
| `Pengendalian` | ✅ Input temuan, akar masalah, RTL | Berdasarkan hasil evaluasi |
| `Peningkatan` | ✅ Input komentar peningkatan | Tahap akhir PPEPP |
| `Admin` | ✅ Manajemen user & role | Dashboard admin |
| `SuperUser` | ✅ Akses semua sheet | Read-only lintas jurusan |

---

## Persyaratan

- **PHP 8.4+** — [Download](https://www.php.net/downloads)
- **Composer** — [Download](https://getcomposer.org/download/)
- **Bun** — [Download](https://bun.sh) *(pengganti npm/pnpm)*
- **MySQL 8+** atau MariaDB

---

## Langkah Setup

### 1. Clone Repository

```bash
git clone https://github.com/Burzess/Software-SPMI.git
cd Software-SPMI
```

### 2. Salin File `.env`

```bash
cp .env.example .env
```

### 3. Install Dependensi PHP

```bash
composer install
```

### 4. Install Dependensi JavaScript

```bash
bun install
```

### 5. Generate Kunci Aplikasi

```bash
php artisan key:generate
```

### 6. Konfigurasi Database

Edit `.env` sesuai database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spmi_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 7. Jalankan Migrasi Database

```bash
php artisan migrate
```

### 8. Jalankan Seeder (Opsional — Data Awal)

```bash
php artisan db:seed --class=UserSeeder
```

---

## Menjalankan Aplikasi

### Server Laravel (Backend)

```bash
php artisan serve
```

Server berjalan di `http://127.0.0.1:8000`.  
Jika port `8000` sudah dipakai, Laravel otomatis beralih ke `8001`.

### Server Pengembangan Vite (Frontend)

```bash
bun dev
```

---

## Menjalankan Tests

### Jalankan Semua Test

```bash
php artisan test
```

### Jalankan Test Spesifik

```bash
# Hanya test autentikasi
php artisan test --filter=AuthTest

# Hanya test submit pelaksanaan
php artisan test --filter=PelaksanaanSubmitTest

# Hanya test halaman sheet
php artisan test --filter=SheetPageTest
```

### Struktur Test

```
tests/
├── Feature/
│   ├── Auth/
│   │   └── AuthTest.php           # Login, logout, redirect
│   ├── Sheet/
│   │   └── SheetPageTest.php      # Akses halaman sheet, role-based data
│   ├── Pelaksanaan/
│   │   └── PelaksanaanSubmitTest.php
│   ├── Evaluasi/
│   │   └── EvaluasiSubmitTest.php
│   ├── Pengendalian/
│   │   └── PengendalianSubmitTest.php
│   └── Peningkatan/
│       └── PeningkatanSubmitTest.php
└── Unit/                          # (reserved untuk unit test murni)
```

> Test menggunakan **SQLite in-memory** dengan trait `RefreshDatabase` — tidak mempengaruhi database development Anda.

---

## Arsitektur

```
Browser → [GET /sheet/TI/2024/pendidikan]
    → Laravel Router → Auth Middleware
    → SheetController::show()
        → (berdasarkan role) PelaksanaanController::getPelaksanaanData()
    → Inertia::render('sheet', ['sheetData' => $data])
    → Blade app.blade.php (mounting point)
    → Vue app.js → Pages/sheet.vue menerima props
```

Semua submit data menggunakan `router.post()` dari `@inertiajs/vue3`, dan controller merespons dengan `back()->with('success', ...)`.

---

## Catatan Keamanan

- **Axios `1.14.1` dan `0.30.4` diblokir permanen** via `overrides` di `package.json` (versi yang terkompromasi pada insiden supply chain Maret 2026).
- Versi axios yang digunakan: `^1.15.0` (patch CVE-2025-27152, CVE-2025-58754, CVE-2025-62718).
- Autentikasi menggunakan **Cookie Session Laravel** — tidak ada token API yang exposed ke localStorage.
