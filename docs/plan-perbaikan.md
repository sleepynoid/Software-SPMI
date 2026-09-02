# Plan Perbaikan — Hasil Audit Bug & Improvement

Hasil pencarian bug dan perbaikan terhadap codebase SPMI. Tiap item punya status
pengerjaannya.

## Ringkasan

| # | Temuan | Severity | Status |
| --- | --- | --- | --- |
| 1 | Route export/template hilang di `lib/route.ts` | 🔴 Bug | ✅ fixed |
| 2 | RTL bisa diisi lintas unit (no ownership check) | 🔴 Security | ✅ fixed |
| 3 | KKA bisa disimpan lintas unit/periode (no ownership check) | 🔴 Security | ✅ fixed |
| 4 | Import/export inti tidak pernah diuji nyata (`Excel::fake`) | 🟠 Test gap | ✅ fixed |
| 5 | Duplikat `kode_indikator` → error 500 | 🟠 Bug | ✅ fixed |
| 6 | Cascade delete menghapus jejak audit tanpa guard | 🟠 Risiko data | ✅ fixed |
| 7 | Fallback `periode_id` tidak konsisten (cast null→0) | 🟡 Minor | ✅ fixed |
| 8 | Import throws exception type yang tidak di-catch controller | 🟠 Bug | ✅ fixed |
| 9 | Import baca header dgn key salah (`headingRowFormatter()` dead code) | 🔴 Bug | ✅ fixed |

## Detail & Solusi

### 1. Route export/template (fixed)

`lib/route.ts` memetakan nama route → fungsi Wayfinder, tapi tidak memuat
`penetapan.standar.export` & `penetapan.standar.import.template`. `resolveRoute()`
mengembalikan `/` diam-diam → tombol "Export Excel" & "Unduh Template" navigasi ke `/`.

**Solusi:** tambahkan kedua entry ke map, dengan forwarding query param.

### 2 & 3. Otorisasi store (fixed)

- `RtlController::store` hanya validasi field, tidak cek `$kka` milik unit user.
- `KKAController::store` tidak cek capaian termasuk unit yang diaudit.

**Solusi:** derive unit dari resource yang di-bind dan `abort(403)` bila tidak cocok
(pola sama seperti `EvaluasiDiriController`). KKA butuh `unit_kerja_id` dikirim dari
form frontend.

### 4. Test import/export nyata (fixed)

`ImportStandarTest` & `ExportStandarTest` memakai `Excel::fake()` sehingga
`StandarImport::collection()` dan `StandarExport::map()` tidak pernah dieksekusi CI.

**Solusi:** test import memakai file `.xlsx` asli (PhpSpreadsheet) tanpa fake, dan
assert baris masuk DB. Test export menambah uji mapping/flattening `StandarExport`
secara langsung.

### 5. Validasi unik kode indikator (fixed)

DB punya unique `(standar_id, kode_indikator)` tapi controller tidak validasi →
`QueryException` (500) saat duplikat.

**Solusi:** tambah rule `Rule::unique(...)->where('standar_id', ...)` pada
`store`/`update`.

### 6. Guard destructive delete (fixed)

Seluruh rantai FK `onDelete('cascade')`. Hapus `PeriodeAMI` / `KategoriStandar` /
`UnitKerja` menghapus standar→indikator→target→capaian→KKA→tindak lanjut + risalah.

**Solusi:** blokir delete bila masih ada data dependen (flash error). Delete
`Standar`/`Indikator` dibiarkan cascade (delegasi granular yang wajar).

### 7. Fallback periode_id (fixed)

`StandarController::index` dsb. memakai `?:` tanpa fallback akhir → bila semua periode
"Selesai", tampil data semua periode (atau kosong, tidak konsisten antar controller).

**Solusi:** standardisasi ke chain `input ?? non-Selesai ?? latest`.

### 8. Error handling import (fixed)

`StandarImport::collection()` melempar `Illuminate\Validation\ValidationException`,
tapi `ImportStandarController` hanya catch `Maatwebsite\Excel\Validators\ValidationException`.
Pesan "file kosong" / "format tidak valid" tidak tertangkap dengan benar.

**Solusi:** catch kedua jenis exception, mapping error sebagai `file`.

### 9. Import baca header dgn key salah (fixed)

`StandarImport` men-deklarasi `headingRowFormatter(): string { return 'none'; }`, tapi
maatwebsite/excel 4.0 **tidak memakai** method itu — heading di-slug oleh config
`excel.imports.heading_row.formatter` (default `slug`). Akibatnya key baris jadi
snake_case (`nama_standar`), sementara `rules()` & `collection()` memakai key ber-spasi
(`Nama Standar`) → import selalu gagal `validation.required` (tetap lolos uji karena
sebelumnya `Excel::fake()`).

**Solusi:** gunakan key snake_case di `rules()` dan `collection()`, hapus method
`headingRowFormatter()` yang dead. Dicatat sebagai rule di `.ai/rules/imports.md`.

### Test environment (fixed saat eksekusi)

- `.env` lokal tidak punya `APP_KEY` → semua HTTP test gagal `MissingAppKeyException`.
  Diperbaiki dengan `php artisan key:generate`.
- Test Inertia butuh Vite manifest → ditambah `withoutVite()` di `tests/TestCase.php`.

## Utang pra-ada (bukan dari perubahan ini, belum dikerjakan)

- **PHPStan level 7**: ±106 error pre-existing (`missingType.return`, `missingType.generics`,
  `nullsafe.neverNull`) di controller & export. `composer types:check` merah sejak
  sebelum perubahan ini.
- **`vue-tsc`**: puluhan error typing longgar (`any`) di `resources/js/pages`, plus
  `resources/js/types/navigation.ts` mengimpor `@lucide/vue` yang sudah dihapus saat
  migrasi ke PrimeVue.
- `.env` lokal memakai `DB_CONNECTION=pgsql` (port salah, server tak ada) — dev DB
  harusnya SQLite sesuai `AGENTS.md`.

## Sisa (opsional, tidak dieksekusi)

- Konsolidasi `lib/route.ts` ke Wayfinder langsung (hilangkan dobel lapisan routing).
- Paginasi daftar Master/Standar/Indikator (sebagian masih `->get()` penuh).
- Redundansi `bcrypt()` di `UserController::store` (cast `hashed` sudah menangani).
- Test regresi drb-drive: pastikan tiap route di `web.php` punya entry di `lib/route.ts`.
- Bereskan utang type PHPStan & vue-tsc (di atas).