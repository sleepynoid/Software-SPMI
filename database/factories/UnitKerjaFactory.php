<?php

namespace Database\Factories;

use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitKerja>
 */
class UnitKerjaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_unit'      => fake()->company(),
            'jenis_unit'     => fake()->randomElement(['Fakultas', 'Program Studi', 'Biro', 'Lembaga']),
            'kepala_unit_id' => null,
        ];
    }
}
