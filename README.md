# Software-SPMI ITATS

> Sistem Penjaminan Mutu Internal (SPMI) berbasis siklus **PPEPP** (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan) — Monolith Web App berbasis Laravel 11 + Inertia.js + Vue 3.

---

## 🏛️ Deskripsi Proyek

Software-SPMI adalah platform Audit Mutu Internal (AMI) terintegrasi yang dirancang khusus untuk **Institut Teknologi Adhi Tama Surabaya (ITATS)**. Aplikasi ini mendigitalisasi seluruh siklus penjaminan mutu perguruan tinggi secara relasional dan sistematis, menggantikan proses manual berbasis kertas/spreadsheet yang tidak terpusat.

### ✨ Fitur Utama:
- **📊 Penetapan**: Manajemen Standar Dikti & Indikator Mutu. Dilengkapi fitur **Import Massal via Excel** dengan pemetaan otomatis ke seluruh Unit Kerja.
- **🚀 Pelaksanaan**: Pengisian Evaluasi Diri (EDOM) oleh Auditee dengan pelampiran bukti dokumen (URL/Cloud Storage).
- **🔍 Evaluasi**: Kertas Kerja Audit (KKA) untuk Auditor dengan sistem **Traffic Light** untuk kategori temuan (Sesuai, OB, KTS Minor/Mayor).
- **🛠️ Pengendalian**: Penyusunan Akar Masalah dan Rencana Tindak Lanjut (RTL/PTK) oleh Auditee.
- **📈 Peningkatan**: Dokumentasi Risalah Rapat Tinjauan Manajemen (RTM) oleh Pimpinan untuk perumusan standar siklus berikutnya.

---

## 💻 Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 11, PHP 8.4 |
| **Frontend** | Vue 3 (Composition API), Inertia.js v2 |
| **Library UI** | PrimeVue 4, Tailwind CSS 4 |
| **Aesthetics** | Modern Glassmorphism, Dynamic Animations |
| **Package Manager** | **pnpm** |
| **Build Tool** | Vite 5 |

---

## 👥 Roles & Akses Control (RBAC)

| Role | Deskripsi |
|---|---|
| `Admin / LPM` | Manajemen master data, Standar, Indikator, dan Target Unit. |
| `Auditee` | Melaporkan capaian kinerja unit dan menyusun rencana perbaikan (RTL). |
| `Auditor` | Memverifikasi capaian dan memberikan temuan audit di KKA. |
| `Pimpinan` | Memantau dashboard eksekutif dan memimpin RTM. |

---

## 🔑 Akun Demo (Seeded)

Gunakan akun berikut untuk menguji fungsionalitas sistem (Password: **`password`**):

| Role | Email | Nama |
|---|---|---|
| **Admin/LPM** | `admin@spmi.ac.id` | Administrator LPM |
| **Pimpinan** | `pimpinan@spmi.ac.id` | Rektor ITATS |
| **Auditor** | `ahmad@spmi.ac.id` | Prof. Ahmad Auditor, Ph.D. |
| **Auditee** | `auditee@spmi.ac.id` | Kaprodi Informatika |
| **Auditee** | `hendra@spmi.ac.id` | Kaprodi Manajemen |

---

## 🚀 Langkah Setup

### 1. Clone & Install
```bash
git clone https://github.com/Burzess/Software-SPMI.git
cd Software-SPMI
composer install
pnpm install
```

### 2. Lingkungan & Database
```bash
cp .env.example .env
php artisan key:generate
# Sesuaikan konfigurasi database di .env
php artisan migrate:fresh --seed
```

### 3. Menjalankan Aplikasi
```bash
# Jalankan Backend
php artisan serve

# Jalankan Frontend (Terminal terpisah)
pnpm dev
```

---

## 📂 Struktur Modul PPEPP

```
app/Http/Controllers/
├── Penetapan/       # Import Excel, Standar & Target Unit
├── Pelaksanaan/    # Evaluasi Diri (EDOM)
├── Evaluasi/       # Kertas Kerja Audit (KKA)
├── Pengendalian/   # Rencana Tindak Lanjut (RTL)
└── Peningkatan/    # Risalah RTM
```

---

## 🛡️ Keunggulan Arsitektur

- **Relational Integrity**: Data standar hingga temuan audit terhubung secara ketat untuk meminimalisir anomali data.
- **Dynamic UX**: Penggunaan PrimeVue 4 dan Tailwind 4 memberikan pengalaman antarmuka yang sangat responsif dan premium.
- **Scalable Seeders**: Dilengkapi dengan `RealisticDataSeeder` untuk simulasi siklus audit yang komprehensif dalam sekali perintah.

---
© 2026 ITATS Documentation.
