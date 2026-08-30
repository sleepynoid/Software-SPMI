<?php

use App\Models\IndikatorMutu;
use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\Role;
use App\Models\StandarDikti;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

test('guests are redirected to the login page', function () {
    $periode = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Pelaksanaan EDOM',
    ]);

    $response = $this->get(route('penetapan.standar.export', ['periode_id' => $periode->id]));
    $response->assertRedirect();
});

test('admin can export standar', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $periode = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Pelaksanaan EDOM',
    ]);

    $kategori = KategoriStandar::create(['nama_kategori' => 'Akademik']);
    $unit = UnitKerja::create(['nama_unit' => 'Fakultas Teknik', 'jenis_unit' => 'Fakultas']);
    $standar = StandarDikti::create([
        'periode_id' => $periode->id,
        'kategori_id' => $kategori->id,
        'nama_standar' => 'Standar Pendidikan',
    ]);
    $indikator = IndikatorMutu::create([
        'standar_id' => $standar->id,
        'kode_indikator' => 'IKU-01',
        'isi_standar' => 'Persentase lulusan tepat waktu',
        'jenis' => 'IKU',
    ]);
    TargetUnit::create([
        'indikator_id' => $indikator->id,
        'unit_kerja_id' => $unit->id,
        'nilai_target' => 90,
        'satuan' => '%',
    ]);

    Excel::fake();

    $response = $this->get(route('penetapan.standar.export', ['periode_id' => $periode->id]));
    $response->assertOk();
});

test('export filters by periode', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $periode1 = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Pelaksanaan EDOM',
    ]);
    $periode2 = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Draft',
    ]);

    $kategori = KategoriStandar::create(['nama_kategori' => 'Akademik']);
    $unit = UnitKerja::create(['nama_unit' => 'Fakultas Teknik', 'jenis_unit' => 'Fakultas']);

    $standar1 = StandarDikti::create([
        'periode_id' => $periode1->id,
        'kategori_id' => $kategori->id,
        'nama_standar' => 'Standar Pendidikan',
    ]);
    $standar2 = StandarDikti::create([
        'periode_id' => $periode2->id,
        'kategori_id' => $kategori->id,
        'nama_standar' => 'Standar Penelitian',
    ]);

    $indikator1 = IndikatorMutu::create([
        'standar_id' => $standar1->id,
        'kode_indikator' => 'IKU-01',
        'isi_standar' => 'Persentase lulusan tepat waktu',
        'jenis' => 'IKU',
    ]);
    $indikator2 = IndikatorMutu::create([
        'standar_id' => $standar2->id,
        'kode_indikator' => 'IKU-01',
        'isi_standar' => 'Persentase publikasi',
        'jenis' => 'IKU',
    ]);

    TargetUnit::create([
        'indikator_id' => $indikator1->id,
        'unit_kerja_id' => $unit->id,
        'nilai_target' => 90,
        'satuan' => '%',
    ]);
    TargetUnit::create([
        'indikator_id' => $indikator2->id,
        'unit_kerja_id' => $unit->id,
        'nilai_target' => 50,
        'satuan' => '%',
    ]);

    Excel::fake();

    $response = $this->get(route('penetapan.standar.export', ['periode_id' => $periode1->id]));
    $response->assertOk();

    Excel::assertDownloaded('export_standar_dikti.xlsx');
});
