# Sistem Penjaminan Mutu Internal (SPMI) - Perguruan Tinggi

Repositori ini berisi rancangan arsitektur database relasional, alur kerja (workflow), dan struktur antarmuka (UI/Routing) untuk aplikasi Sistem Penjaminan Mutu Internal (SPMI) khusus institusi Pendidikan Tinggi (Universitas, Institut, Politeknik, Akademi).

Sistem ini dirancang sangat dinamis agar mematuhi standar nasional (SN-Dikti) sekaligus mengakomodasi standar kekhasan Perguruan Tinggi Swasta (PTS) maupun Negeri (PTN), menggunakan siklus **PPEPP** (Penetapan - Pelaksanaan - Evaluasi - Pengendalian - Peningkatan).

---

## 🗄️ Struktur Database (Entity Relationship)

Database dibagi menjadi beberapa modul agar terstruktur, *scalable*, dan mudah menghasilkan laporan untuk keperluan Akreditasi (BAN-PT/LAM).

### 1. Modul Autentikasi & Organisasi
Modul ini menangani *Role-Based Access Control* (RBAC) dan hierarki unit kerja di kampus.

**Tabel `roles`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | INT (PK, AI) | |
| `nama_role` | VARCHAR | `Admin/LPM`, `Pimpinan`, `Auditor`, `Auditee` |

**Tabel `unit_kerja`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | INT (PK, AI) | |
| `nama_unit` | VARCHAR | Contoh: Prodi Informatika, Fakultas Teknik, LPPM |
| `jenis_unit` | ENUM | `Fakultas`, `Program Studi`, `Biro`, `Lembaga` |
| `kepala_unit_id` | INT (FK) | Relasi ke `users.id` (Dekan/Kaprodi/Ketua) |

**Tabel `users`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | INT (PK, AI) | |
| `nama_lengkap` | VARCHAR | |
| `nidn` | VARCHAR | Nomor Induk Dosen Nasional (*Nullable* untuk Tendik) |
| `jenis_user` | ENUM | `Dosen`, `Tenaga Kependidikan` |
| `role_id` | INT (FK) | Relasi ke `roles.id` |
| `unit_kerja_id`| INT (FK) | Relasi ke `unit_kerja.id` |

### 2. Modul Siklus Audit (Jangkar)
Wadah untuk 1 siklus Audit Mutu Internal (AMI).

**Tabel `periode_ami`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `tahun_akademik` | VARCHAR | Contoh: Ganjil 2024/2025 |
| `tgl_mulai_audit`| DATE | |
| `tgl_selesai_audit`| DATE| |
| `status` | ENUM | `Draft`, `Pelaksanaan EDOM`, `Audit Lapangan`, `RTM`, `Selesai` |

### 3. Modul PPEPP (Pipeline Alur Mutu)

#### 📌 Tahap 1: Penetapan (Planning)
**Tabel `kategori_standar` (Master Data)**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | INT (PK) | |
| `nama_kategori` | VARCHAR | Contoh: Pendidikan, Penelitian, Pengabdian |
| `is_default` | BOOLEAN | `True` jika standar wajib nasional, `False` jika khas kampus |

**Tabel `standar_dikti`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `periode_id` | BIGINT (FK)| Relasi ke `periode_ami.id` |
| `kategori_id` | INT (FK) | Relasi ke `kategori_standar.id` |
| `nama_standar` | VARCHAR | Contoh: Standar Kompetensi Lulusan |

**Tabel `indikator_mutu`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `standar_id` | BIGINT (FK)| Relasi ke `standar_dikti.id` |
| `kode_indikator` | VARCHAR | Contoh: STD-PND-01 |
| `isi_standar` | TEXT | Deskripsi detail indikator yang harus dicapai |
| `jenis` | ENUM | `IKU` (Utama) atau `IKT` (Tambahan) |

**Tabel `target_unit`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `indikator_id` | BIGINT (FK)| Relasi ke `indikator_mutu.id` |
| `unit_kerja_id` | INT (FK) | Target per prodi/unit |
| `nilai_target` | FLOAT | Angka pencapaian |
| `satuan` | VARCHAR | Contoh: `%`, `SKS`, `Dokumen` |

#### 🚀 Tahap 2: Pelaksanaan (Doing / Evaluasi Diri)
**Tabel `capaian_pelaksanaan`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `target_unit_id` | BIGINT (FK)| Relasi ke `target_unit.id` |
| `nilai_aktual` | FLOAT | Realisasi capaian prodi |
| `evaluasi_diri` | TEXT | Penilaian mandiri oleh Auditee |
| `link_dokumen_bukti`| VARCHAR | URL Google Drive / Berkas lampiran |

#### 🔍 Tahap 3: Evaluasi (Checking / AMI)
**Tabel `kertas_kerja_audit` (KKA)**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `capaian_id` | BIGINT (FK)| Relasi ke `capaian_pelaksanaan.id` |
| `auditor_id` | INT (FK) | Relasi ke `users.id` |
| `kategori_temuan`| ENUM | `Sesuai`, `Melampaui`, `Observasi (OB)`, `KTS Minor`, `KTS Mayor` |
| `deskripsi_temuan`| TEXT | Catatan dari auditor untuk prodi |

#### 🛠️ Tahap 4: Pengendalian (Acting / PTK & RTL)
**Tabel `tindak_lanjut_ptk`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `kka_id` | BIGINT (FK)| Relasi ke `kertas_kerja_audit.id` |
| `akar_masalah` | TEXT | Diisi oleh Auditee |
| `rencana_tindak_lanjut`| TEXT | (RTL) |
| `jadwal_penyelesaian`| DATE | Batas waktu perbaikan |
| `status_verifikasi`| ENUM | `Open`, `Menunggu Verifikasi`, `Closed` |

#### 📈 Tahap 5: Peningkatan (Acting / RTM)
**Tabel `risalah_rtm`**
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | |
| `periode_id` | BIGINT (FK)| Relasi ke `periode_ami.id` |
| `unit_kerja_id` | INT (FK) | Relasi ke `unit_kerja.id` |
| `tanggal_rapat` | DATE | |
| `hasil_pembahasan`| TEXT | Keputusan rapat |
| `rekomendasi_standar`| TEXT | Perumusan target/standar mutu baru |

---

## 🔄 Alur Kerja Sistem (Workflow AMI)

1. **Persiapan (Penetapan):** Admin/LPM menginisiasi siklus AMI dan menetapkan Indikator beserta Target ke masing-masing Prodi.
2. **Evaluasi Diri (Pelaksanaan):** Auditee (Prodi) menginput nilai capaian aktual, link bukti dokumen, dan Evaluasi Diri.
3. **Audit Mutu Internal (Evaluasi):** Auditor memverifikasi capaian Prodi dan menerbitkan status temuan (Sesuai/OB/KTS) di Kertas Kerja Audit (KKA).
4. **Pengendalian:** Jika ada KTS/OB, Auditee wajib mengisi Form Tindak Lanjut (Akar Masalah & RTL). Auditor memverifikasi perbaikan.
5. **Rapat Tinjauan Manajemen (Peningkatan):** Pimpinan meninjau hasil evaluasi dan merumuskan standar baru untuk siklus berikutnya.

---

## 🖥️ Struktur Halaman (User Interface & Routing)

Aplikasi menggunakan *Role-Based Access Control* (RBAC) pada level *routing*. Menu navigasi akan menyesuaikan dengan *role* pengguna yang sedang *login*.

### 🌐 Publik & General
* `/login` : Autentikasi pengguna.
* `/profile` : Informasi akun, ganti kata sandi.

### 🏠 Dashboard (Berbasis Role)
* `/dashboard` : 
  * **Admin/LPM**: Ringkasan status periode AMI aktif.
  * **Auditee**: *Progress bar* capaian target dan *alert deadline* RTL.
  * **Auditor**: Daftar antrean KKA yang perlu diaudit.
  * **Pimpinan**: Grafik eksekutif capaian IKU/IKT dan persentase temuan.

### ⚙️ Modul Master Data (Akses: Admin/LPM)
* `/master/users` : CRUD data pengguna, penetapan *role* dan unit kerja.
* `/master/unit-kerja` : CRUD daftar Fakultas, Prodi, Biro, dan Lembaga.
* `/master/kategori-standar` : CRUD kategori standar (Pendidikan, Penelitian, dll).

### 📝 Modul Penetapan (Akses: Admin/LPM)
* `/penetapan/periode` : Manajemen pembukaan dan penutupan siklus AMI.
* `/penetapan/standar` : Pembuatan Standar Dikti.
* `/penetapan/indikator` : Pembuatan Indikator Mutu (IKU/IKT).
* `/penetapan/distribusi-target` : Matriks penetapan angka target untuk masing-masing prodi/unit.

### 🏃‍♂️ Modul Pelaksanaan (Akses: Auditee)
* `/pelaksanaan/evaluasi-diri` : Daftar target indikator milik prodi terkait.
* `/pelaksanaan/input-capaian/{id}` : Form pelaporan *nilai aktual*, catatan evaluasi diri, dan lampiran URL bukti dokumen.

### 🔍 Modul Evaluasi / AMI (Akses: Auditor)
* `/evaluasi/jadwal-audit` : Daftar prodi yang menjadi tanggung jawab auditor.
* `/evaluasi/kka/{id}` : Antarmuka *side-by-side* untuk membandingkan target vs aktual, serta *form* penetapan temuan (Sesuai/KTS/OB).

### 🛠️ Modul Pengendalian / RTL (Akses: Auditee & Auditor)
* `/pengendalian/daftar-temuan` : Daftar indikator yang terkena status KTS atau OB.
* `/pengendalian/isi-rtl/{id}` (Auditee) : Form pengisian Akar Masalah dan Rencana Tindak Lanjut (RTL).
* `/pengendalian/verifikasi/{id}` (Auditor) : Form verifikasi bukti perbaikan untuk mengubah status menjadi *Closed*.

### 📈 Modul Peningkatan / RTM (Akses: Pimpinan & Admin)
* `/peningkatan/rtm` : Daftar agenda Rapat Tinjauan Manajemen.
* `/peningkatan/risalah/{id}` : Form input hasil rapat dan rekomendasi target/standar baru.

### 🖨️ Modul Laporan & Dokumen Ekspor (Akses: Pimpinan, LPM, Admin)
* `/laporan/rekap-ami` : Tabel dinamis untuk memfilter rekapitulasi data AMI.
* `/laporan/cetak-kka` : *Export* Kertas Kerja Audit ke PDF (Format Borang).
* `/laporan/cetak-lkps` : *Export* kompilasi data ke Excel untuk kebutuhan portal akreditasi (SAPTO BAN-PT / LAM).