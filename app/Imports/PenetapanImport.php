<?php

namespace App\Imports;

use App\Models\Penetapan;
use App\Models\Standar;
use App\Models\Indikator;
use App\Models\Target;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenetapanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $penetapan = Penetapan::create(['id_sheet' => 1]);

        $currentStandar = null;
        $lastStandarNote = null; // Variabel untuk menyimpan nilai 'STANDAR' terakhir yang tidak kosong

        foreach ($rows as $row) {
            // Cek apakah kolom 'STANDAR' kosong dan gunakan nilai terakhir jika ya
            if (empty($row['standar']) && $lastStandarNote !== null) {
                $row['standar'] = $lastStandarNote;
            } else {
                $lastStandarNote = $row['standar']; // Perbarui nilai terakhir jika 'standar' tidak kosong
            }

            if (!$currentStandar || $currentStandar->note != $row['standar']) {
                $currentStandar = Standar::firstOrCreate([
                    'id_penetapan' => $penetapan->id,
                    'note' => $row['standar'],
                    'tipe' => 'input'
                ]);
            }

            $indikator = Indikator::firstOrCreate([
                'id_standar' => $currentStandar->id,
                'note' => $row['indikator']
            ]);

            Target::create([
                'id_indikator' => $indikator->id,
                'value' => $row['target']
            ]);
        }
    }
}