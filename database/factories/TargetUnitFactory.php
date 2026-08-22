<?php

namespace Database\Factories;

use App\Models\IndikatorMutu;
use App\Models\TargetUnit;
use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetUnitFactory extends Factory
{
    protected $model = TargetUnit::class;

    public function definition(): array
    {
        return [
            'indikator_id' => IndikatorMutu::factory(),
            'unit_kerja_id' => UnitKerja::factory(),
            'nilai_target' => fake()->randomFloat(1, 0, 100),
            'satuan' => fake()->randomElement(['%', 'Dokumen/Dosen', 'Orang', 'Jam']),
        ];
    }
}
