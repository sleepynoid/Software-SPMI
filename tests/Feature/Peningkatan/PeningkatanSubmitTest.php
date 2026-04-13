<?php

namespace Tests\Feature\Peningkatan;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\BuktiPengendalian;
use App\Models\Evaluasi;
use App\Models\Indikator;
use App\Models\Jurusan;
use App\Models\Pelaksanaan;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeningkatanSubmitTest extends TestCase
{
    use RefreshDatabase;

    private function createPeningkatanFixture(): array
    {
        $jurusan        = Jurusan::factory()->create();
        $sheet          = Sheet::factory()->create(['id_jurusan' => $jurusan->id]);
        $penetapan      = Penetapan::factory()->create(['id_sheet' => $sheet->id]);
        $standar        = Standar::factory()->create(['id_penetapan' => $penetapan->id]);
        $indikator      = Indikator::factory()->create(['id_standar' => $standar->id]);
        $pelaksanaan    = Pelaksanaan::factory()->create(['id_sheet' => $sheet->id]);
        $evaluasi       = Evaluasi::factory()->create(['id_sheet' => $sheet->id]);
        $buktiPel       = BuktiPelaksanaan::factory()->create([
            'id_indikator'   => $indikator->id,
            'id_pelaksanaan' => $pelaksanaan->id,
        ]);
        $buktiEval      = BuktiEvaluasi::factory()->create([
            'id_bukti_pelaksanaan' => $buktiPel->id,
            'id_evaluasi'          => $evaluasi->id,
        ]);
        $buktiPengendali = BuktiPengendalian::factory()->create([
            'id_bukti_evaluasi' => $buktiEval->id,
        ]);

        return compact('buktiPengendali');
    }

    public function test_guest_cannot_submit_peningkatan(): void
    {
        $this->post('/submitPeningkatan', [])->assertRedirect('/login');
    }

    public function test_user_can_submit_peningkatan(): void
    {
        ['buktiPengendali' => $buktiPengendali] = $this->createPeningkatanFixture();
        $user = User::factory()->create(['role' => 'Peningkatan']);

        $response = $this->actingAs($user)->post('/submitPeningkatan', [
            'data' => [
                'idBuktiPengendalian' => $buktiPengendali->id,
                'komenPeningkatan'    => 'Peningkatan berkelanjutan telah dilakukan.',
                'userName'            => $user->name,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_submit_peningkatan_creates_record_in_database(): void
    {
        ['buktiPengendali' => $buktiPengendali] = $this->createPeningkatanFixture();
        $user = User::factory()->create(['role' => 'Peningkatan']);

        $this->actingAs($user)->post('/submitPeningkatan', [
            'data' => [
                'idBuktiPengendalian' => $buktiPengendali->id,
                'komenPeningkatan'    => 'Sudah ada peningkatan nyata.',
                'userName'            => $user->name,
            ],
        ]);

        $this->assertDatabaseHas('peningkatans', [
            'id_pengendalian' => $buktiPengendali->id,
            'komentar'        => 'Sudah ada peningkatan nyata.',
        ]);
    }

    public function test_submit_peningkatan_updates_existing_record(): void
    {
        ['buktiPengendali' => $buktiPengendali] = $this->createPeningkatanFixture();
        $user = User::factory()->create(['role' => 'Peningkatan']);

        // Submit pertama
        $this->actingAs($user)->post('/submitPeningkatan', [
            'data' => [
                'idBuktiPengendalian' => $buktiPengendali->id,
                'komenPeningkatan'    => 'Awal.',
                'userName'            => $user->name,
            ],
        ]);

        // Submit kedua — seharusnya update, bukan insert duplikat
        $this->actingAs($user)->post('/submitPeningkatan', [
            'data' => [
                'idBuktiPengendalian' => $buktiPengendali->id,
                'komenPeningkatan'    => 'Diperbarui.',
                'userName'            => $user->name,
            ],
        ]);

        $this->assertDatabaseCount('peningkatans', 1);
        $this->assertDatabaseHas('peningkatans', [
            'id_pengendalian' => $buktiPengendali->id,
            'komentar'        => 'Diperbarui.',
        ]);
    }

    public function test_submit_peningkatan_fails_without_komentar(): void
    {
        ['buktiPengendali' => $buktiPengendali] = $this->createPeningkatanFixture();
        $user = User::factory()->create(['role' => 'Peningkatan']);

        $response = $this->actingAs($user)->post('/submitPeningkatan', [
            'data' => [
                'idBuktiPengendalian' => $buktiPengendali->id,
                'userName'            => $user->name,
            ],
        ]);

        $response->assertSessionHasErrors('data.komenPeningkatan');
    }
}
