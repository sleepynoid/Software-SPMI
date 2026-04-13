<?php

namespace Database\Factories;

use App\Models\Indikator;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_indikator' => Indikator::factory(),
            'value'        => $this->faker->randomFloat(2, 50, 100),
        ];
    }
}
