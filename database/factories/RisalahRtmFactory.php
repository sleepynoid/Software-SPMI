<?php

namespace Database\Factories;

use App\Models\PeriodeAMI;
use App\Models\RisalahRtm;
use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RisalahRtm>
 */
class RisalahRtmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periode_id'           => PeriodeAMI::factory(),
            'unit_kerja_id'        => UnitKerja::inRandomOrder()->first()?->id ?? UnitKerja::factory(),
            'tgl_rtm'              => fake()->date(),
            'pimpinan_rapat'       => fake()->name(),
            'isi_risalah'          => fake()->paragraphs(3, true),
            'keputusan_peningkatan' => fake()->paragraph(),
        ];
    }
}
