<?php

namespace Database\Factories;

use App\Models\Penetapan;
use Illuminate\Database\Eloquent\Factories\Factory;

class StandarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_penetapan' => Penetapan::factory(),
            'note'         => $this->faker->sentence(),
            'tipe'         => $this->faker->randomElement(['input', 'proses', 'output']),
        ];
    }
}
