<?php

namespace Tests\Feature\Pelaksanaan;

use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\Jurusan;
use App\Models\Pelaksanaan;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelaksanaanSubmitTest extends TestCase
{
    use RefreshDatabase;

    private function createIndikatorWithPelaksanaan(): array
    {
        $jurusan    = Jurusan::factory()->create();
        $sheet      = Sheet::factory()->create(['id_jurusan' => $jurusan->id]);
        $penetapan  = Penetapan::factory()->create(['id_sheet' => $sheet->id]);
        $standar    = Standar::factory()->create(['id_penetapan' => $penetapan->id, 'tipe' => 'input']);
        $indikator  = Indikator::factory()->create(['id_standar' => $standar->id]);
        $pelaksanaan = Pelaksanaan::factory()->create(['id_sheet' => $sheet->id]);

        return compact('indikator', 'pelaksanaan');
    }

    public function test_guest_cannot_submit_pelaksanaan(): void
    {
        $response = $this->post('/submitPelaksanaan', []);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_submit_pelaksanaan(): void
    {
        ['indikator' => $indikator, 'pelaksanaan' => $pelaksanaan] = $this->createIndikatorWithPelaksanaan();

        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $response = $this->actingAs($user)->post('/submitPelaksanaan', [
            'data' => [
                'idIndikator'         => $indikator->id,
                'komentarPelaksanaan' => 'Sudah dilaksanakan dengan baik.',
                'idPelaksanaan'       => $pelaksanaan->id,
                'userName'            => $user->name,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_submit_creates_bukti_pelaksanaan_in_database(): void
    {
        ['indikator' => $indikator, 'pelaksanaan' => $pelaksanaan] = $this->createIndikatorWithPelaksanaan();

        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $this->actingAs($user)->post('/submitPelaksanaan', [
            'data' => [
                'idIndikator'         => $indikator->id,
                'komentarPelaksanaan' => 'Komentar pertama.',
                'idPelaksanaan'       => $pelaksanaan->id,
                'userName'            => $user->name,
            ],
        ]);

        $this->assertDatabaseHas('bukti_pelaksanaans', [
            'id_indikator' => $indikator->id,
            'komentar'     => 'Komentar pertama.',
            'edited_by'    => $user->name,
        ]);
    }

    public function test_submit_updates_existing_bukti_pelaksanaan(): void
    {
        ['indikator' => $indikator, 'pelaksanaan' => $pelaksanaan] = $this->createIndikatorWithPelaksanaan();

        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        // Submit pertama
        $this->actingAs($user)->post('/submitPelaksanaan', [
            'data' => [
                'idIndikator'         => $indikator->id,
                'komentarPelaksanaan' => 'Komentar awal.',
                'idPelaksanaan'       => $pelaksanaan->id,
                'userName'            => $user->name,
            ],
        ]);

        // Submit kedua (update)
        $this->actingAs($user)->post('/submitPelaksanaan', [
            'data' => [
                'idIndikator'         => $indikator->id,
                'komentarPelaksanaan' => 'Komentar diperbarui.',
                'idPelaksanaan'       => $pelaksanaan->id,
                'userName'            => $user->name,
            ],
        ]);

        $this->assertDatabaseCount('bukti_pelaksanaans', 1);
        $this->assertDatabaseHas('bukti_pelaksanaans', [
            'id_indikator' => $indikator->id,
            'komentar'     => 'Komentar diperbarui.',
        ]);
    }

    public function test_submit_fails_validation_without_required_fields(): void
    {
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $response = $this->actingAs($user)->post('/submitPelaksanaan', [
            'data' => [],
        ]);

        $response->assertSessionHasErrors(['data.idIndikator', 'data.komentarPelaksanaan']);
    }

    public function test_submit_fails_with_nonexistent_indikator(): void
    {
        ['pelaksanaan' => $pelaksanaan] = $this->createIndikatorWithPelaksanaan();
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $response = $this->actingAs($user)->post('/submitPelaksanaan', [
            'data' => [
                'idIndikator'         => 99999,
                'komentarPelaksanaan' => 'komentar',
                'idPelaksanaan'       => $pelaksanaan->id,
                'userName'            => $user->name,
            ],
        ]);

        $response->assertSessionHasErrors('data.idIndikator');
    }
}
