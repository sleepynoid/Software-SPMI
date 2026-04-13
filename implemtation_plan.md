# Implementation Plan: Database Improvement & Tech Stack Update

## Latar Belakang

Analisa mendalam terhadap seluruh migration, model, dan controller pada project **Software-SPMI** menemukan sejumlah masalah struktural pada database dan penggunaan tech stack yang perlu di-improve untuk skalabilitas dan maintainabilitas jangka panjang.

---

## BAGIAN 1 — Analisa Database Saat Ini

### 1.1 ERD Teks (Relasi Antar Tabel)

```
users
  └─ id, name, email, password, role (string), timestamps

sheets
  └─ id, jurusan (string), periode (string), tipe_sheet (enum), note, timestamps
  └─ [⚠️ tidak ada FK ke users | jurusan bukan FK ke tabel referensi]

penetapans
  └─ id, id_sheet (FK→sheets), timestamps
  └─ [⚠️ tidak ada kolom data sendiri, hanya wrapper]

standars
  └─ id, id_penetapan (integer, ⚠️ BUKAN unsignedBigInteger, tidak ada FK constraint!), note, tipe (enum), timestamps

indikators
  └─ id, id_standar (FK→standars), note, timestamps

targets
  └─ id, id_indikator (FK→indikators), value (float), timestamps

pelaksanaans
  └─ id, id_sheet (integer, ⚠️ BUKAN FK!), timestamps
  └─ [⚠️ relasi ke sheets tidak ada constraint]

bukti_pelaksanaans
  └─ id, komentar (longtext), edited_by (string), id_pelaksanaan (FK→pelaksanaans), id_indikator (FK→indikators), timestamps

evaluasis
  └─ id, id_sheet (integer, ⚠️ BUKAN FK!), timestamps
  └─ [⚠️ relasi ke sheets tidak ada constraint]

bukti_evaluasis
  └─ id, adjustment (enum), komentar (string ⚠️ seharusnya text), edited_by, id_evaluasi (FK→evaluasis), id_bukti_pelaksanaan (FK→bukti_pelaksanaans), timestamps

bukti_pengendalians
  └─ id, temuan (string ⚠️), akar_masalah (string ⚠️), rtl (string ⚠️), pelaksanaan_rtl (string ⚠️), edited_by, id_bukti_evaluasi (FK→bukti_evaluasis), timestamps

peningkatans
  └─ id, komentar (string ⚠️), id_pengendalian (unsignedBigInteger, ⚠️ TIDAK ADA FK constraint!), timestamps

links
  └─ id, judul_link, link, tipe_link (enum), id_bukti (unsignedBigInteger ⚠️ TIDAK ADA FK!, ambigu antara Pelaksanaan/Evaluasi), timestamps

api_logs
  └─ id, user_id (FK→users nullable), method, url, request_headers, request_body, status_code, response_body, ip_address, user_agent, response_time, timestamps
```

---

### 1.2 Masalah Database yang Ditemukan

#### 🔴 Kritis — Integritas Referensial Rusak

| Tabel | Kolom | Masalah |
|---|---|---|
| `standars` | `id_penetapan` | Tipe `integer` biasa, **tidak ada foreign key constraint** |
| `pelaksanaans` | `id_sheet` | Tipe `integer` biasa, **tidak ada foreign key constraint** |
| `evaluasis` | `id_sheet` | Tipe `integer` biasa, **tidak ada foreign key constraint** |
| `peningkatans` | `id_pengendalian` | `unsignedBigInteger` tapi **tidak ada foreign key constraint** |
| `links` | `id_bukti` | `unsignedBigInteger` **ambigu** — bisa merujuk ke `bukti_pelaksanaans` ATAU `bukti_evaluasis`, tidak ada FK |

#### 🔴 Kritis — Desain Tabel Ambigu

**Tabel `links` polymorphic tidak proper:**  
Kolom `tipe_link` digunakan untuk membedakan jenis bukti (`Pelaksanaan` vs `Evaluasi`), namun kolom `id_bukti` tidak memiliki FK yang jelas. Ini adalah pola *polymorphic association* yang diimplementasi secara manual tanpa struktur yang benar.

```sql
-- Saat ini:
links: id_bukti (integer, TANPA FK) + tipe_link (enum)

-- Masalah: DELETE bukti_pelaksanaans tidak akan cascade ke links
-- karena tidak ada FK constraint
```

**Tabel `penetapans` hanya wrapper kosong:**  
Tabel ini hanya memiliki `id` dan `id_sheet`, tidak menyimpan data apapun. Fungsinya bisa digabung ke `sheets`.

#### 🟡 Sedang — Tipe Data Tidak Tepat

| Tabel | Kolom | Masalah | Rekomendasi |
|---|---|---|---|
| `bukti_evaluasis` | `komentar` | `string` (255 char) | Gunakan `text` atau `longText` |
| `bukti_pengendalians` | `temuan`, `akar_masalah`, `rtl`, `pelaksanaan_rtl` | `string` (255 char) | Gunakan `text` — field ini kemungkinan panjang |
| `peningkatans` | `komentar` | `string` (255 char) | Gunakan `text` |
| `targets` | `value` | `float` | Gunakan `decimal(10,2)` untuk presisi angka |
| `sheets` | `jurusan` | `string` bebas | Normalkan ke tabel `jurusans` |
| `users` | `role` | `string` bebas | Gunakan `enum` atau tabel `roles` |

#### 🟡 Sedang — Normalisasi Kurang

1. **`jurusan` disimpan sebagai string di `sheets`** — tidak ada tabel referensi, rentan typo dan inkonsistensi data
2. **`periode` disimpan sebagai string** — format tidak standar (bisa "2024", "2024/2025", "Ganjil 2024", dll.)
3. **`edited_by` disimpan sebagai string nama** — seharusnya FK ke `users.id` untuk konsistensi
4. **`tipe_sheet` enum hanya 3 nilai** — tapi kode controller memperlakukannya berbeda dengan `tipe` standar (input/proses/output)

#### 🟢 Sudah Baik
- Primary key menggunakan `bigIncrements` (auto)
- `api_logs` sudah sangat lengkap strukturnya
- `bukti_pengendalians` sudah ada FK ke `bukti_evaluasis`
- Sebagian besar tabel menggunakan `cascade` on delete

---

### 1.3 Masalah di Model Eloquent

| Model | Masalah |
|---|---|
| `Sheet` | Tidak ada relasi `hasOne(Penetapan)`, `hasOne(Pelaksanaan)`, dll. |
| `Penetapan` | Tidak ada `belongsTo(Sheet)` |
| `Indikator` | `target()` didefinisikan sebagai `hasMany` tapi seharusnya `hasOne` (1 indikator = 1 target) |
| `BuktiEvaluasi` | `link()` salah — menggunakan `id_bukti_pelaksanaan` bukan `id_bukti` |
| `link` | Nama class huruf kecil, melanggar PSR-4 convention |
| Semua model | Tidak ada relasi lengkap — semua join dilakukan secara manual di controller |

---

## BAGIAN 2 — Rencana Perbaikan

### Overview Fase

```
Fase 1: Perbaikan Database Struktural      [~1-2 hari]
Fase 2: Refactor Model & Controller        [~2-3 hari]
Fase 3: Update Tech Stack Backend          [~1 hari]
Fase 4: Update Tech Stack Frontend         [~2-3 hari]
```

---

## Fase 1 — Perbaikan Database Struktural

### 1.1 Migration Baru: Tambah Foreign Key yang Hilang

Buat migration baru (non-destructive) untuk menambahkan constraint:

```php
// [NEW] database/migrations/2026_xx_xx_add_missing_fk_constraints.php

// Perbaiki standars.id_penetapan
$table->unsignedBigInteger('id_penetapan')->change();
$table->foreign('id_penetapan')->references('id')->on('penetapans')->onDelete('cascade');

// Perbaiki pelaksanaans.id_sheet
$table->unsignedBigInteger('id_sheet')->change();
$table->foreign('id_sheet')->references('id')->on('sheets')->onDelete('cascade');

// Perbaiki evaluasis.id_sheet
$table->unsignedBigInteger('id_sheet')->change();
$table->foreign('id_sheet')->references('id')->on('sheets')->onDelete('cascade');

// Perbaiki peningkatans.id_pengendalian
$table->foreign('id_pengendalian')->references('id')->on('bukti_pengendalians')->onDelete('cascade');
```

### 1.2 Migration Baru: Tambah Tabel Referensi

```php
// [NEW] database/migrations/2026_xx_xx_create_jurusans_table.php
Schema::create('jurusans', function (Blueprint $table) {
    $table->id();
    $table->string('kode')->unique();     // e.g. "TI", "SI", "SK"
    $table->string('nama');               // e.g. "Teknik Informatika"
    $table->string('jenjang');            // e.g. "S1", "D3"
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Tambahkan FK di sheets setelah tabel jurusans dibuat
$table->unsignedBigInteger('jurusan_id')->after('jurusan')->nullable();
$table->foreign('jurusan_id')->references('id')->on('jurusans');
```

### 1.3 Migration Baru: Perbaiki Tipe Data

```php
// [NEW] database/migrations/2026_xx_xx_fix_column_types.php

// bukti_evaluasis.komentar: string → text
$table->text('komentar')->change();

// bukti_pengendalians: semua string → text
$table->text('temuan')->change();
$table->text('akar_masalah')->change();
$table->text('rtl')->change();
$table->text('pelaksanaan_rtl')->change();

// peningkatans.komentar: string → text
$table->text('komentar')->change();

// targets.value: float → decimal
$table->decimal('value', 10, 2)->change();

// Tambah edited_by FK (opsional, bisa string saja)
// Tambah kolom user_id ke bukti_pelaksanaans, bukti_evaluasis, dll.
$table->unsignedBigInteger('user_id')->nullable()->after('edited_by');
$table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
```

### 1.4 Perbaiki Tabel `links` (Polymorphic yang Benar)

```php
// [NEW] database/migrations/2026_xx_xx_fix_links_table.php

// Opsi A: Pisahkan jadi 2 tabel terpisah (lebih eksplisit)
Schema::create('bukti_pelaksanaan_links', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('id_bukti_pelaksanaan');
    $table->string('judul_link');
    $table->string('url');
    $table->timestamps();
    $table->foreign('id_bukti_pelaksanaan')->references('id')->on('bukti_pelaksanaans')->onDelete('cascade');
});

Schema::create('bukti_evaluasi_links', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('id_bukti_evaluasi');
    $table->string('judul_link');
    $table->string('url');
    $table->timestamps();
    $table->foreign('id_bukti_evaluasi')->references('id')->on('bukti_evaluasis')->onDelete('cascade');
});

// Opsi B: Gunakan Eloquent Polymorphic resmi Laravel
// linkable_type (string) + linkable_id (unsignedBigInteger) + unique index
```

> [!IMPORTANT]
> **Diperlukan keputusan dari developer**: Pilih **Opsi A** (2 tabel terpisah, lebih eksplisit dan mudah di-query) atau **Opsi B** (1 tabel polymorphic Laravel, lebih ringkas tapi sedikit lebih kompleks). Saya merekomendasikan **Opsi A** karena lebih mudah dibaca dan di-maintain.

### 1.5 Pertimbangkan Gabungkan/Hapus Tabel Wrapper

Tabel `penetapans`, `pelaksanaans`, `evaluasis` saat ini hanya sebagai **"penanda fase"** — mereka tidak menyimpan data bermakna. Ada dua pilihan:

> [!IMPORTANT]
> **Opsi 1 (Recommended)**: Pertahankan tabel-tabel ini sebagai anchor fase PPEPP, tapi tambahkan kolom metadata berguna seperti `status`, `submitted_at`, `submitted_by`, `catatan`.  
> **Opsi 2**: Hapus tabel wrapper, dan tambahkan kolom `fase` langsung di `sheets` — tapi ini mempersulit tracking status per fase.

---

## Fase 2 — Refactor Model & Controller

### 2.1 Perbaiki Model Eloquent

#### [MODIFY] `app/Models/Sheet.php`
Tambahkan semua relasi yang hilang:
```php
public function penetapan() { return $this->hasOne(Penetapan::class, 'id_sheet'); }
public function pelaksanaan() { return $this->hasOne(Pelaksanaan::class, 'id_sheet'); }
public function evaluasi() { return $this->hasOne(Evaluasi::class, 'id_sheet'); }
public function jurusan() { return $this->belongsTo(Jurusan::class); }
```

#### [MODIFY] `app/Models/Indikator.php`
Ubah `hasMany(Target)` → `hasOne(Target)`:
```php
public function target() { return $this->hasOne(Target::class, 'id_indikator'); }
public function buktiPelaksanaan() { return $this->hasOne(BuktiPelaksanaan::class, 'id_indikator'); }
```

#### [MODIFY] `app/Models/link.php`
Rename class `Link` (PascalCase), perbaiki relasi polymorphic.

### 2.2 Refactor Controller — Ganti `Model::all()` dengan Eager Loading

Contoh perbaikan `getPelaksanaan()`:

```php
// ❌ SEBELUM (N+1 problem)
$indikator = Indikator::all();
$target = Target::all();
$bukti = BuktiPelaksanaan::all();
// ... nested foreach manual

// ✅ SESUDAH (1 query dengan eager loading)
$sheet = Sheet::with([
    'penetapan.standars.indikators.target',
    'penetapan.standars.indikators.buktiPelaksanaan',
    'pelaksanaan'
])
->where('jurusan', $jurusan)
->where('periode', $periode)
->where('tipe_sheet', $tipePendidikan)
->firstOrFail();
```

---

## Fase 3 — Update Tech Stack Backend

### 3.1 Database: SQLite → MySQL (Production)

Saat ini default menggunakan SQLite. Untuk production:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spmi_db
DB_USERNAME=spmi_user
DB_PASSWORD=secret
```

Jalankan:
```bash
php artisan migrate:fresh --seed
```

### 3.2 Tambah Laravel Telescope (Monitoring Dev)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3.3 Tambah Laravel Pint + PHPStan (Code Quality)

```bash
composer require --dev phpstan/phpstan
./vendor/bin/pint  # sudah ada di composer.json
```

### 3.4 Tambah API Resource Classes

Ganti array manual di controller dengan proper `JsonResource`:
```bash
php artisan make:resource PelaksanaanResource
php artisan make:resource SheetResource  # sudah ada tapi belum dipakai
```

---

## Fase 4 — Update Tech Stack Frontend

### 4.1 Pinia — State Management

Saat ini tidak ada state management di frontend. Setiap navigasi memanggil `/api/user`.

```bash
npm install pinia
```

Buat store:
```
resources/js/stores/
  ├── auth.js       # User session, role, token
  ├── sheet.js      # Data sheets yang sedang aktif
  └── ui.js         # Loading states, toast notifications
```

### 4.2 Hapus Dependency Tidak Perlu

| Package | Status | Alasan |
|---|---|---|
| `bootstrap` + `bootstrap-vue` | ❌ Hapus | Sudah pakai PrimeVue + Tailwind |
| `bootstrap-vue` | ❌ Hapus | Tidak kompatibel Vue 3 dengan baik |
| `crypto-js` | ⚠️ Review | Digunakan untuk apa? Bisa dihapus jika tidak perlu |
| `vue-loader` | ❌ Hapus | Sudah ditangani `@vitejs/plugin-vue` |
| `xlsx` | ⚠️ Review | Apakah masih dipakai di frontend? Bisa pindah ke backend |
| `ldrs` | ⚠️ Review | Loading animasi — bisa diganti PrimeVue ProgressSpinner |

### 4.3 Update Routing — Tambah Pinia Auth Guard

```javascript
// Sebelum: setiap route = 1 API call
const isAuthenticated = async () => await axios.get('/api/user');

// Sesudah: cek dari store, hanya fetch sekali
const authStore = useAuthStore();
if (!authStore.isLoaded) await authStore.fetchUser();
return authStore.user;
```

### 4.4 Tambah Vue DevTools Kompatibilitas

Pastikan `app.js` mengaktifkan devtools di development:
```javascript
if (import.meta.env.DEV) {
    app.config.devtools = true;
}
```

---

## Ringkasan Perubahan File

### Backend

| Aksi | File |
|---|---|
| [NEW] | `database/migrations/2026_xx_add_missing_fk_constraints.php` |
| [NEW] | `database/migrations/2026_xx_create_jurusans_table.php` |
| [NEW] | `database/migrations/2026_xx_fix_column_types.php` |
| [NEW] | `database/migrations/2026_xx_fix_links_table.php` |
| [NEW] | `database/migrations/2026_xx_add_metadata_to_fase_tables.php` |
| [MODIFY] | `app/Models/Sheet.php` — tambah relasi |
| [MODIFY] | `app/Models/Penetapan.php` — tambah relasi |
| [MODIFY] | `app/Models/Indikator.php` — perbaiki hasMany→hasOne |
| [MODIFY] | `app/Models/link.php` → rename `Link.php` |
| [MODIFY] | `app/Http/Controllers/PelaksanaanController.php` — eager loading |
| [MODIFY] | `app/Http/Controllers/EvaluasiController.php` — eager loading |
| [MODIFY] | `app/Http/Controllers/PengendalianController.php` — eager loading |
| [MODIFY] | `app/Http/Controllers/PeningkatanController.php` — eager loading |
| [MODIFY] | `app/Http/Controllers/AccountController.php` — perbaiki logout |
| [NEW] | `app/Http/Resources/PelaksanaanResource.php` |
| [NEW] | `app/Http/Resources/SheetResource.php` (perbaiki yang ada) |
| [DELETE] | `app/Http/Controllers/testcontroller.php` |

### Frontend

| Aksi | File |
|---|---|
| [NEW] | `resources/js/stores/auth.js` |
| [NEW] | `resources/js/stores/sheet.js` |
| [MODIFY] | `resources/js/app.js` — tambah Pinia |
| [MODIFY] | `resources/js/router.js` — gunakan store untuk auth guard |
| [MODIFY] | `package.json` — hapus dependency tidak perlu |

---

## Pertanyaan untuk Keputusan

> [!IMPORTANT]
> **Q1**: Untuk tabel `links` — pilih Opsi A (2 tabel terpisah) atau Opsi B (Laravel polymorphic)?

> [!IMPORTANT]
> **Q2**: Apakah tabel `penetapans`, `pelaksanaans`, `evaluasis` perlu ditambahkan metadata? (status, submitted_at, catatan) — atau tetap sebagai anchor phase saja?

> [!IMPORTANT]
> **Q3**: Apakah `jurusan` perlu dinormalisasi ke tabel terpisah, atau cukup diperbaiki validasinya di level aplikasi?

> [!WARNING]
> **Q4**: Apakah ada data produksi yang sudah ada di database? Jika ya, perbaikan FK constraint dan tipe data harus dilakukan dengan migrasi non-destructive (bukan `migrate:fresh`).

---

## Rencana Verifikasi

### Automated
```bash
php artisan migrate --pretend   # Cek SQL yang akan dijalankan
php artisan test                # Jalankan unit test
./vendor/bin/pint --test        # Cek code style
```

### Manual
- Tes login/logout dan pastikan token dihapus dengan benar
- Tes import Excel dan validasi data masuk ke tabel dengan constraint baru
- Tes navigasi frontend — pastikan tidak ada API call berulang
- Cek Telescope dashboard untuk monitoring query database
