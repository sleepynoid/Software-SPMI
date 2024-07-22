<?php

namespace Database\Factories;

use App\Models\Standar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Indikator>
 */
class IndikatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_standar' => Standar::factory(),
            'note' => $this->faker->sentence,
        ];
    }
}
