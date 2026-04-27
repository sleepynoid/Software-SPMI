<?php

namespace Database\Seeders;

use App\Models\KategoriStandar;
use Illuminate\Database\Seeder;

class KategoriStandarSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Standar Pendidikan', 'is_default' => true],
            ['nama_kategori' => 'Standar Penelitian', 'is_default' => true],
            ['nama_kategori' => 'Standar Pengabdian Masyarakat', 'is_default' => true],
        ];

        foreach ($categories as $cat) {
            KategoriStandar::create($cat);
        }
    }
}
