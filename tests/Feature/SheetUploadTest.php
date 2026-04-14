<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use App\Models\User;

class SheetUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function evaluasi_user_can_upload_sheet()
    {
        \Maatwebsite\Excel\Facades\Excel::fake();

        // Create a user with role Evaluasi
        $user = User::factory()->create(['role' => 'Evaluasi']);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test.xlsx');

        $response = $this->postJson('/import', [
            'file' => $file,
            'jurusan' => 'Teknik Mesin',
            'tipe' => 'pendidikan',
            'periode' => '2023-2024 Ganjil',
            'note' => 'Test upload',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function non_evaluasi_user_cannot_upload_sheet()
    {
        $user = User::factory()->create(['role' => 'Admin']);
        $this->actingAs($user);
        $file = UploadedFile::fake()->create('test.xlsx');
        $response = $this->postJson('/import', [
            'file' => $file,
            'jurusan' => 'Teknik Mesin',
            'tipe' => 'pendidikan',
            'periode' => '2023-2024 Ganjil',
            'note' => 'Test upload',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }
}
?>
