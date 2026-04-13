<?php

namespace Database\Factories;

use App\Models\BuktiPelaksanaan;
use App\Models\Evaluasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuktiEvaluasiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_bukti_pelaksanaan' => BuktiPelaksanaan::factory(),
            'id_evaluasi'          => Evaluasi::factory(),
            'komentar'             => $this->faker->paragraph(),
            'adjustment'           => $this->faker->randomElement(['melampai', 'mencapai', 'belum mencapai', 'menyimpan']),
            'edited_by'            => $this->faker->name(),
        ];
    }
}
