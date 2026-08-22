<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nama_role', 'Admin/LPM')->first()->id;
        $pimpinanRole = Role::where('nama_role', 'Pimpinan')->first()->id;
        $auditorRole = Role::where('nama_role', 'Auditor')->first()->id;
        $auditeeRole = Role::where('nama_role', 'Auditee')->first()->id;

        $lpmUnit = UnitKerja::where('nama_unit', 'LIKE', '%LPM%')->first()->id;
        $prodiUnit = UnitKerja::where('nama_unit', 'Prodi Informatika')->first()->id;

        User::create([
            'nama_lengkap' => 'Administrator LPM',
            'email' => 'admin@spmi.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $adminRole,
            'unit_kerja_id' => $lpmUnit,
            'jenis_user' => 'Tenaga Kependidikan',
        ]);

        User::create([
            'nama_lengkap' => 'Rektor Pelaksana',
            'email' => 'rektor@spmi.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $pimpinanRole,
            'unit_kerja_id' => null,
            'jenis_user' => 'Dosen',
            'nidn' => '1234567890',
        ]);

        User::create([
            'nama_lengkap' => 'Auditor Internal 1',
            'email' => 'auditor@spmi.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $auditorRole,
            'unit_kerja_id' => $lpmUnit,
            'jenis_user' => 'Dosen',
        ]);

        User::create([
            'nama_lengkap' => 'Kaprodi Informatika',
            'email' => 'auditee@spmi.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $auditeeRole,
            'unit_kerja_id' => $prodiUnit,
            'jenis_user' => 'Dosen',
            'nidn' => '0987654321',
        ]);
    }
}
