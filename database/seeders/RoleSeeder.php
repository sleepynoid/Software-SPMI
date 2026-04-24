<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin/LPM',
            'Pimpinan',
            'Auditor',
            'Auditee',
        ];

        foreach ($roles as $role) {
            Role::create(['nama_role' => $role]);
        }
    }
}
