<?php

namespace App\Exports;

use App\Models\StandarDikti;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StandarExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(public int $periodeId) {}

    public function collection(): Collection
    {
        return StandarDikti::with('kategori', 'indikatorMutus.targetUnits.unitKerja')
            ->where('periode_id', $this->periodeId)
            ->get();
    }

    public function headings(): array
    {
        return ['Kategori', 'Nama Standar', 'Kode Indikator', 'Isi Indikator', 'Jenis', 'Target', 'Satuan', 'Unit Kerja'];
    }

    public function map($standar): array
    {
        $rows = [];

        foreach ($standar->indikatorMutus as $indikator) {
            if ($indikator->targetUnits->isEmpty()) {
                $rows[] = [
                    $standar->kategori->nama_kategori ?? '',
                    $standar->nama_standar,
                    $indikator->kode_indikator,
                    $indikator->isi_standar,
                    $indikator->jenis,
                    '',
                    '',
                    '',
                ];

                continue;
            }

            foreach ($indikator->targetUnits as $target) {
                $rows[] = [
                    $standar->kategori->nama_kategori ?? '',
                    $standar->nama_standar,
                    $indikator->kode_indikator,
                    $indikator->isi_standar,
                    $indikator->jenis,
                    $target->nilai_target,
                    $target->satuan,
                    $target->unitKerja->nama_unit ?? '',
                ];
            }
        }

        return $rows;
    }
}
