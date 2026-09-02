<?php

use App\Models\IndikatorMutu;
use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\Role;
use App\Models\StandarDikti;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('penetapan.standar.import.index'));
    $response->assertRedirect();
});

test('admin can access import page', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $response = $this->get(route('penetapan.standar.import.index'));
    $response->assertOk();
});

test('import valid xlsx creates data', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $periode = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Pelaksanaan EDOM',
    ]);

    UnitKerja::create(['nama_unit' => 'Fakultas Teknik', 'jenis_unit' => 'Fakultas']);
    UnitKerja::create(['nama_unit' => 'Fakultas Ekonomi', 'jenis_unit' => 'Fakultas']);

    $response = $this->post(route('penetapan.standar.import.store'), [
        'file' => UploadedFile::fake()->createWithContent('test.xlsx', generateValidXlsxContent()),
        'periode_id' => $periode->id,
    ]);

    $response->assertRedirect(route('penetapan.standar.index'));

    expect(StandarDikti::where('periode_id', $periode->id)->count())->toBe(1);
    expect(IndikatorMutu::count())->toBe(1);
    expect(KategoriStandar::where('nama_kategori', 'Akademik')->count())->toBe(1);
    expect(TargetUnit::count())->toBe(2);

    $standar = StandarDikti::where('periode_id', $periode->id)->first();
    expect($standar->nama_standar)->toBe('Standar Pendidikan');
    expect($standar->indikatorMutus()->first()->kode_indikator)->toBe('IKU-01');
});

test('import invalid file format returns error', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $periode = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Pelaksanaan EDOM',
    ]);

    $response = $this->postJson(route('penetapan.standar.import.store'), [
        'file' => UploadedFile::fake()->create('test.csv', 100, 'text/csv'),
        'periode_id' => $periode->id,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['file']);
});

test('import missing required column returns error', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $periode = PeriodeAMI::create([
        'tahun_akademik' => '2025/2026',
        'tgl_mulai_audit' => '2025-09-01',
        'tgl_selesai_audit' => '2026-01-31',
        'status' => 'Pelaksanaan EDOM',
    ]);

    $content = generateInvalidXlsxContent();

    $response = $this->from(route('penetapan.standar.import.index'))->post(route('penetapan.standar.import.store'), [
        'file' => UploadedFile::fake()->createWithContent('test.xlsx', $content),
        'periode_id' => $periode->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('file');
});

test('template download returns xlsx', function () {
    $role = Role::firstOrCreate(['nama_role' => 'Admin/LPM']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    Excel::fake();

    $response = $this->get(route('penetapan.standar.import.template'));
    $response->assertOk();
});

function generateValidXlsxContent(): string
{
    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Kategori');
    $sheet->setCellValue('B1', 'Nama Standar');
    $sheet->setCellValue('C1', 'Kode Indikator');
    $sheet->setCellValue('D1', 'Isi Indikator');
    $sheet->setCellValue('E1', 'Jenis');
    $sheet->setCellValue('F1', 'Target');
    $sheet->setCellValue('G1', 'Satuan');
    $sheet->setCellValue('H1', 'Unit Kerja');

    $sheet->setCellValue('A2', 'Akademik');
    $sheet->setCellValue('B2', 'Standar Pendidikan');
    $sheet->setCellValue('C2', 'IKU-01');
    $sheet->setCellValue('D2', 'Persentase lulusan tepat waktu');
    $sheet->setCellValue('E2', 'IKU');
    $sheet->setCellValue('F2', 90);
    $sheet->setCellValue('G2', '%');
    $sheet->setCellValue('H2', 'Fakultas');

    $writer = new Xlsx($spreadsheet);
    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
    $writer->save($tempFile);
    $content = file_get_contents($tempFile);
    unlink($tempFile);

    return $content;
}

function generateInvalidXlsxContent(): string
{
    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Wrong Header');
    $sheet->setCellValue('B1', 'Another Wrong Header');

    $writer = new Xlsx($spreadsheet);
    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
    $writer->save($tempFile);
    $content = file_get_contents($tempFile);
    unlink($tempFile);

    return $content;
}
