<?php

namespace Tests\Feature\Evaluasi;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
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

class EvaluasiSubmitTest extends TestCase
{
    use RefreshDatabase;

    private function createEvaluasiFixture(): array
    {
        $jurusan      = Jurusan::factory()->create();
        $sheet        = Sheet::factory()->create(['id_jurusan' => $jurusan->id]);
        $penetapan    = Penetapan::factory()->create(['id_sheet' => $sheet->id]);
        $standar      = Standar::factory()->create(['id_penetapan' => $penetapan->id]);
        $indikator    = Indikator::factory()->create(['id_standar' => $standar->id]);
        $pelaksanaan  = Pelaksanaan::factory()->create(['id_sheet' => $sheet->id]);
        $evaluasi     = Evaluasi::factory()->create(['id_sheet' => $sheet->id]);
        $buktiPel     = BuktiPelaksanaan::factory()->create([
            'id_indikator'   => $indikator->id,
            'id_pelaksanaan' => $pelaksanaan->id,
        ]);

        return compact('indikator', 'pelaksanaan', 'evaluasi', 'buktiPel');
    }

    public function test_guest_cannot_submit_evaluasi(): void
    {
        $this->post('/submitEvaluasi', [])->assertRedirect('/login');
    }

    public function test_user_can_submit_evaluasi(): void
    {
        ['indikator' => $indikator, 'evaluasi' => $evaluasi, 'buktiPel' => $buktiPel] = $this->createEvaluasiFixture();

        $user = User::factory()->create(['role' => 'Evaluasi']);

        $response = $this->actingAs($user)->post('/submitEvaluasi', [
            'data' => [
                'idBuktiPelaksanaan' => $buktiPel->id,
                'idEvaluasi'         => $evaluasi->id,
                'komentarEvaluasi'   => 'Evaluasi baik.',
                'adjusment'          => 'mencapai',
                'userName'           => $user->name,
                'idIndikator'        => $indikator->id,
                'indicator'          => null,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_submit_evaluasi_creates_record_in_database(): void
    {
        ['indikator' => $indikator, 'evaluasi' => $evaluasi, 'buktiPel' => $buktiPel] = $this->createEvaluasiFixture();

        $user = User::factory()->create(['role' => 'Evaluasi']);

        $this->actingAs($user)->post('/submitEvaluasi', [
            'data' => [
                'idBuktiPelaksanaan' => $buktiPel->id,
                'idEvaluasi'         => $evaluasi->id,
                'komentarEvaluasi'   => 'Tercapai target.',
                'adjusment'          => 'mencapai',
                'userName'           => $user->name,
                'idIndikator'        => $indikator->id,
                'indicator'          => null,
            ],
        ]);

        $this->assertDatabaseHas('bukti_evaluasis', [
            'id_bukti_pelaksanaan' => $buktiPel->id,
            'komentar'             => 'Tercapai target.',
            'adjustment'           => 'mencapai',
        ]);
    }

    public function test_submit_evaluasi_updates_indikator_note_when_indicator_given(): void
    {
        ['indikator' => $indikator, 'evaluasi' => $evaluasi, 'buktiPel' => $buktiPel] = $this->createEvaluasiFixture();

        $user = User::factory()->create(['role' => 'Evaluasi']);

        $this->actingAs($user)->post('/submitEvaluasi', [
            'data' => [
                'idBuktiPelaksanaan' => $buktiPel->id,
                'idEvaluasi'         => $evaluasi->id,
                'komentarEvaluasi'   => 'Updated.',
                'adjusment'          => 'melampai',
                'userName'           => $user->name,
                'idIndikator'        => $indikator->id,
                'indicator'          => 'Indikator baru diperbarui',
            ],
        ]);

        $this->assertDatabaseHas('indikators', [
            'id'   => $indikator->id,
            'note' => 'Indikator baru diperbarui',
        ]);
    }

    public function test_submit_evaluasi_fails_without_required_fields(): void
    {
        $user = User::factory()->create(['role' => 'Evaluasi']);

        $response = $this->actingAs($user)->post('/submitEvaluasi', [
            'data' => ['komentarEvaluasi' => 'hanya komentar'],
        ]);

        $response->assertSessionHasErrors(['data.idBuktiPelaksanaan', 'data.adjusment']);
    }
}
