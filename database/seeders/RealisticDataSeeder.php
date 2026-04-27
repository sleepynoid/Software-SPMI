<?php

namespace Database\Seeders;

use App\Models\CapaianPelaksanaan;
use App\Models\IndikatorMutu;
use App\Models\KategoriStandar;
use App\Models\KertasKerjaAudit;
use App\Models\PeriodeAMI;
use App\Models\RisalahRtm;
use App\Models\Role;
use App\Models\StandarDikti;
use App\Models\TargetUnit;
use App\Models\TindakLanjutPtk;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Role & Unit Dasar
        $roles = Role::all()->pluck('id', 'nama_role');
        $units = UnitKerja::all()->pluck('id', 'nama_unit');

        // 2. Tambah Banyak User (Auditor & Auditee)
        $usersData = [
            [
                'nama_lengkap' => 'Prof. Ahmad Auditor, Ph.D.',
                'email' => 'ahmad@spmi.ac.id',
                'role' => 'Auditor',
                'unit' => 'Lembaga Penjaminan Mutu (LPM)',
            ],
            [
                'nama_lengkap' => 'Dr. Siti Auditor, M.Si.',
                'email' => 'siti@spmi.ac.id',
                'role' => 'Auditor',
                'unit' => 'Lembaga Penjaminan Mutu (LPM)',
            ],
            [
                'nama_lengkap' => 'Hendra Kaprodi, M.M.',
                'email' => 'hendra@spmi.ac.id',
                'role' => 'Auditee',
                'unit' => 'Prodi Manajemen',
            ],
            [
                'nama_lengkap' => 'Dewi Dekan, M.T.',
                'email' => 'dewi@spmi.ac.id',
                'role' => 'Auditee',
                'unit' => 'Fakultas Teknik',
            ],
        ];

        $createdUsers = [];
        foreach ($usersData as $ud) {
            $createdUsers[$ud['email']] = User::create([
                'nama_lengkap' => $ud['nama_lengkap'],
                'email' => $ud['email'],
                'password' => Hash::make('password'),
                'role_id' => $roles[$ud['role']],
                'unit_kerja_id' => $units[$ud['unit']] ?? null,
                'jenis_user' => 'Dosen',
            ]);
        }

        // 3. Setup Periode (Aktif & Selesai)
        $periodeSelesai = PeriodeAMI::create([
            'tahun_akademik' => '2023/2024 Genap',
            'tgl_mulai_audit' => '2025-10-01',
            'tgl_selesai_audit' => '2025-10-30',
            'status' => 'Selesai',
        ]);

        $periodeAktif = PeriodeAMI::create([
            'tahun_akademik' => '2024/2025 Ganjil',
            'tgl_mulai_audit' => '2026-05-01',
            'tgl_selesai_audit' => '2026-05-30',
            'status' => 'Audit Lapangan',
        ]);

        // 4. Kategori & Standar
        $katPendidikan = KategoriStandar::where('nama_kategori', 'Standar Pendidikan')->first();
        $katPenelitian = KategoriStandar::where('nama_kategori', 'Standar Penelitian')->first();

        // --- SKENARIO 1: PENDIDIKAN (KTS MINOR) ---
        $stdPnd = StandarDikti::create([
            'periode_id' => $periodeAktif->id,
            'kategori_id' => $katPendidikan->id,
            'nama_standar' => 'Standar Proses Pembelajaran',
        ]);

        $indRps = IndikatorMutu::create([
            'standar_id' => $stdPnd->id,
            'kode_indikator' => 'PND-02',
            'isi_standar' => '100% Mata kuliah memiliki RPS yang diperbarui dalam 2 tahun terakhir.',
            'jenis' => 'IKU',
        ]);

        $targetRpsIF = TargetUnit::create([
            'indikator_id' => $indRps->id,
            'unit_kerja_id' => $units['Prodi Informatika'],
            'nilai_target' => 100,
            'satuan' => '%',
        ]);

        $capaianRps = CapaianPelaksanaan::create([
            'target_unit_id' => $targetRpsIF->id,
            'nilai_aktual' => 85,
            'evaluasi_diri' => 'Beberapa dosen senior belum mengupdate RPS ke format OBE terbaru.',
            'link_dokumen_bukti' => 'https://spmi.ac.id/if/rps-2024',
            'submitted_by' => User::where('email', 'auditee@spmi.ac.id')->first()->id,
            'submitted_at' => now(),
        ]);

        $kkaRps = KertasKerjaAudit::create([
            'capaian_id' => $capaianRps->id,
            'auditor_id' => $createdUsers['ahmad@spmi.ac.id']->id,
            'kategori_temuan' => 'KTS Minor',
            'deskripsi_temuan' => 'Ditemukan 15% RPS masih menggunakan format lama dan belum mencantumkan CPL prodi secara eksplisit.',
        ]);

        TindakLanjutPtk::create([
            'kka_id' => $kkaRps->id,
            'akar_masalah' => 'Kurangnya sosialisasi teknis pengisian template RPS OBE bagi dosen.',
            'rencana_tindak_lanjut' => 'Workshop penyusunan RPS OBE dan pendampingan peer-group dosen.',
            'jadwal_penyelesaian' => '2026-07-15',
            'status_verifikasi' => 'Open',
        ]);

        // --- SKENARIO 2: PENELITIAN (MELAMPAUI) ---
        $stdLit = StandarDikti::create([
            'periode_id' => $periodeAktif->id,
            'kategori_id' => $katPenelitian->id,
            'nama_standar' => 'Standar Hasil Penelitian',
        ]);

        $indPub = IndikatorMutu::create([
            'standar_id' => $stdLit->id,
            'kode_indikator' => 'LIT-01',
            'isi_standar' => 'Rata-rata publikasi internasional bereputasi (Scopus/WoS) minimal 0.5 per dosen per tahun.',
            'jenis' => 'IKU',
        ]);

        $targetPubIF = TargetUnit::create([
            'indikator_id' => $indPub->id,
            'unit_kerja_id' => $units['Prodi Informatika'],
            'nilai_target' => 0.5,
            'satuan' => 'Dokumen/Dosen',
        ]);

        $capaianPub = CapaianPelaksanaan::create([
            'target_unit_id' => $targetPubIF->id,
            'nilai_aktual' => 1.2,
            'evaluasi_diri' => 'Adanya hibah kompetitif internal mendorong dosen mempublikasikan hasil riset ke jurnal Q1/Q2.',
            'link_dokumen_bukti' => 'https://sinta.kemdikbud.go.id/authors/detail/IF',
            'submitted_by' => User::where('email', 'auditee@spmi.ac.id')->first()->id,
            'submitted_at' => now(),
        ]);

        KertasKerjaAudit::create([
            'capaian_id' => $capaianPub->id,
            'auditor_id' => $createdUsers['siti@spmi.ac.id']->id,
            'kategori_temuan' => 'Melampaui',
            'deskripsi_temuan' => 'Pencapaian sangat baik. Prodi Informatika berhasil melampaui target nasional dan institusi.',
        ]);

        // --- SKENARIO 4: SARPRAS (OBSERVASI) ---
        $stdSarpras = StandarDikti::create([
            'periode_id' => $periodeAktif->id,
            'kategori_id' => $katPendidikan->id, // Bisa buat kategori Sarpras jika perlu
            'nama_standar' => 'Standar Sarana Prasarana',
        ]);

        $indLab = IndikatorMutu::create([
            'standar_id' => $stdSarpras->id,
            'kode_indikator' => 'SAR-01',
            'isi_standar' => 'Ketersediaan lisensi software original untuk 100% unit komputer laboratorium.',
            'jenis' => 'IKU',
        ]);

        $targetLabFT = TargetUnit::create([
            'indikator_id' => $indLab->id,
            'unit_kerja_id' => $units['Fakultas Teknik'],
            'nilai_target' => 100,
            'satuan' => '%',
        ]);

        $capaianLab = CapaianPelaksanaan::create([
            'target_unit_id' => $targetLabFT->id,
            'nilai_aktual' => 95,
            'evaluasi_diri' => 'Sedang dalam proses perpanjangan lisensi untuk 5 unit komputer baru.',
            'link_dokumen_bukti' => 'https://drive.google.com/ft/lisensi-soft',
            'submitted_by' => $createdUsers['dewi@spmi.ac.id']->id,
            'submitted_at' => now(),
        ]);

        KertasKerjaAudit::create([
            'capaian_id' => $capaianLab->id,
            'auditor_id' => $createdUsers['siti@spmi.ac.id']->id,
            'kategori_temuan' => 'Observasi (OB)',
            'deskripsi_temuan' => 'Hanya tersisa 5 unit yang belum terlisensi. Sebaiknya dipercepat agar tidak menghambat praktikum.',
        ]);

        // 8. Risalah RTM (Peningkatan)
        RisalahRtm::create([
            'periode_id' => $periodeAktif->id,
            'unit_kerja_id' => $units['Fakultas Teknik'],
            'tgl_rtm' => '2026-06-10',
            'pimpinan_rapat' => 'Rektor Prof. Ahmad',
            'isi_risalah' => 'Pembahasan temuan lisensi software. Universitas akan mengalokasikan dana darurat untuk lisensi kolektif.',
            'keputusan_peningkatan' => 'Pengadaan lisensi software akan ditarik ke level Universitas agar lebih efisien biaya.',
        ]);

        RisalahRtm::create([
            'periode_id' => $periodeAktif->id,
            'unit_kerja_id' => $units['Prodi Informatika'],
            'tgl_rtm' => '2026-06-11',
            'pimpinan_rapat' => 'Warek I Dr. Syaiful',
            'isi_risalah' => 'Apresiasi capaian publikasi internasional IF. Prodi IF diminta menjadi mentor bagi prodi lain.',
            'keputusan_peningkatan' => 'Insentif publikasi Q1 ditingkatkan 20% untuk memotivasi prodi lain.',
        ]);
    }
}
