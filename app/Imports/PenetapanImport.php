<?php

namespace App\Imports;

use App\Models\Penetapan;
use App\Models\Standar;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class PenetapanImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    public function collection(Collection $rows)
    {
        Log::info('isi data pertama: '. $rows[0]);
        $penetapan = Penetapan::create(['id_sheet' => 1]);

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

                if (!empty($row['target'])) {
                    Target::create([
                        'id_indikator' => $indikator->id,
                        'value' => $row['target']
                    ]);
                } else {
                    Log::warning("Kolom 'target' kosong untuk indikator: " . $row['indikator']);
                }
            } else {
                Log::warning("Kolom 'indikator' kosong untuk standar: " . $row['standar']);
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

//    public function rules(): array
//    {
//        return [
//            '*.standar' => 'string|max:255',
//            '*.indikator' => 'string|max:255',
//            '*.target' => 'numeric'
//        ];
//    }
//
//    public function customValidationMessages()
//    {
//        return [
//            '*.standar.string' => 'Kolom standar harus berupa teks.',
//            '*.standar.max' => 'Kolom standar tidak boleh lebih dari 255 karakter.',
//            '*.indikator.string' => 'Kolom indikator harus berupa teks.',
//            '*.indikator.max' => 'Kolom indikator tidak boleh lebih dari 255 karakter.',
//            '*.target.numeric' => 'Kolom target harus berupa angka.'
//        ];
//    }
}
