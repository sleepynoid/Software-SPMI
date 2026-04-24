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
        $adminRole = \App\Models\Role::where('nama_role', 'Admin/LPM')->first()->id;
        $pimpinanRole = \App\Models\Role::where('nama_role', 'Pimpinan')->first()->id;
        $auditorRole = \App\Models\Role::where('nama_role', 'Auditor')->first()->id;
        $auditeeRole = \App\Models\Role::where('nama_role', 'Auditee')->first()->id;

        $lpmUnit = \App\Models\UnitKerja::where('nama_unit', 'LIKE', '%LPM%')->first()->id;
        $prodiUnit = \App\Models\UnitKerja::where('nama_unit', 'Prodi Informatika')->first()->id;

        // Admin
        User::create([
            'nama_lengkap' => 'Administrator LPM',
            'email' => 'admin@spmi.ac.id',
            'password' => bcrypt('password'),
            'role_id' => $adminRole,
            'unit_kerja_id' => $lpmUnit,
            'jenis_user' => 'Tenaga Kependidikan',
        ]);

        // Pimpinan
        User::create([
            'nama_lengkap' => 'Rektor Pelaksana',
            'email' => 'rektor@spmi.ac.id',
            'password' => bcrypt('password'),
            'role_id' => $pimpinanRole,
            'unit_kerja_id' => null,
            'jenis_user' => 'Dosen',
            'nidn' => '1234567890',
        ]);

        // Auditor
        User::create([
            'nama_lengkap' => 'Auditor Internal 1',
            'email' => 'auditor@spmi.ac.id',
            'password' => bcrypt('password'),
            'role_id' => $auditorRole,
            'unit_kerja_id' => $lpmUnit,
            'jenis_user' => 'Dosen',
        ]);

        // Auditee
        User::create([
            'nama_lengkap' => 'Kaprodi Informatika',
            'email' => 'auditee@spmi.ac.id',
            'password' => bcrypt('password'),
            'role_id' => $auditeeRole,
            'unit_kerja_id' => $prodiUnit,
            'jenis_user' => 'Dosen',
            'nidn' => '0987654321',
        ]);
    }
}
