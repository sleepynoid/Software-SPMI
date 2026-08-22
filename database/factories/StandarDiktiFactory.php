<?php

namespace Database\Factories;

use App\Models\KategoriStandar;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use Illuminate\Database\Eloquent\Factories\Factory;

class StandarDiktiFactory extends Factory
{
    protected $model = StandarDikti::class;

    public function definition(): array
    {
        return [
            'periode_id' => PeriodeAMI::factory(),
            'kategori_id' => KategoriStandar::factory(),
            'nama_standar' => fake()->unique()->words(4, true),
        ];
    }
}
