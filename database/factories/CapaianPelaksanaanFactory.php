<?php

namespace Database\Factories;

use App\Models\CapaianPelaksanaan;
use App\Models\TargetUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CapaianPelaksanaanFactory extends Factory
{
    protected $model = CapaianPelaksanaan::class;

    public function definition(): array
    {
        return [
            'target_unit_id' => TargetUnit::factory(),
            'nilai_aktual' => fake()->optional(0.8)->randomFloat(1, 0, 150),
            'evaluasi_diri' => fake()->optional(0.7)->paragraph(),
            'link_dokumen_bukti' => fake()->optional(0.5)->url(),
            'submitted_by' => null,
            'submitted_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
