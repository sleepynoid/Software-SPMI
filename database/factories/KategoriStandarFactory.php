<?php

namespace Database\Factories;

use App\Models\KategoriStandar;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriStandarFactory extends Factory
{
    protected $model = KategoriStandar::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => fake()->unique()->words(3, true),
            'is_default' => fake()->boolean(30),
        ];
    }
}
