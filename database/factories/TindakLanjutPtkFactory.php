<?php

namespace Database\Factories;

use App\Models\KertasKerjaAudit;
use App\Models\TindakLanjutPtk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TindakLanjutPtk>
 */
class TindakLanjutPtkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kka_id'                => KertasKerjaAudit::factory(),
            'akar_masalah'          => fake()->paragraph(),
            'rencana_tindak_lanjut' => fake()->paragraph(),
            'jadwal_penyelesaian'   => fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status_verifikasi'     => fake()->randomElement(['Open', 'Menunggu Verifikasi', 'Closed']),
        ];
    }
}
