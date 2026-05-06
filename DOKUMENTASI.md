# Dokumentasi Sistem Penjaminan Mutu Internal (SPMI)

Sistem ini dirancang untuk memfasilitasi siklus **PPEPP** secara digital dan terintegrasi.

## 🚀 Fitur Utama & Alur Kerja (PPEPP)

### 1. P - Penetapan (Standard Setting)
*   **Manajemen Periode AMI**: Admin membuat siklus audit tahunan.
*   **Import Standar Excel**: Mempercepat input ratusan indikator standar mutu.
*   **Target Unit Mapping**: Mendistribusikan indikator ke prodi/unit kerja yang relevan.

### 2. P - Pelaksanaan (Implementation)
*   **Evaluasi Diri (EDOM)**: Unit kerja melaporkan capaian aktual secara mandiri.
*   **Bukti Digital**: Auditee melampirkan link dokumen bukti (Cloud Storage/Drive) untuk setiap indikator.

### 3. E - Evaluasi (Internal Quality Audit)
*   **Audit Lapangan**: Auditor menilai kesesuaian antara laporan EDOM dengan fakta di lapangan.
*   **Traffic Light System**: Visualisasi status temuan (Melampaui, Sesuai, Observasi, KTS Minor, KTS Mayor).

### 4. P - Pengendalian (Control)
*   **Rencana Tindak Lanjut (RTL/PTK)**: Auditee menyusun langkah perbaikan untuk temuan ketidaksesuaian.
*   **Verifikasi Auditor**: Auditor memverifikasi dan menutup (Close) temuan jika perbaikan sudah tuntas.

### 5. P - Peningkatan (Improvement)
*   **Risalah RTM**: Dokumentasi Rapat Tinjauan Manajemen untuk memutuskan standar baru yang lebih tinggi di periode berikutnya.

---

## 📊 Struktur Database

### `roles`
Menyimpan peran pengguna dalam sistem.
- `id`: Primary Key
- `nama_role`: Nama peran (Admin/LPM, Auditee, Auditor, Pimpinan)
- `slug`: Identitas unik (admin, auditee, auditor, pimpinan)

### `unit_kerja`
Daftar unit kerja atau program studi.
- `id`: Primary Key
- `nama_unit`: Nama Prodi/Unit
- `jenis`: Jenis unit (Prodi/Non-Prodi)

### `users`
Data pengguna sistem.
- `id`: Primary Key
- `role_id`: FK ke `roles`
- `unit_kerja_id`: FK ke `unit_kerja`
- `name`: Nama lengkap
- `email`: Email (Username)
- `password`: Password terenkripsi

---

## 2. Siklus PENETAPAN (Standar Mutu)

### `periode_ami`
Siklus tahun akademik audit.
- `id`: Primary Key
- `tahun_akademik`: Contoh "2024/2025 Ganjil"
- `tgl_mulai_audit`: Tanggal mulai
- `tgl_selesai_audit`: Tanggal selesai
- `status`: Status siklus (Draft, Pelaksanaan EDOM, Audit Lapangan, RTM, Selesai)

### `kategori_standar`
Pengelompokan standar (Pendidikan, Penelitian, Pengabdian, dll).
- `id`: Primary Key
- `nama_kategori`: Nama kelompok standar

### `standar_dikti`
Dokumen standar mutu.
- `id`: Primary Key
- `periode_id`: FK ke `periode_ami`
- `kategori_id`: FK ke `kategori_standar`
- `nama_standar`: Judul standar (misal: Standar Isi Pembelajaran)

### `indikator_mutu`
Butir-butir indikator dari sebuah standar.
- `id`: Primary Key
- `standar_id`: FK ke `standar_dikti`
- `kode_indikator`: Kode unik (misal: PND-01)
- `isi_standar`: Deskripsi detail indikator
- `jenis`: IKU (Indikator Kinerja Utama) atau IKT (Indikator Kinerja Tambahan)

### `target_unit`
Distribusi indikator ke unit kerja tertentu beserta targetnya.
- `id`: Primary Key
- `indikator_id`: FK ke `indikator_mutu`
- `unit_kerja_id`: FK ke `unit_kerja`
- `nilai_target`: Angka target yang harus dicapai
- `satuan`: Satuan nilai (%, IPK, Menit, dll)

---

## 3. Siklus PELAKSANAAN (Evaluasi Diri)

### `capaian_pelaksanaan`
Laporan realisasi dari unit kerja (Auditee).
- `id`: Primary Key
- `target_unit_id`: FK ke `target_unit`
- `nilai_aktual`: Hasil realisasi di lapangan
- `evaluasi_diri`: Narasi analisis capaian
- `link_dokumen_bukti`: URL dokumen pendukung
- `submitted_by`: FK ke `users` (siapa yang melapor)
- `submitted_at`: Waktu pelaporan

---

## 4. Siklus EVALUASI (Audit Mutu Internal)

### `kertas_kerja_audit` (KKA)
Hasil penilaian oleh Auditor.
- `id`: Primary Key
- `capaian_id`: FK ke `capaian_pelaksanaan`
- `kategori_temuan`: (Sesuai, Melampaui, Observasi, KTS Minor, KTS Mayor)
- `deskripsi_temuan`: Uraian hasil audit
- `rekomendasi`: Saran perbaikan dari auditor
- `auditor_id`: FK ke `users` (siapa yang mengaudit)

---

## 5. Siklus PENGENDALIAN (RTL / PTK)

### `tindak_lanjut_ptk`
Rencana Tindak Lanjut dari Auditee atas temuan audit.
- `id`: Primary Key
- `kka_id`: FK ke `kertas_kerja_audit`
- `akar_masalah`: Analisis mengapa terjadi temuan
- `rencana_tindak_lanjut`: Langkah nyata perbaikan (RTL)
- `jadwal_penyelesaian`: Deadline perbaikan
- `status_verifikasi`: Status (Open, Menunggu Verifikasi, Closed)

---

## 6. Siklus PENINGKATAN (RTM)

### `risalah_rtm`
Dokumentasi Rapat Tinjauan Manajemen.
- `id`: Primary Key
- `periode_id`: FK ke `periode_ami`
- `unit_kerja_id`: FK ke `unit_kerja`
- `tgl_rtm`: Waktu rapat dilaksanakan
- `pimpinan_rapat`: Nama pemimpin rapat (misal: Rektor)
- `isi_risalah`: Ringkasan diskusi rapat
- `keputusan_peningkatan`: Detail langkah peningkatan standar (Siklus P)

---

## 7. Daftar File Migrasi & Urutan Build

Berikut adalah pemetaan antara file migrasi dan tabel yang dihasilkan:

| Urutan | File Migrasi | Tabel Utama | Fungsi |
| :--- | :--- | :--- | :--- |
| 1 | `0001_01_01_000000_create_users_table.php` | `users` | Autentikasi & Profil |
| 2 | `2026_04_24_000001_create_roles_table.php` | `roles` | Hak Akses |
| 3 | `2026_04_24_000002_create_unit_kerja_table.php` | `unit_kerja` | Daftar Prodi/Unit |
| 4 | `2026_04_24_000003_create_periode_ami_table.php` | `periode_ami` | Siklus Waktu Audit |
| 5 | `2026_04_24_000004_create_kategori_standar_table.php` | `kategori_standar` | Kelompok Standar |
| 6 | `2026_04_24_000005_create_standar_dikti_table.php` | `standar_dikti` | Dokumen Standar |
| 7 | `2026_04_24_000006_create_indikator_mutu_table.php` | `indikator_mutu` | Butir Indikator |
| 8 | `2026_04_24_000007_create_target_unit_table.php` | `target_unit` | Distribusi & Target |
| 9 | `2026_04_24_000008_create_capaian_pelaksanaan_table.php` | `capaian_pelaksanaan` | Laporan Realisasi |
| 10 | `2026_04_24_000009_create_kertas_kerja_audit_table.php` | `kertas_kerja_audit` | Penilaian Auditor |
| 11 | `2026_04_24_000010_create_tindak_lanjut_ptk_table.php` | `tindak_lanjut_ptk` | Rencana Perbaikan |
| 12 | `2026_04_24_000011_create_risalah_rtm_table.php` | `risalah_rtm` | Peningkatan Mutu |

> **Catatan**: Urutan migrasi sangat krusial dikarenakan adanya relasi *Foreign Key*. Pastikan tidak mengubah prefix tanggal pada nama file migrasi agar tidak terjadi error saat `php artisan migrate`.
