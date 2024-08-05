<?php

namespace App\Imports;

use App\Models\{BuktiPelaksanaan, Evaluasi, link, Pelaksanaan, Penetapan, Sheet, Standar, Indikator, Target};
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow, WithCustomCsvSettings, SkipsEmptyRows};
use Illuminate\Support\Collection;

class PenetapanImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, WithCustomCsvSettings
{
    private $sheet;
    public function __construct($sheet)
    {
        $this->sheet = $sheet;
    }
    
    public function collection(Collection $rows)
    {
        $sheet = Sheet::firstOrCreate([
            'jurusan' => $this->sheet['jurusan'],
            'periode' => $this->sheet['periode'],
            'note' => $this->sheet['note'],
            'tipe_sheet' => $this->sheet['tipe_sheet']
        ]);
        
        $penetapan = Penetapan::create(['id_sheet' => $sheet->id]);
        $pelaksanaan = Pelaksanaan::create(['id_sheet' => $sheet->id]);
        $evaluasi = Evaluasi::create(['id_sheet' => $sheet->id]);
        $currentType = 'input';
        $currentStandar = null;
        $lastStandarNote = null;

        foreach ($rows as $row) {
            if (empty($row['standar']) && $lastStandarNote !== null) {
                $row['standar'] = $lastStandarNote;
            } else {
                $lastStandarNote = $row['standar'];
            }

            if (in_array(strtolower($row['standar']), ['input', 'proses', 'output'])) {
                $currentType = strtolower($row['standar']);
                continue;
            }

            if (!$currentStandar || $currentStandar->note != $row['standar']) {
                $currentStandar = Standar::firstOrCreate([
                    'id_penetapan' => $penetapan->id,
                    'note' => $row['standar'],
                    'tipe' => $currentType
                ]);
            }

            if (!empty($row['indikator'])) {
                $indikator = Indikator::firstOrCreate([
                    'id_standar' => $currentStandar->id,
                    'note' => $row['indikator']
                ]);

                $buktiPelaksanaan = BuktiPelaksanaan::create([
                    'komentar' => '',
                    'id_indikator' => $indikator->id,
                    'id_pelaksanaan' => $pelaksanaan->id
                ]);

                link::create([
                    'judul_link' => '',
                    'link' => '',
                    'id_bukti_pelaksanaan' => $buktiPelaksanaan->id
                ]);

                if (!empty($row['target'])) {
                    Target::create([
                        'id_indikator' => $indikator->id,
                        'value' => $row['target']
                    ]);
                }
            }
        }
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
    }
}
