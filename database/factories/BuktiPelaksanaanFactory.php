<?php

namespace Database\Factories;

use App\Models\Indikator;
use App\Models\Pelaksanaan;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuktiPelaksanaanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_indikator'   => Indikator::factory(),
            'id_pelaksanaan' => Pelaksanaan::factory(),
            'komentar'       => $this->faker->paragraph(),
            'edited_by'      => $this->faker->name(),
        ];
    }
}
