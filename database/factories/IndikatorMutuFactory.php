<?php

namespace Database\Factories;

use App\Models\IndikatorMutu;
use App\Models\StandarDikti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IndikatorMutu>
 */
class IndikatorMutuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'standar_id'      => StandarDikti::factory(),
            'kode_indikator'  => 'IND-' . fake()->unique()->numerify('####'),
            'isi_standar'     => fake()->paragraph(),
            'jenis'           => fake()->randomElement(['IKU', 'IKT']),
        ];
    }
}
