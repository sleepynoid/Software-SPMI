<?php

namespace Database\Factories;

use App\Models\Sheet;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluasiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_sheet' => Sheet::factory(),
        ];
    }
}
