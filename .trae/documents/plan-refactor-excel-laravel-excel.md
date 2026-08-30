# Plan: Refactor Excel Import/Export ke Laravel Excel

## Summary

Refactor fitur Excel dari client-side `xlsx` (SheetJS) ke server-side `maatwebsite/laravel-excel`. Saat ini hanya ada fitur import di `ImportStandarController` yang menggunakan `xlsx` di browser. Plan ini mencakup: refactor import, tambah template download, dan tambah export data.

## Current State Analysis

### Yang Sudah Ada

- **Import (client-side)**: [Import.vue](file:///home/sweet/code/Software-SPMI/resources/js/pages/Penetapan/Import.vue)
  - User pilih file → `FileReader` baca di browser → `XLSX.read()` parse ke JSON
  - JSON dikirim via Inertia POST ke `ImportStandarController::store()`
  - PrimeVue `FileUpload` (mode basic) untuk file selection
  - Preview data di `DataTable` sebelum submit
- **Backend**: [ImportStandarController.php](file:///home/sweet/code/Software-SPMI/app/Http/Controllers/Penetapan/ImportStandarController.php)
  - `index()` — render page dengan `periodes` (status != Selesai) dan `active_periode`
  - `store()` — terima JSON array (`data` + `periode_id`), validasi header, proses upsert dalam transaction
  - Header validasi: cek `Nama Standar`, `Kode Indikator`, `Isi Indikator`, `Target`, `Satuan` di first row
  - Lookup maps: `$unitNameMap`, `$unitTypeMap`, `$kategoriMap`, `$standarMap` (avoid N+1)
  - Pass 1: resolve/create `KategoriStandar` + `StandarDikti` → build `$indikatorUpserts` → `IndikatorMutu::upsert()` by `(standar_id, kode_indikator)`
  - Pass 2: resolve indikators → build `$targetUpserts` with unit resolution → `TargetUnit::upsert()` in chunks of 1000 by `(indikator_id, unit_kerja_id)`
  - Unit resolution: specific name → type (e.g. "fakultas") → all units (if empty)
  - Fallback columns: `Kode` → `Kode Indikator`, `Isi Indikator (Ambil Paling Kanan)` → `Isi Indikator`, `PIC / Unit Kerja` → `Unit Kerja`
  - Target only created if `nilaiTarget > 0`
- **Library**: `xlsx` v0.18.5 di [package.json](file:///home/sweet/code/Software-SPMI/package.json) (dependencies)
- **Routes**: [web.php](file:///home/sweet/code/Software-SPMI/routes/web.php) lines 35-36
  - `GET /penetapan/import-standar` → `ImportStandarController@index` → name `penetapan.standar.import.index`
  - `POST /penetapan/import-standar` → `ImportStandarController@store` → name `penetapan.standar.import.store`
  - Middleware: `auth`, `verified`, `role:Admin/LPM`

### Bug di Current Import.vue

1. **Line 37**: `readXlsx.utils.sheet_to_json(ws)` — `readXlsx` undefined, should be `XLSX`. File parsing always fails, caught by catch block.
2. **Lines 39-48**: Remap row keys to snake_case (`nama_standar`, `kode_indikator`, etc.) but controller expects original Excel header strings (`Nama Standar`, `Kode Indikator`). Header validation fails even if Bug 1 fixed.

Refactor ini akan menghilangkan kedua bug karena client-side parsing dihapus seluruhnya.

### Yang Belum Ada

- `maatwebsite/laravel-excel` tidak terinstall (cek [composer.json](file:///home/sweet/code/Software-SPMI/composer.json))
- `config/excel.php` tidak ada
- `app/Imports/` dan `app/Exports/` directories tidak ada
- Template download dari server
- Export data standar/indikator ke Excel
- Server-side file parsing

### Model Hierarchy

```
PeriodeAMI (1) ──< (N) StandarDikti (N) >── (1) KategoriStandar
                         |
                         | (1)
                         v
                    (N) IndikatorMutu
                         |
                         | (1)
                         v
                    (N) TargetUnit (N) >── (1) UnitKerja
```

Key fields:
- `StandarDikti`: `periode_id`, `kategori_id`, `nama_standar`
- `IndikatorMutu`: `standar_id`, `kode_indikator`, `isi_standar` (text), `jenis` (enum: IKU, IKT)
- `TargetUnit`: `indikator_id`, `unit_kerja_id`, `nilai_target` (float), `satuan`
- Unique constraints: `(standar_id, kode_indikator)` on `indikator_mutu`, `(indikator_id, unit_kerja_id)` on `target_unit`

### Frontend Patterns

- Inertia v3 + Vue 3 + PrimeVue 4 + Tailwind v4
- Wayfinder typed routes: `import { route } from '@/lib/route'`
- PrimeVue components auto-imported via `unplugin-vue-components` + `@primevue/auto-import-resolver`
- `useForm` from `@inertiajs/vue3` for form state
- `useToast` from `primevue/usetoast` for notifications
- Brand color: `#00479b`
- CRUD pattern: Dialog-based create/edit, toast feedback

### Test Patterns

- Pest PHP, file di `tests/Feature/`
- Factories tersedia untuk semua model: `UserFactory`, `RoleFactory`, `PeriodeAMIFactory`, `StandarDiktiFactory`, `KategoriStandarFactory`, `IndikatorMutuFactory`, `TargetUnitFactory`, `UnitKerjaFactory`
- `UserFactory`: `role_id` null by default, `email_verified_at` set
- `RoleFactory`: `nama_role` random from `['Admin/LPM', 'Pimpinan', 'Auditor', 'Auditee']`
- Role middleware `CheckRole`: cek `$request->user()->role->nama_role` against `$roles` parameter

---

## Target State

- **Import**: File `.xlsx` dikirim langsung ke server → Laravel Excel parse → proses data (same upsert logic)
- **Template**: User klik tombol → server generate `.xlsx` → download
- **Export**: User klik tombol → server query data → generate `.xlsx` → download
- **Hapus**: Dependensi `xlsx` dari `package.json`

---

## Phase 1: Install & Setup

### 1.1 Install Laravel Excel
```bash
composer require maatwebsite/laravel-excel
```

### 1.2 Publish config
```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config --no-interaction
```
- File: `config/excel.php` — pastikan timeout dan memory cukup untuk file besar

---

## Phase 2: Refactor Import

### 2.1 Buat Import Class
- **Command**: `php artisan make:import StandarImport --no-interaction`
- **File baru**: `app/Imports/StandarImport.php`
- **Implements**: `ToCollection`, `WithHeadingRow`, `WithValidation`, `SkipsEmptyRows`
- **Heading row**: Laravel Excel auto-normalisasi heading ke snake_case. `WithHeadingRow` membaca baris pertama sebagai header. Default formatter: `slug()` — mengubah `"Nama Standar"` → `nama_standar`. Gunakan `WithCustomHeadingRow` atau set `headingRowFormatter` ke `none` di config jika perlu preserve original keys.
- **Logic**:
  - Constructor inject `int $periodeId`
  - `collection(Collection $rows)` — iterasi baris, proses sama seperti `store()` sekarang:
    - Build lookup maps: `$unitNameMap`, `$unitTypeMap`, `$kategoriMap`, `$standarMap`
    - Pass 1: resolve/create `KategoriStandar` + `StandarDikti` → build `$indikatorUpserts` → `IndikatorMutu::upsert()` by `(standar_id, kode_indikator)`
    - Pass 2: resolve indikators → build `$targetUpserts` with unit resolution → `TargetUnit::upsert()` in chunks of 1000
    - Unit resolution: specific name → type → all units
    - Fallback: `kode` → `kode_indikator`, `isi_indikator_ambil_paling_kanan` → `isi_indikator`, `pic_unit_kerja` → `unit_kerja`
    - Target only if `nilai_target > 0`
  - `rules()` — validasi per-baris:
    - `nama_standar` → `required|string`
    - `kode_indikator` → `required|string`
    - `isi_indikator` → `required|string`
    - `jenis` → `nullable|string|in:IKU,IKT`
    - `target` → `nullable|numeric|min:0`
    - `satuan` → `nullable|string`
    - `kategori` → `nullable|string`
    - `unit_kerja` → `nullable|string`
  - Wrap dalam `DB::transaction()`
  - Return `ValidationException` dengan row number jika error (Laravel Excel auto-handle ini)

**Catatan heading normalization**: Laravel Excel default `headingRowFormatter` adalah `slug()`. Jadi header `"Nama Standar"` menjadi key `nama_standar`, `"Kode Indikator"` menjadi `kode_indikator`, dst. Access row values via `$row['nama_standar']` bukan `$row['Nama Standar']`. Alternatif: set `WithCustomValueBinder` atau override `headingRowFormatter()` return `'none'` untuk preserve original keys. Pilih approach snake_case karena lebih clean.

### 2.2 Update Controller
- **File**: [ImportStandarController.php](file:///home/sweet/code/Software-SPMI/app/Http/Controllers/Penetapan/ImportStandarController.php)
- **`store()` method** — refactor total:
  - Validasi file: `['file' => 'required|file|mimes:xlsx,xls|max:10240', 'periode_id' => 'required|exists:periode_ami,id']`
  - Panggil `Excel::import(new StandarImport($periodeId), $request->file('file'))`
  - Catch `ValidationException` dari Laravel Excel → return `redirect()->back()->withErrors($e->errors())->withInput()`
  - Catch `\Maatwebsite\Excel\Validators\ValidationException` untuk row-level errors → extract error messages with row numbers
  - Redirect ke `penetapan.standar.index` dengan success message
- **Hapus**: Seluruh logic parsing JSON, header validation, lookup maps, upsert logic (pindah ke `StandarImport`)
- **Imports**: Tambah `use Maatwebsite\Excel\Facades\Excel;`, `use App\Imports\StandarImport;`
- **Hapus imports**: `IndikatorMutu`, `KategoriStandar`, `StandarDikti`, `TargetUnit`, `UnitKerja`, `DB` (tidak lagi dipakai di controller)

### 2.3 Update Frontend
- **File**: [Import.vue](file:///home/sweet/code/Software-SPMI/resources/js/pages/Penetapan/Import.vue)
- **Hapus**:
  - `import * as XLSX from 'xlsx'` (line 7)
  - `uploadedData` ref (line 16)
  - `onFileSelect` function (lines 25-58) — seluruh FileReader + XLSX logic
  - Preview `DataTable` (lines 129-138) — tidak ada lagi client-side preview
  - `form.data` field
- **Ganti**:
  - `useForm({ file: null as File | null, periode_id: null as number | null })`
  - `onFileSelect(event)` — set `form.file = event.files?.[0]` saja, no parsing
  - `submitImport()` — validasi `form.file` dan `selectedPeriodeId`, lalu `form.post(route('penetapan.standar.import.store'), { ... })`
  - Inertia auto-handle `multipart/form-data` saat form berisi File object
- **Template**:
  - `FileUpload` tetap, `@select` handler cuma set `form.file`
  - Hapus preview DataTable, ganti dengan info nama file saja
  - Tambah tombol "Unduh Template Excel" (lihat Phase 3)
  - Tambah info format di card samping atau di bawah upload area
- **Error handling**: `onError` callback tetap, tampilkan error dari server (row-level validation errors)

### 2.4 Route Update
- **File**: [web.php](file:///home/sweet/code/Software-SPMI/routes/web.php)
- Route `POST /penetapan/import-standar` sudah ada (line 36), tidak perlu ubah
- Pastikan middleware `auth`, `verified`, `role:Admin/LPM` sudah cover (sudah, via group prefix `penetapan`)

---

## Phase 3: Template Download

### 3.1 Buat Export Class
- **Command**: `php artisan make:export StandarTemplateExport --no-interaction`
- **File baru**: `app/Exports/StandarTemplateExport.php`
- **Implements**: `FromCollection`, `ShouldAutoSize`, `WithHeadings`
- **Logic**:
  - `collection()` — return sample rows (2-3 baris contoh data realistis)
  - `headings()` — return header array: `["Kategori", "Nama Standar", "Kode Indikator", "Isi Indikator", "Jenis", "Target", "Satuan", "Unit Kerja"]`
  - Sample rows contoh:
    ```php
    return collect([
        ['Akademik', 'Standar Pendidikan', 'IKU-01', 'Persentase lulusan tepat waktu', 'IKU', 90, '%', 'Fakultas'],
        ['Akademik', 'Standar Pendidikan', 'IKT-01', 'Jumlah mahasiswa aktif', 'IKT', 500, 'Mahasiswa', 'Program Studi'],
    ]);
    ```

### 3.2 Tambah Route & Controller Method
- **File**: [web.php](file:///home/sweet/code/Software-SPMI/routes/web.php)
  - Tambah di dalam `penetapan` group (line 32-40):
    ```php
    Route::get('/import-standar/template', [ImportStandarController::class, 'template'])->name('standar.import.template');
    ```
  - Letakkan sebelum `Route::get('/import-standar', ...)` untuk avoid route conflict
- **File**: [ImportStandarController.php](file:///home/sweet/code/Software-SPMI/app/Http/Controllers/Penetapan/ImportStandarController.php)
  - Tambah method:
    ```php
    public function template()
    {
        return Excel::download(new StandarTemplateExport, 'template_import_standar.xlsx');
    }
    ```
  - Tambah imports: `use Maatwebsite\Excel\Facades\Excel;`, `use App\Exports\StandarTemplateExport;`

### 3.3 Update Frontend
- **File**: [Import.vue](file:///home/sweet/code/Software-SPMI/resources/js/pages/Penetapan/Import.vue)
- **Tambah tombol**: "Unduh Template Excel" di toolbar header (sebelah Select periode)
  ```vue
  <a :href="route('penetapan.standar.import.template')">
      <Button label="Unduh Template" icon="pi pi-download" severity="secondary" outlined />
  </a>
  ```
- **Tambah info**: Instruksi format di bawah upload area — header names + contoh data

---

## Phase 4: Export Data

### 4.1 Buat Export Class
- **Command**: `php artisan make:export StandarExport --no-interaction`
- **File baru**: `app/Exports/StandarExport.php`
- **Implements**: `FromCollection`, `WithHeadings`, `WithMapping`, `ShouldAutoSize`
- **Logic**:
  - `__construct(public int $periodeId)` — constructor property promotion
  - `collection()` — query `StandarDikti::with('kategori', 'indikatorMutus.targetUnits.unitKerja')->where('periode_id', $this->periodeId)->get()`
  - `headings()` — `["Kategori", "Nama Standar", "Kode Indikator", "Isi Indikator", "Jenis", "Target", "Satuan", "Unit Kerja"]`
  - `map($standar)` — flatten hierarchy: untuk setiap `StandarDikti`, iterasi `indikatorMutus`, untuk setiap `IndikatorMutu` iterasi `targetUnits`, return baris per target:
    ```php
    public function map($standar): array
    {
        $rows = [];
        foreach ($standar->indikatorMutus as $indikator) {
            foreach ($indikator->targetUnits as $target) {
                $rows[] = [
                    $standar->kategori->nama_kategori ?? '',
                    $standar->nama_standar,
                    $indikator->kode_indikator,
                    $indikator->isi_standar,
                    $indikator->jenis,
                    $target->nilai_target,
                    $target->satuan,
                    $target->unitKerja->nama_unit ?? '',
                ];
            }
            // Jika indikator tidak punya target, tetap tampilkan satu baris
            if ($indikator->targetUnits->isEmpty()) {
                $rows[] = [
                    $standar->kategori->nama_kategori ?? '',
                    $standar->nama_standar,
                    $indikator->kode_indikator,
                    $indikator->isi_standar,
                    $indikator->jenis,
                    '', '', '',
                ];
            }
        }
        return $rows;
    }
    ```

### 4.2 Tambah Route & Controller Method
- **File**: [web.php](file:///home/sweet/code/Software-SPMI/routes/web.php)
  - Tambah di dalam `penetapan` group:
    ```php
    Route::get('/standar/export', [StandarController::class, 'export'])->name('standar.export');
    ```
  - Letakkan sebelum `Route::resource('standar', ...)` (line 34) untuk avoid route conflict
- **File**: [StandarController.php](file:///home/sweet/code/Software-SPMI/app/Http/Controllers/Penetapan/StandarController.php)
  - Tambah method:
    ```php
    public function export(Request $request)
    {
        $request->validate(['periode_id' => 'required|exists:periode_ami,id']);
        return Excel::download(new StandarExport($request->periode_id), 'export_standar_dikti.xlsx');
    }
    ```
  - Tambah imports: `use Maatwebsite\Excel\Facades\Excel;`, `use App\Exports\StandarExport;`

### 4.3 Update Frontend
- **File**: [Standar/Index.vue](file:///home/sweet/code/Software-SPMI/resources/js/pages/Penetapan/Standar/Index.vue)
- **Tambah tombol**: "Export Excel" di toolbar (sebelah tombol "Tambah Standar")
  ```vue
  <a :href="route('penetapan.standar.export', { periode_id: selectedPeriode })">
      <Button label="Export Excel" icon="pi pi-file-export" severity="success" outlined />
  </a>
  ```
- Tombol ini GET request, bukan Inertia visit — browser handle download langsung

---

## Phase 5: Tests

### 5.1 Buat Feature Test untuk Import
- **Command**: `php artisan make:test ImportStandarTest --pest --no-interaction`
- **File**: `tests/Feature/ImportStandarTest.php`
- **Test cases**:
  1. `test_guest_redirected_to_login` — akses import page tanpa login
  2. `test_admin_can_access_import_page` — login as Admin/LPM, GET index, assert Ok
  3. `test_import_valid_xlsx_creates_data` — upload valid `.xlsx`, assert `StandarDikti`, `IndikatorMutu`, `TargetUnit` created di DB
  4. `test_import_invalid_file_format_returns_error` — upload `.csv` (bukan xlsx), assert validation error
  5. `test_import_missing_required_column_returns_error` — upload `.xlsx` dengan header salah, assert error message
  6. `test_template_download_returns_xlsx` — GET template route, assert response download + content-type correct
- **Setup**: Gunakan factories untuk create `User` + `Role` (Admin/LPM) + `PeriodeAMI` + `UnitKerja`. Untuk test file upload, gunakan `UploadedFile::fake()->createWithContent()` atau generate temp `.xlsx` dengan `maatwebsite/laravel-excel` di test.

### 5.2 Buat Feature Test untuk Export
- **Command**: `php artisan make:test ExportStandarTest --pest --no-interaction`
- **File**: `tests/Feature/ExportStandarTest.php`
- **Test cases**:
  1. `test_guest_redirected_to_login` — akses export route tanpa login
  2. `test_admin_can_export_standar` — login as Admin/LPM, create standar data via factories, GET export route, assert response download + content-type `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
  3. `test_export_filters_by_periode` — create data di 2 periode berbeda, export satu periode, assert hanya data periode tersebut

---

## Phase 6: Cleanup & Verification

### 6.1 Hapus Dependensi Lama
```bash
npm uninstall xlsx
```

### 6.2 Run Pint
```bash
vendor/bin/pint --dirty --format agent
```

### 6.3 Verification
```bash
npm run build
php artisan test --compact
```

### 6.4 Test Checklist
- [ ] Import: Upload file `.xlsx` → data masuk ke database
- [ ] Import: File kosong/gagal parse → error message muncul
- [ ] Import: Format kolom salah → validasi error muncul dengan row number
- [ ] Import: Duplicate data → upsert update, tidak insert double
- [ ] Template: Klik "Unduh Template" → file `.xlsx` terdownload
- [ ] Template: Buka di Excel → header + sample data benar
- [ ] Export: Klik "Export Excel" → file `.xlsx` terdownload
- [ ] Export: Buka di Excel → data sesuai filter periode
- [ ] Semua test `php artisan test --compact` pass
- [ ] `npm run build` sukses tanpa error
- [ ] `vendor/bin/pint --dirty --format agent` clean

---

## File Changes Summary

| Action | File | Description |
|--------|------|-------------|
| Install | `composer.json` | Tambah `maatwebsite/laravel-excel` via `composer require` |
| Create | `app/Imports/StandarImport.php` | Import class dengan upsert logic (pindah dari controller) |
| Create | `app/Exports/StandarTemplateExport.php` | Template download export dengan sample rows |
| Create | `app/Exports/StandarExport.php` | Data export class dengan mapping flatten hierarchy |
| Create | `config/excel.php` | Published config dari vendor |
| Edit | `app/Http/Controllers/Penetapan/ImportStandarController.php` | Refactor `store()` pakai `Excel::import`, tambah `template()` |
| Edit | `app/Http/Controllers/Penetapan/StandarController.php` | Tambah `export()` method |
| Edit | `routes/web.php` | Tambah route `standar.import.template` + `standar.export` |
| Edit | `resources/js/pages/Penetapan/Import.vue` | Hapus XLSX client-side, ganti server-side upload + tombol template |
| Edit | `resources/js/pages/Penetapan/Standar/Index.vue` | Tambah tombol export |
| Edit | `package.json` | Hapus `xlsx` via `npm uninstall` |
| Create | `tests/Feature/ImportStandarTest.php` | Feature tests untuk import + template |
| Create | `tests/Feature/ExportStandarTest.php` | Feature tests untuk export |

---

## Assumptions & Decisions

1. **Heading normalization**: Laravel Excel default `slug()` formatter mengubah `"Nama Standar"` → `nama_standar`. Import class akan akses row via snake_case keys. Alternatif: set `headingRowFormatter()` return `'none'` untuk preserve original keys. Dipilih snake_case karena lebih clean dan konsisten.

2. **Import class pakai `ToCollection` bukan `ToModel`**: Karena logic import kompleks (upsert, lookup maps, 2-pass processing, unit resolution), `ToCollection` memberi kontrol penuh. `ToModel` terlalu rigid untuk multi-table upsert.

3. **Export flatten per target**: Satu `StandarDikti` dengan 3 indikator yang masing-masing punya 2 target = 6 baris di export. Ini match dengan format import, sehingga export → edit → re-import round-trip konsisten.

4. **Template route diletakkan sebelum resource route**: `/import-standar/template` harus sebelum `/import-standar` untuk avoid route conflict. Laravel route matching first-match-wins.

5. **Export route diletakkan sebelum resource route**: `/standar/export` harus sebelum `/standar/{standar}` (resource show) untuk avoid conflict.

6. **Tombol export pakai `<a :href>` bukan Inertia visit**: Karena response adalah file download (binary), bukan HTML/Inertia page. Browser handle download langsung via GET.

7. **Test file `.xlsx` generation**: Untuk test import, generate temp `.xlsx` di test menggunakan `Excel::store()` atau `UploadedFile::fake()`. Alternatif: commit fixture file di `tests/fixtures/`.

8. **`SkipsEmptyRows` interface**: Tambah ke import class untuk skip baris kosong otomatis, menghilangkan manual `empty($namaStandar)` check.
