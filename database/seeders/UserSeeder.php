<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'user peningkatan',
            'email' => 'peningkatan@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'Peningkatan',
        ]);

        User::create([
            'name' => 'user evaluasi',
            'email' => 'evaluasi@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'Evaluasi',
        ]);

        User::create([
            'name' => 'user pelaksanaan',
            'email' => 'pelaksanaan@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'Pelaksanaan',
        ]);

        User::create([
            'name' => 'user pengendalian',
            'email' => 'pengendalian@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'Pengendalian',
        ]);
    }
}
