<?php

namespace Database\Factories;

use App\Models\PeriodeAMI;
use App\Models\RisalahRtm;
use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

class RisalahRtmFactory extends Factory
{
    protected $model = RisalahRtm::class;

    public function definition(): array
    {
        return [
            'periode_id' => PeriodeAMI::factory(),
            'unit_kerja_id' => UnitKerja::factory(),
            'tgl_rtm' => fake()->dateTimeBetween('-1 year', 'now'),
            'pimpinan_rapat' => fake()->name(),
            'isi_risalah' => fake()->paragraphs(3, true),
            'keputusan_peningkatan' => fake()->paragraphs(2, true),
        ];
    }
}
