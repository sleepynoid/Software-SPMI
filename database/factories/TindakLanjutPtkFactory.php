<?php

namespace Database\Factories;

use App\Models\KertasKerjaAudit;
use App\Models\TindakLanjutPtk;
use Illuminate\Database\Eloquent\Factories\Factory;

class TindakLanjutPtkFactory extends Factory
{
    protected $model = TindakLanjutPtk::class;

    public function definition(): array
    {
        return [
            'kka_id' => KertasKerjaAudit::factory(),
            'akar_masalah' => fake()->optional(0.7)->paragraph(),
            'rencana_tindak_lanjut' => fake()->optional(0.7)->paragraph(),
            'jadwal_penyelesaian' => fake()->optional(0.6)->dateTimeBetween('now', '+3 months'),
            'status_verifikasi' => fake()->randomElement(['Open', 'Menunggu Verifikasi', 'Closed']),
        ];
    }
}
