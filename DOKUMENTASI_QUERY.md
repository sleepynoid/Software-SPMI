# Dokumentasi Lengkap Query Eloquent & SQL - Software-SPMI ITATS

Dokumentasi seluruh operasi database (CRUD) yang dilakukan dalam siklus PPEPP.

---

## 🏗️ 1. Modul Penetapan (P)
Fokus: Pengaturan standar, indikator, dan target unit.

### A. Read (Baca Data)
- **Fetch Standar & Hierarki**:
  ```php
  StandarDikti::with(['kategori', 'indikator.targetUnit.unitKerja'])->get();
  ```
  **SQL Raw:** `SELECT * FROM standar_dikti;` (diikuti eager loading query ke tabel terkait).

### B. Write/Update (Tulis & Ubah)
- **Tambah/Ubah Target Unit**:
  ```php
  TargetUnit::updateOrCreate(
      ['indikator_id' => $id, 'unit_kerja_id' => $unitId],
      ['nilai_target' => $value, 'satuan' => $satuan]
  );
  ```
  **SQL Raw:** `INSERT INTO target_unit (...) VALUES (...) ON DUPLICATE KEY UPDATE ...`

---

## 📝 2. Modul Pelaksanaan (P)
Fokus: Pengisian Evaluasi Diri (EDOM) oleh Auditee.

### A. Read (Baca Data)
- **List Indikator per Unit**:
  ```php
  CapaianPelaksanaan::with(['targetUnit.indikator'])
      ->whereHas('targetUnit', fn($q) => $q->where('unit_kerja_id', $userUnit))
      ->get();
  ```
  **SQL Raw:** `SELECT * FROM capaian_pelaksanaan WHERE EXISTS (SELECT * FROM target_unit ...)`

### B. Write (Tulis Data)
- **Input Capaian**:
  ```php
  CapaianPelaksanaan::create([
      'target_unit_id' => $tid, 'nilai_aktual' => $val, 
      'evaluasi_diri' => $text, 'submitted_by' => $uid
  ]);
  ```
  **SQL Raw:** `INSERT INTO capaian_pelaksanaan (target_unit_id, ...) VALUES (...)`

---

## 🔍 3. Modul Evaluasi (E)
Fokus: Audit lapangan oleh Auditor.

### A. Read (Baca Data)
- **Daftar Tugas Audit**:
  ```php
  KertasKerjaAudit::with(['capaian.targetUnit.unitKerja'])->where('auditor_id', $uid)->get();
  ```

### B. Write/Update (Tulis & Ubah)
- **Submit Temuan Audit**:
  ```php
  KertasKerjaAudit::updateOrCreate(
      ['capaian_id' => $cid],
      ['kategori_temuan' => $kat, 'deskripsi_temuan' => $txt, 'auditor_id' => $uid]
  );
  ```
  **SQL Raw:** `UPDATE kertas_kerja_audit SET kategori_temuan = ? WHERE capaian_id = ?`

---

## 🛠️ 4. Modul Pengendalian (P)
Fokus: Rencana Tindak Lanjut (RTL/PTK) oleh Auditee.

### A. Read (Baca Data)
- **Daftar Temuan untuk Direspon**:
  ```php
  TindakLanjutPtk::with(['kka.capaian'])->whereHas('kka.capaian.targetUnit', fn($q) => $q->where('unit_kerja_id', $uid))->get();
  ```

### B. Write (Tulis Data)
- **Input RTL**:
  ```php
  TindakLanjutPtk::create([
      'kka_id' => $kid, 'rencana_tindak_lanjut' => $rtl, 'jadwal_penyelesaian' => $date
  ]);
  ```
  **SQL Raw:** `INSERT INTO tindak_lanjut_ptk (kka_id, ...) VALUES (...)`

---

## 📊 5. Modul Peningkatan (P)
Fokus: Rapat Tinjauan Manajemen (RTM).

### A. Read (Baca Data)
- **List Risalah RTM**:
  ```php
  RisalahRtm::with(['periode', 'unitKerja'])->latest()->get();
  ```

### B. Write (Tulis Data)
- **Simpan Risalah**:
  ```php
  RisalahRtm::create([
      'periode_id' => $pid, 'tgl_rtm' => $date, 'isi_risalah' => $txt
  ]);
  ```
  **SQL Raw:** `INSERT INTO risalah_rtm (periode_id, ...) VALUES (...)`

---

## 🔒 6. Autentikasi & User (System)
- **Login Check**:
  ```php
  User::where('email', $email)->first();
  ```
  **SQL Raw:** `SELECT * FROM users WHERE email = ? LIMIT 1`

---

## ⚡ Ringkasan Operasi
- **Read**: Dominan `SELECT` dengan `JOIN` via `with()`.
- **Write**: Menggunakan `create()` (**INSERT**) dan `updateOrCreate()` (**INSERT/UPDATE**).
- **Delete**: Menggunakan `delete()` (**DELETE**), biasanya dipicu secara manual oleh Admin di menu Master Data.
