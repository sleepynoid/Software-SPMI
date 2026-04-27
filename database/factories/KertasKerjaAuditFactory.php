<?php

namespace Database\Factories;

use App\Models\CapaianPelaksanaan;
use App\Models\KertasKerjaAudit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KertasKerjaAudit>
 */
class KertasKerjaAuditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'capaian_id'      => CapaianPelaksanaan::factory(),
            'auditor_id'      => User::whereHas('role', function($q) { $q->where('nama_role', 'Auditor'); })->first()?->id ?? User::factory(),
            'kategori_temuan' => fake()->randomElement(['Sesuai', 'Melampaui', 'Observasi (OB)', 'KTS Minor', 'KTS Mayor']),
            'deskripsi_temuan'=> fake()->paragraph(),
        ];
    }
}
