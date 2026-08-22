<?php

namespace Database\Factories;

use App\Models\IndikatorMutu;
use App\Models\StandarDikti;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndikatorMutuFactory extends Factory
{
    protected $model = IndikatorMutu::class;

    public function definition(): array
    {
        return [
            'standar_id' => StandarDikti::factory(),
            'kode_indikator' => fake()->unique()->bothify('???-##'),
            'isi_standar' => fake()->sentence(10),
            'jenis' => fake()->randomElement(['IKU', 'IKT']),
        ];
    }
}
