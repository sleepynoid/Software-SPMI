<?php

namespace Database\Factories;

use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitKerjaFactory extends Factory
{
    protected $model = UnitKerja::class;

    public function definition(): array
    {
        return [
            'nama_unit' => fake()->unique()->words(3, true),
            'jenis_unit' => fake()->randomElement(['Fakultas', 'Program Studi', 'Biro', 'Lembaga']),
            'kepala_unit_id' => null,
        ];
    }
}
