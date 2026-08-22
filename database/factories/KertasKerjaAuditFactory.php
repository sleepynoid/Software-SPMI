<?php

namespace Database\Factories;

use App\Models\CapaianPelaksanaan;
use App\Models\KertasKerjaAudit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KertasKerjaAuditFactory extends Factory
{
    protected $model = KertasKerjaAudit::class;

    public function definition(): array
    {
        return [
            'capaian_id' => CapaianPelaksanaan::factory(),
            'auditor_id' => User::factory(),
            'kategori_temuan' => fake()->randomElement(['Sesuai', 'Melampaui', 'Observasi (OB)', 'KTS Minor', 'KTS Mayor']),
            'deskripsi_temuan' => fake()->optional(0.8)->paragraph(),
        ];
    }
}
