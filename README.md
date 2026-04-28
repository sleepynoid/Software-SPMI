# Software-SPMI ITATS

> Sistem Penjaminan Mutu Internal (SPMI) berbasis siklus **PPEPP** (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan) — Monolith Web App berbasis Laravel 11 + Inertia.js + Vue 3.

---

## 🏛️ Deskripsi Proyek

Software-SPMI adalah platform Audit Mutu Internal (AMI) terintegrasi yang dirancang khusus untuk **Institut Teknologi Adhi Tama Surabaya (ITATS)**. Aplikasi ini mendigitalisasi seluruh siklus penjaminan mutu perguruan tinggi secara relasional dan sistematis.

### Fitur Utama:
- **Penetapan**: Manajemen Standar Dikti, Indikator Mutu, dan distribusi Target Unit.
- **Pelaksanaan**: Pengisian Evaluasi Diri (EDOM) oleh Auditee dengan bukti dokumen.
- **Evaluasi**: Kertas Kerja Audit (KKA) untuk Auditor (Kategori temuan: Sesuai, OB, KTS).
- **Pengendalian**: Penyusunan Rencana Tindak Lanjut (RTL/PTK) oleh Auditee.
- **Peningkatan**: Dokumentasi Risalah Rapat Tinjauan Manajemen (RTM) oleh Pimpinan.

---

## 💻 Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 11, PHP 8.4 |
| **Frontend** | Vue 3 (Composition API), Inertia.js v2 |
| **Library UI** | PrimeVue 4, Tailwind CSS 4 |
| **Package Manager** | **pnpm** |
| **Build Tool** | Vite 5 |
| **Database** | MySQL / SQLite |

---

## 👥 Roles & Akses Control (RBAC)

| Role | Deskripsi |
|---|---|
| `Admin / LPM` | Manajemen master data (Periode, Unit, User), Standar, Indikator, dan Target. |
| `Auditee` | Mengisi Evaluasi Diri (Pelaksanaan) dan Rencana Tindak Lanjut (Pengendalian). |
| `Auditor` | Melakukan audit lapangan dan mengisi Kertas Kerja Audit (Evaluasi). |
| `Pimpinan` | Melakukan review capaian mutu dan memimpin Rapat Tinjauan Manajemen (Peningkatan). |

---

## 🔑 Akun Demo (Seeded)

Gunakan akun berikut untuk menguji fungsionalitas sistem (Password: `password`):

| Role | Email | Deskripsi |
|---|---|---|
| **Admin/LPM** | `admin@spmi.ac.id` | Akses penuh manajemen standar & unit. |
| **Pimpinan** | `rektor@spmi.ac.id` | Akses Dashboard & Risalah RTM. |
| **Auditor** | `auditor@spmi.ac.id` | Akses Kertas Kerja Audit (KKA). |
| **Auditee** | `auditee@spmi.ac.id` | Akses Evaluasi Diri & RTL. |

---

## 🛠️ Persyaratan

- **PHP 8.4+**
- **Composer**
- **pnpm** (dianjurkan) atau npm/yarn
- **MySQL 8+**

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
# Sesuaikan DB_DATABASE di .env
php artisan migrate --seed
```

### 3. Menjalankan Aplikasi
```bash
# Terminal 1: Backend
php artisan serve

# Terminal 2: Frontend
pnpm dev
```

---

## 🧪 Data Simulasi & Testing

Aplikasi dilengkapi dengan Factory untuk men-generate data demo dalam jumlah besar guna pengujian fungsionalitas penuh:

```bash
# Generate 10 siklus audit lengkap secara otomatis via Tinker
php artisan tinker --execute="App\Models\RisalahRtm::factory()->count(10)->create()"
```

Instruksi di atas akan secara otomatis membuat data relasional mulai dari Periode, Standar, Indikator, Capaian, hingga Risalah RTM.

---

## 📂 Struktur Folder Phase-Based (PPEPP)

```
app/Http/Controllers/
├── Penetapan/       # Manajemen Standar & Target
├── Pelaksanaan/    # Evaluasi Diri Auditee
├── Evaluasi/       # Audit KKA oleh Auditor
├── Pengendalian/   # RTL / PTK
└── Peningkatan/    # Risalah RTM Pimpinan
```

---

## 🛡️ Catatan Keamanan

- **Zero-Token Auth**: Menggunakan Cookie Session Laravel murni tanpa penyimpanan token di LocalStorage (lebih aman terhadap XSS).
- **Supply Chain Security**: Proteksi terhadap versi dependensi yang terkompromasi (Axios patch).
- **Validation**: Strict validation di setiap fase PPEPP untuk menjaga integritas data mutu.

---
© 2026 ITATS Documentation.
