<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Daftar Role Baru yang Sudah Disederhanakan
        $roles = [
            'Super Admin',
            'Admin Sekolah',
            'Kepala Sekolah',
            'Guru BK',
            'Guru', // <- Disatukan menjadi satu peran
            'Petugas Piket'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Buat Akun Super Admin Pertama
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@scanattend.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'school_id' => null,
                'is_active' => true,
            ]
        );

        // 3. Berikan role Super Admin ke akun tersebut
        $superAdmin->assignRole('Super Admin');
    }
}