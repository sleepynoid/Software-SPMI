# Plan: Refactor Excel Import/Export ke Laravel Excel

## Summary

Refactor fitur Excel dari client-side `xlsx` (SheetJS) ke server-side `maatwebsite/laravel-excel`. Saat ini hanya ada fitur import di `ImportStandarController` yang menggunakan `xlsx` di browser. Plan ini mencakup: refactor import, tambah template download, dan tambah export data.

## Current State

### Yang Sudah Ada
- **Import (client-side)**: `resources/js/pages/Penetapan/Import.vue`
  - User pilih file → `FileReader` baca di browser → `XLSX.read()` parse ke JSON
  - JSON dikirim via Inertia POST ke `ImportStandarController::store()`
  - Controller proses: upsert kategori, standar, indikator, target
- **Backend**: `app/Http/Controllers/Penetapan/ImportStandarController.php`
  - `index()` — render page dengan periodes
  - `store()` — terima JSON array, validasi, proses upsert dalam transaction
- **Library**: `xlsx` v0.18.5 di `package.json`
- **Template download**: Ada di old project (`XLSX.utils.aoa_to_sheet`) tapi belum di-migrate

### Yang Belum Ada
- Template download dari server
- Export data standar/indikator ke Excel
- Server-side file parsing

## Target State

- **Import**: File dikirim langsung ke server → Laravel Excel parse → proses data
- **Template**: User klik tombol → server generate `.xlsx` → download
- **Export**: User klik tombol → server query data → generate `.xlsx` → download
- **Hapus**: Dependensi `xlsx` dari `package.json`

---

## Phase 1: Install & Setup

### 1.1 Install Laravel Excel
```bash
composer require maatwebsite/laravel-excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

### 1.2 Publish config
- File: `config/excel.php` — pastikan timeout dan memory cukup untuk file besar

---

## Phase 2: Refactor Import

### 2.1 Buat Import Class
- **File baru**: `app/Imports/StandarImport.php`
- **Implements**: `ToCollection, WithHeadingRow, WithValidation`
- **Logic**:
  - `collection(Collection $rows)` — iterasi baris, proses sama seperti `store()` sekarang
  - `rules()` — validasi per-baris: `Kategori`, `Nama Standar`, `Kode Indikator`, `Isi Indikator`, `Target`, `Satuan`
  - `headingRowFormatter()` — normalisasi heading (handle spasi, kapitalisasi)
  - Inject `periode_id` via constructor
  - Return `ValidationException` dengan row number jika error

### 2.2 Update Controller
- **File**: `app/Http/Controllers/Penetapan/ImportStandarController.php`
- **`store()` method**:
  - Validasi file: `required|file|mimes:xlsx,xls|max:10240`
  - Panggil `Excel::import(new StandarImport($periodeId), $file)`
  - Handle `ValidationException` → return ke page dengan error detail per baris
  - Redirect ke index dengan success message

### 2.3 Update Frontend
- **File**: `resources/js/pages/Penetapan/Import.vue`
- **Hapus**: Import `xlsx`, `FileReader` logic, `uploadedData` ref, `onFileSelect` function
- **Ganti**: `useForm({ file: null, periode_id: null })` + `enctype="multipart/form-data"`
- **Template**: `<FileUpload mode="basic" />` → `@change` handler yang set `form.file`
- **Submit**: `form.post(route('penetapan.standar.import.store'))` dengan file langsung
- **Preview**: Setelah upload, tampilkan nama file + jumlah baris (dari response atau hide preview)

### 2.4 Route Update
- **File**: `routes/web.php`
- POST `/penetapan/import-standar` → `ImportStandarController@store` (sudah ada, tambah middleware auth)

---

## Phase 3: Template Download

### 3.1 Buat Export Class
- **File baru**: `app/Exports/StandarTemplateExport.php`
- **Implements**: `FromCollection, ShouldAutoSize, WithHeadings`
- **Logic**:
  - `collection()` — return sample rows (2-3 baris contoh)
  - `headings()` — return header array: `["Kategori", "Nama Standar", "Kode Indikator", "Isi Indikator", "Jenis", "Target", "Satuan", "Unit Kerja"]`

### 3.2 Tambah Route & Controller Method
- **File**: `routes/web.php`
  ```php
  Route::get('/penetapan/import-standar/template', [ImportStandarController::class, 'template'])->name('standar.import.template');
  ```
- **File**: `app/Http/Controllers/Penetapan/ImportStandarController.php`
  ```php
  public function template()
  {
      return Excel::download(new StandarTemplateExport, 'template_import_standar.xlsx');
  }
  ```

### 3.3 Update Frontend
- **File**: `resources/js/pages/Penetapan/Import.vue`
- **Tambah tombol**: "Unduh Template Excel" → `<a :href="route('penetapan.standar.import.template')">`
- **Tambah info**: Instruksi format di card samping (seperti old project)

---

## Phase 4: Export Data

### 4.1 Buat Export Class
- **File baru**: `app/Exports/StandarExport.php`
- **Implements**: `FromCollection, WithHeadings, WithMapping, ShouldAutoSize`
- **Logic**:
  - `__construct($periodeId)` — filter by periode
  - `collection()` — query `StandarDikti::with('kategori', 'indikatorMutus.targetUnits.unitKerja')` → flatten ke baris
  - `headings()` — `["Kategori", "Nama Standar", "Kode Indikator", "Isi Indikator", "Jenis", "Target", "Satuan", "Unit Kerja"]`
  - `map($row)` — mapping model ke array sesuai heading

### 4.2 Tambah Route & Controller Method
- **File**: `routes/web.php`
  ```php
  Route::get('/penetapan/standar/export', [StandarController::class, 'export'])->name('standar.export');
  ```
- **File**: `app/Http/Controllers/Penetapan/StandarController.php`
  ```php
  public function export(Request $request)
  {
      $periodeId = $request->periode_id;
      return Excel::download(new StandarExport($periodeId), 'export_standar_dikti.xlsx');
  }
  ```

### 4.3 Update Frontend
- **File**: `resources/js/pages/Penetapan/Standar/Index.vue`
- **Tambah tombol**: "Export Excel" di toolbar → `<a :href="route('penetapan.standar.export', { periode_id: selectedPeriode })">`

---

## Phase 5: Cleanup & Verification

### 5.1 Hapus Dependensi Lama
```bash
npm uninstall xlsx
```

### 5.2 Test Checklist
- [ ] Import: Upload file `.xlsx` → data masuk ke database
- [ ] Import: File kosong/gagal parse → error message muncul
- [ ] Import: Format kolom salah → validasi error muncul
- [ ] Template: Klik "Unduh Template" → file `.xlsx` terdownload
- [ ] Template: Buka di Excel → header + sample data benar
- [ ] Export: Klik "Export Excel" → file `.xlsx` terdownload
- [ ] Export: Buka di Excel → data sesuai filter periode
- [ ] Semua test `php artisan test` pass

### 5.3 Verification
```bash
npm run build
php artisan test
vendor/bin/pint --dirty --format agent
```

---

## File Changes Summary

| Action | File | Description |
|--------|------|-------------|
| Install | `composer.json` | Tambah `maatwebsite/laravel-excel` |
| Create | `app/Imports/StandarImport.php` | Import class untuk Excel |
| Create | `app/Exports/StandarTemplateExport.php` | Template download export |
| Create | `app/Exports/StandarExport.php` | Data export class |
| Edit | `app/Http/Controllers/Penetapan/ImportStandarController.php` | Refactor store() + tambah template() |
| Edit | `app/Http/Controllers/Penetapan/StandarController.php` | Tambah export() |
| Edit | `routes/web.php` | Tambah route template + export |
| Edit | `resources/js/pages/Penetapan/Import.vue` | Refactor ke server-side upload |
| Edit | `resources/js/pages/Penetapan/Standar/Index.vue` | Tambah tombol export |
| Edit | `package.json` | Hapus `xlsx` |
