<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JurusanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode'      => strtoupper($this->faker->unique()->lexify('??')),
            'nama'      => $this->faker->words(3, true),
            'jenjang'   => $this->faker->randomElement(['S1', 'D3', 'S2']),
            'is_active' => true,
        ];
    }
}
