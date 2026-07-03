<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\SchoolSetting;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Str;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_gate_scanner_can_scan_student()
    {
        // 1. Persiapan Data
        $school = School::create(['npsn' => '12345678', 'name' => 'Sekolah Test']);
        SchoolSetting::create([
            'school_id' => $school->id,
            'time_in' => '07:00:00',
            'time_late' => '07:15:00',
            'time_out' => '15:00:00',
            'notify_in' => false,
            'notify_out' => false,
            'lesson_duration' => 45,
            'break_duration' => 30,
            'break_after_lesson' => 4
        ]);
        
        $classroom = Classroom::create([
            'school_id' => $school->id,
            'level' => 'X',
            'name' => 'X IPA 1'
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
            'nis' => '10001',
            'name' => 'Siswa Test',
            'gender' => 'L',
            'parent_phone' => '08123456',
            'qr_code_string' => Str::random(10),
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Piket',
            'email' => 'piket@sekolah.com',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $admin->assignRole('Petugas Piket');

        // 2. Simulasi Login
        $this->actingAs($admin);

        // 3. Simulasi Scan Masuk
        $response = $this->postJson(route('admin.attendances.scan_process'), [
            'qr_code' => $student->qr_code_string
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('gate_attendances', [
            'student_id' => $student->id,
            'date' => date('Y-m-d')
        ]);
    }
}
