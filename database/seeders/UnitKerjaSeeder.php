<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['nama_unit' => 'Fakultas Teknik', 'jenis_unit' => 'Fakultas'],
            ['nama_unit' => 'Fakultas Ekonomi', 'jenis_unit' => 'Fakultas'],
            ['nama_unit' => 'Prodi Informatika', 'jenis_unit' => 'Program Studi'],
            ['nama_unit' => 'Prodi Manajemen', 'jenis_unit' => 'Program Studi'],
            ['nama_unit' => 'Lembaga Penjaminan Mutu (LPM)', 'jenis_unit' => 'Lembaga'],
            ['nama_unit' => 'Biro Administrasi Akademik', 'jenis_unit' => 'Biro'],
        ];

        foreach ($units as $unit) {
            UnitKerja::create($unit);
        }
    }
}
