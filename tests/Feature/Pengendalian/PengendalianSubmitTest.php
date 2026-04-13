<?php

namespace Tests\Feature\Pengendalian;

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

class PengendalianSubmitTest extends TestCase
{
    use RefreshDatabase;

    private function createPengendalianFixture(): array
    {
        $jurusan     = Jurusan::factory()->create();
        $sheet       = Sheet::factory()->create(['id_jurusan' => $jurusan->id]);
        $penetapan   = Penetapan::factory()->create(['id_sheet' => $sheet->id]);
        $standar     = Standar::factory()->create(['id_penetapan' => $penetapan->id]);
        $indikator   = Indikator::factory()->create(['id_standar' => $standar->id]);
        $pelaksanaan = Pelaksanaan::factory()->create(['id_sheet' => $sheet->id]);
        $evaluasi    = Evaluasi::factory()->create(['id_sheet' => $sheet->id]);
        $buktiPel    = BuktiPelaksanaan::factory()->create([
            'id_indikator'   => $indikator->id,
            'id_pelaksanaan' => $pelaksanaan->id,
        ]);
        $buktiEval   = BuktiEvaluasi::factory()->create([
            'id_bukti_pelaksanaan' => $buktiPel->id,
            'id_evaluasi'          => $evaluasi->id,
        ]);

        return compact('buktiEval');
    }

    public function test_guest_cannot_submit_pengendalian(): void
    {
        $this->post('/submitPengendalian', [])->assertRedirect('/login');
    }

    public function test_user_can_submit_pengendalian(): void
    {
        ['buktiEval' => $buktiEval] = $this->createPengendalianFixture();
        $user = User::factory()->create(['role' => 'Pengendalian']);

        $response = $this->actingAs($user)->post('/submitPengendalian', [
            'data' => [
                'idBuktiEvaluasi' => $buktiEval->id,
                'temuan'          => 'Ada temuan tidak sesuai target.',
                'akarMasalah'     => 'Kurangnya monitoring.',
                'rtl'             => 'Lakukan evaluasi mingguan.',
                'pelaksanaanRtl'  => 'Evaluasi setiap senin.',
                'userName'        => $user->name,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_submit_pengendalian_creates_record_in_database(): void
    {
        ['buktiEval' => $buktiEval] = $this->createPengendalianFixture();
        $user = User::factory()->create(['role' => 'Pengendalian']);

        $this->actingAs($user)->post('/submitPengendalian', [
            'data' => [
                'idBuktiEvaluasi' => $buktiEval->id,
                'temuan'          => 'Temuan penting.',
                'akarMasalah'     => 'Akar masalah.',
                'rtl'             => 'RTL konkret.',
                'pelaksanaanRtl'  => 'Jadwal pelaksanaan.',
                'userName'        => $user->name,
            ],
        ]);

        $this->assertDatabaseHas('bukti_pengendalians', [
            'id_bukti_evaluasi' => $buktiEval->id,
            'temuan'            => 'Temuan penting.',
        ]);
    }

    public function test_submit_pengendalian_fails_without_temuan(): void
    {
        ['buktiEval' => $buktiEval] = $this->createPengendalianFixture();
        $user = User::factory()->create(['role' => 'Pengendalian']);

        $response = $this->actingAs($user)->post('/submitPengendalian', [
            'data' => [
                'idBuktiEvaluasi' => $buktiEval->id,
                'userName'        => $user->name,
            ],
        ]);

        $response->assertSessionHasErrors('data.temuan');
    }
}
