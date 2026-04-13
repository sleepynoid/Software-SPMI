<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SheetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_jurusan' => Jurusan::factory(),
            'periode'    => $this->faker->randomElement(['2023/2024', '2024/2025', '2025/2026']),
            'note'       => $this->faker->sentence(),
            'tipe_sheet' => $this->faker->randomElement(['pendidikan', 'pengabdian', 'penelitian']),
        ];
    }
}
