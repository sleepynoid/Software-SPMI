<?php

namespace Tests\Feature\Sheet;

use App\Models\Jurusan;
use App\Models\Pelaksanaan;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SheetPageTest extends TestCase
{
    use RefreshDatabase;

    private function createSheetFixture(): array
    {
        $jurusan    = Jurusan::factory()->create(['nama' => 'Teknik Informatika', 'kode' => 'TI']);
        $sheet      = Sheet::factory()->create([
            'id_jurusan' => $jurusan->id,
            'periode'    => '2024',
            'tipe_sheet' => 'pendidikan',
        ]);
        Penetapan::factory()->create(['id_sheet' => $sheet->id]);
        Pelaksanaan::factory()->create(['id_sheet' => $sheet->id]);

        return compact('jurusan', 'sheet');
    }

    public function test_guest_cannot_access_sheet_page(): void
    {
        $response = $this->get('/sheet/TI/2024/pendidikan');

        $response->assertRedirect('/login');
    }

    public function test_sheet_page_returns_200_for_authenticated_user(): void
    {
        $this->createSheetFixture();

        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $response = $this->actingAs($user)->get('/sheet/TI/2024/pendidikan');

        $response->assertStatus(200);
    }

    public function test_sheet_page_renders_inertia_sheet_component(): void
    {
        $this->createSheetFixture();
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $this->actingAs($user)
            ->get('/sheet/TI/2024/pendidikan')
            ->assertInertia(fn ($page) => $page->component('sheet', false));
    }

    public function test_sheet_defaults_to_input_step(): void
    {
        $this->createSheetFixture();
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $this->actingAs($user)
            ->get('/sheet/TI/2024/pendidikan')
            ->assertInertia(fn ($page) => $page
                ->component('sheet', false)
                ->where('currentStep', 'input')
            );
    }

    public function test_sheet_accepts_proses_step(): void
    {
        $this->createSheetFixture();
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $this->actingAs($user)
            ->get('/sheet/TI/2024/pendidikan/proses')
            ->assertStatus(200)
            ->assertInertia(fn ($page) => $page->where('currentStep', 'proses'));
    }

    public function test_sheet_accepts_output_step(): void
    {
        $this->createSheetFixture();
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $this->actingAs($user)
            ->get('/sheet/TI/2024/pendidikan/output')
            ->assertStatus(200)
            ->assertInertia(fn ($page) => $page->where('currentStep', 'output'));
    }

    public function test_sheet_inertia_props_contain_role_and_sheet_data(): void
    {
        $this->createSheetFixture();
        $user = User::factory()->create(['role' => 'Pelaksanaan']);

        $this->actingAs($user)
            ->get('/sheet/TI/2024/pendidikan')
            ->assertInertia(fn ($page) => $page
                ->component('sheet', false)
                ->where('role', 'Pelaksanaan')
                ->has('sheetData')
            );
    }
}
