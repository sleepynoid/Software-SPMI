<?php

namespace Database\Factories;

use App\Models\IndikatorMutu;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TargetUnit>
 */
class TargetUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'indikator_id'  => IndikatorMutu::factory(),
            'unit_kerja_id' => UnitKerja::inRandomOrder()->first()?->id ?? UnitKerja::factory(),
            'nilai_target'  => fake()->randomFloat(2, 60, 100),
            'satuan'        => fake()->randomElement(['%', 'Dokumen', 'Mahasiswa', 'Dosen']),
        ];
    }
}
