# Arsitektur

## Stack Teknologi

Aplikasi ini adalah SPA (single-page application) Inertia dengan backend Laravel.

- **Backend:** Laravel 13, PHP 8.4, Fortify untuk autentikasi, maatwebsite/laravel-excel untuk import/export Excel.
- **Frontend:** Inertia v3 + Vue 3.5 (`@inertiajs/vue3`), TypeScript, Tailwind CSS v4, PrimeVue 4.
- **Routing frontend:** Wayfinder — impor fungsi route TypeScript dari `@/actions/` (berbasis controller) atau `@/routes/` (berbasis nama route). Jalankan `php artisan wayfinder:generate` setiap kali mengubah route.
- **Komponen UI:** PrimeVue di-auto-import lewat `unplugin-vue-components` + `PrimeVueResolver` — jangan import komponen secara manual.
- **Testing:** Pest 5 (feature test), PHPStan level 7 (`phpstan.neon` mencakup `app/`, `bootstrap/`, `config/`, `database/`, `routes/`), Pint.

## Struktur Direktori

```
app/
  Actions/Fortify/          # CreateNewUser, ResetUserPassword
  Concerns/                 # PasswordValidationRules, ProfileValidationRules
  Exports/                  # StandarExport, StandarTemplateExport (laravel-excel)
  Imports/                  # StandarImport (laravel-excel)
  Http/Controllers/         # Dikelompokkan per domain PPEPP + Master + Settings
  Http/Middleware/          # CheckRole (alias 'role'), HandleInertiaRequests
  Models/                   # 12 model domain
resources/js/
  layouts/                  # AppLayout, AuthLayout, settings/Layout
  pages/                    # Satu folder per halaman, dikelompokkan per domain
  composables/ lib/ types/  # Utilitas frontend
routes/
  web.php                   # Semua route SPMI (diberi nama per domain)
  settings.php              # Profil & keamanan (starter kit)
```

## Alur Domain: Siklus PPEPP

Sistem memetakan siklus mutu ke 4 peran. Setiap halaman digate lewat
middleware `role:` yang membaca `nama_role`.

| Tahap | Peran | Route prefix | Konten |
| --- | --- | --- | --- |
| Penetapan | Admin/LPM | `/penetapan` | Periode AMI, Standar Dikti, Indikator Mutu, Import Excel, Distribusi Target |
| Pelaksanaan | Auditee | `/pelaksanaan` | Evaluasi Diri (EDOM) — isi capaian per target |
| Evaluasi | Auditor | `/evaluasi` | Jadwal Audit, Kertas Kerja Audit (KKA) |
| Pengendalian | Auditee | `/pengendalian` | Isi RTL (tindak lanjut temuan) |
| Peningkatan | Pimpinan | `/peningkatan` | Risalah RTM (rapat tinjauan manajemen) |

Master data (User, Unit Kerja, Kategori Standar) hanya untuk Admin/LPM (`/master`).

**Peran (Role):** `Admin/LPM`, `Pimpinan`, `Auditor`, `Auditee` — disimpan di tabel
`roles.nama_role`. Otorisasi bukan pakai Spatie, melainkan middleware kustom
`App\Http\Middleware\CheckRole` (alias `role` di `bootstrap/app.php`).

## Model Data

Hierarki inti (untuk import/export Excel maupun alur PPEPP):

```
PeriodeAMI (1) ─< (N) StandarDikti (N) >─ (1) KategoriStandar
                        │
                        └─< (N) IndikatorMutu (jenis: IKU/IKT)
                                │
                                └─< (N) TargetUnit (N) >─ (1) UnitKerja
                                        │
                                        └─< (1) CapaianPelaksanaan
                                                │
                                                └─< (1) KertasKerjaAudit (KKA)
                                                        │
                                                        └─< (1) TindakLanjutPtk
```

Status & enum utama:

- `PeriodeAMI.status`: `Draft`, `Pelaksanaan EDOM`, `Audit Lapangan`, `RTM`, `Selesai`.
- `IndikatorMutu.jenis`: `IKU`, `IKT`.
- `KertasKerjaAudit.kategori_temuan`: `Sesuai`, `Melampaui`, `Observasi (OB)`, `KTS Minor`, `KTS Mayor`.
- `TindakLanjutPtk.status_verifikasi`: `Open`, `Menunggu Verifikasi`, `Closed`.
- `UnitKerja.jenis_unit`: `Fakultas`, `Program Studi`, `Biro`, `Lembaga`.

### Relasi Lain

- `User` belongsTo `Role`, belongsTo `UnitKerja` (kolom `role_id`, `unit_kerja_id`).
- `User.role` dan `User.unitKerja` di-eager-load di `HandleInertiaRequests::share()`
  sehingga tersedia di semua halaman via `usePage().props.auth.user`.
- `RisalahRtm` belongsTo `PeriodeAMI` dan `UnitKerja`.

## Autentikasi

- Fortify menangani login/logout/reset password. **Registrasi publik dinonaktifkan**
  (`config/fortify.php` hanya mengaktifkan `Features::resetPasswords()`).
- User hanya dibuat oleh Admin/LPM lewat `Master\UserController`.
- View Fortify dirender via Inertia (bukan Blade) di `FortifyServiceProvider`.
- Login di-throttle 5x/menit (`RateLimiter::for('login')`).

## Import/Export Excel

- Pakai `maatwebsite/laravel-excel` (server-side). Kelas di `app/Imports` dan `app/Exports`.
- `StandarImport` melakukan upsert dua-pass (`IndikatorMutu` by `(standar_id, kode_indikator)`,
  lalu `TargetUnit` by `(indikator_id, unit_kerja_id)` dalam chunk 1000).
- Route GET `standar.export` dan `standar.import.template` dideklarasikan **sebelum**
  `Route::resource(...)` masing-masing di `web.php` untuk menghindari konflik param `{standar}`.
- Tombol download di frontend memakai `<a :href>`, bukan Inertia visit (response binary).