<?php

namespace Database\Seeders;

use App\Models\Penetapan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenetapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Penetapan::create([
            'id_sheet' => '1'
        ]);
    }
}
