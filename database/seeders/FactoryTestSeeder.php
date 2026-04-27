<?php

namespace Database\Seeders;

use App\Models\CapaianPelaksanaan;
use App\Models\IndikatorMutu;
use App\Models\PeriodeAMI;
use App\Models\StandarDikti;
use App\Models\TargetUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class FactoryTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Users
        User::factory()->count(10)->create();

        // 2. Create Periode AMI
        PeriodeAMI::factory()->count(2)->create()->each(function ($periode) {
            // 3. Create Standar Dikti for each Periode
            StandarDikti::factory()->count(3)->create(['periode_id' => $periode->id])->each(function ($standar) {
                // 4. Create Indikator Mutu for each Standar
                IndikatorMutu::factory()->count(2)->create(['standar_id' => $standar->id])->each(function ($indikator) {
                    // 5. Create Target Unit for each Indikator
                    TargetUnit::factory()->count(2)->create(['indikator_id' => $indikator->id])->each(function ($target) {
                        // 6. Create Capaian Pelaksanaan for each Target
                        $capaian = CapaianPelaksanaan::factory()->create(['target_unit_id' => $target->id]);

                        // 7. Create Kertas Kerja Audit (KKA)
                        $kka = \App\Models\KertasKerjaAudit::factory()->create(['capaian_id' => $capaian->id]);

                        // 8. Create Tindak Lanjut PTK
                        \App\Models\TindakLanjutPtk::factory()->create(['kka_id' => $kka->id]);
                    });
                });
            });

            // 9. Create Risalah RTM for each Periode
            \App\Models\RisalahRtm::factory()->count(2)->create(['periode_id' => $periode->id]);
        });
    }
}
