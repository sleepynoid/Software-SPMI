<?php

namespace Database\Factories;

use App\Models\CapaianPelaksanaan;
use App\Models\TargetUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CapaianPelaksanaan>
 */
class CapaianPelaksanaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'target_unit_id'      => TargetUnit::factory(),
            'nilai_aktual'        => fake()->randomFloat(2, 50, 100),
            'evaluasi_diri'       => fake()->paragraph(),
            'link_dokumen_bukti'  => fake()->url(),
            'submitted_by'        => User::inRandomOrder()->first()?->id ?? User::factory(),
            'submitted_at'        => now(),
        ];
    }
}
