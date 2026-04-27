<?php

namespace Database\Factories;

use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StandarDikti>
 */
class StandarDiktiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periode_id'   => PeriodeAMI::factory(),
            'kategori_id'  => KategoriStandar::inRandomOrder()->first()?->id ?? KategoriStandar::factory(),
            'nama_standar' => fake()->sentence(4),
        ];
    }
}
