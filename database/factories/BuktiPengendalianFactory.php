<?php

namespace Database\Factories;

use App\Models\BuktiEvaluasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuktiPengendalianFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_bukti_evaluasi' => BuktiEvaluasi::factory(),
            'temuan'            => $this->faker->sentence(),
            'akar_masalah'      => $this->faker->sentence(),
            'rtl'               => $this->faker->sentence(),
            'pelaksanaan_rtl'   => $this->faker->sentence(),
            'edited_by'         => $this->faker->name(),
        ];
    }
}
