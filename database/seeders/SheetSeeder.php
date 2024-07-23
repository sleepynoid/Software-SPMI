<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sheet;

class SheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sheet::create([
            'jurusan' => 'Teknik Informatika',
            'periode' => '2023-06-06',
            'note' => 'Catatan untuk periode 2023-2024',
            'tipe_sheet' => 'pendidikan'
        ]);

        Sheet::create([
            'jurusan' => 'Teknik Eletro',
            'periode' => '2023-06-06',
            'note' => 'Catatan untuk periode 2023-2024',
            'tipe_sheet' => 'pendidikan'
        ]);

        Sheet::create([
            'jurusan' => 'Teknik Lingkungan',
            'periode' => '2023-06-06',
            'note' => 'Catatan untuk periode 2023-2024',
            'tipe_sheet' => 'pendidikan'
        ]);
    }
}
