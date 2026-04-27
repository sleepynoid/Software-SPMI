<?php

namespace Database\Factories;

use App\Models\PeriodeAMI;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriodeAMI>
 */
class PeriodeAMIFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 year', 'now');
        $end = (clone $start)->modify('+1 month');

        return [
            'tahun_akademik'     => $start->format('Y') . '/' . ($start->format('Y') + 1),
            'tgl_mulai_audit'    => $start->format('Y-m-d'),
            'tgl_selesai_audit'  => $end->format('Y-m-d'),
            'status'             => fake()->randomElement(['Draft', 'Pelaksanaan EDOM', 'Audit Lapangan', 'RTM', 'Selesai']),
        ];
    }
}
