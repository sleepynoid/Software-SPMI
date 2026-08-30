<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StandarTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function collection(): Collection
    {
        return collect([
            ['Akademik', 'Standar Pendidikan', 'IKU-01', 'Persentase lulusan tepat waktu', 'IKU', 90, '%', 'Fakultas'],
            ['Akademik', 'Standar Pendidikan', 'IKT-01', 'Jumlah mahasiswa aktif', 'IKT', 500, 'Mahasiswa', 'Program Studi'],
        ]);
    }

    public function headings(): array
    {
        return ['Kategori', 'Nama Standar', 'Kode Indikator', 'Isi Indikator', 'Jenis', 'Target', 'Satuan', 'Unit Kerja'];
    }
}
