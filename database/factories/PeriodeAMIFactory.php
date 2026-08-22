<?php

namespace Database\Factories;

use App\Models\PeriodeAMI;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodeAMIFactory extends Factory
{
    protected $model = PeriodeAMI::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 year', '+1 year');
        $end = (clone $start)->modify('+'.fake()->numberBetween(14, 60).' days');

        return [
            'tahun_akademik' => fake()->year().'/'.(fake()->year() + 1).' '.fake()->randomElement(['Ganjil', 'Genap']),
            'tgl_mulai_audit' => $start->format('Y-m-d'),
            'tgl_selesai_audit' => $end->format('Y-m-d'),
            'status' => fake()->randomElement(['Draft', 'Pelaksanaan EDOM', 'Audit Lapangan', 'RTM', 'Selesai']),
        ];
    }
}
