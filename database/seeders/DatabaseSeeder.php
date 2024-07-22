<?php

namespace Database\Seeders;

use App\Models\Indikator;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'id' => 1,
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('pass'),
        // ]);

        // Sheet::factory(100)->create();
        Standar::factory()->count(10)->create();
        Indikator::factory()->count(10)->create();
        Target::factory()->count(10)->create();
    }
}
