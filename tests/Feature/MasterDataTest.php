<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Database\Seeders\RolePermissionSeeder;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_superadmin_can_create_school()
    {
        $superadmin = User::role('Super Admin')->first();
        $this->actingAs($superadmin);

        $response = $this->post('/schools', [
            'npsn' => '12345678',
            'name' => 'Sekolah Test',
            'email' => 'test@sekolah.com',
            'phone' => '08123456',
            'address' => 'Jl. Test'
        ]);

        $response->assertRedirect(route('schools.index'));
        $this->assertDatabaseHas('schools', ['npsn' => '12345678']);
    }

    public function test_superadmin_can_create_user()
    {
        $superadmin = User::role('Super Admin')->first();
        $this->actingAs($superadmin);
        
        $school = School::create(['npsn' => '11111111', 'name' => 'Sekolah A']);

        $response = $this->post('/users', [
            'name' => 'Guru Budi',
            'email' => 'budi@sekolah.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Guru',
            'school_id' => $school->id,
            'is_active' => 1
        ]);

        $response->assertRedirect(route('users.index', ['school_id' => $school->id]));
        $this->assertDatabaseHas('users', ['email' => 'budi@sekolah.com']);
    }

    public function test_guru_cannot_access_school_management()
    {
        $school = School::create(['npsn' => '22222222', 'name' => 'Sekolah B']);
        $guru = User::create([
            'name' => 'Guru A',
            'email' => 'guru@sekolaha.com',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $guru->assignRole('Guru');

        $this->actingAs($guru);
        $response = $this->get('/schools');
        
        $response->assertStatus(403);
    }
}
