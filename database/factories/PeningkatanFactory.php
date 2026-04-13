<?php

namespace Database\Factories;

use App\Models\BuktiPengendalian;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeningkatanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_pengendalian' => BuktiPengendalian::factory(),
            'komentar'        => $this->faker->paragraph(),
            'edited_by'       => $this->faker->name(),
        ];
    }
}
