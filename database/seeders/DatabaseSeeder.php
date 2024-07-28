<?php

namespace Database\Seeders;

use App\Models\Indikator;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        DB::table('targets')->insert([
            'id_indikator' => 1,
            'value' => 331
        ]);

        DB::table('targets')->insert([
            'id_indikator' => 2,
            'value' => 332
        ]);

        DB::table('targets')->insert([
            'id_indikator' => 3,
            'value' => 333
        ]);

        DB::table('targets')->insert([
            'id_indikator' => 4,
            'value' => 334
        ]);

        DB::table('targets')->insert([
            'id_indikator' => 5,
            'value' => 335
        ]);

        DB::table('targets')->insert([
            'id_indikator' => 6,
            'value' => 336
        ]);

        // Sheet::factory(100)->create();
//        Standar::factory()->count(10)->create();
//        Indikator::factory()->count(10)->create();
//        Target::factory()->count(10)->create();
    }
}
