<?php

namespace Database\Factories;

use App\Models\Sheet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenetapanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_sheet'     => Sheet::factory(),
            'status'       => 'aktif',
            'submitted_at' => null,
            'submitted_by' => null,
            'catatan'      => null,
        ];
    }
}
