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
        // 1. Buat Daftar Role sesuai SOP
        $roles = [
            'Super Admin',
            'Admin Sekolah',
            'Kepala Sekolah',
            'Guru BK',
            'Wali Kelas',
            'Guru Mata Pelajaran',
            'Petugas Piket'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Buat Akun Super Admin Pertama
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@scanattend.com'], // Patokan pengecekan agar tidak duplikat
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'), // Password default
                'school_id' => null, // Super admin tidak terikat 1 sekolah
                'is_active' => true,
            ]
        );

        // 3. Berikan role Super Admin ke akun tersebut
        $superAdmin->assignRole('Super Admin');
    }
}