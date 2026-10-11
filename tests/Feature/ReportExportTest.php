<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\SchoolSetting;
use App\Models\GateAttendance;
use App\Models\ClassAttendance;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyAttendanceReportMail;
use Illuminate\Support\Str;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_report_index_displays_default_kepsek_nip_and_exporter()
    {
        $school = School::create(['npsn' => '12345678', 'name' => 'SMA Test']);
        
        $kepsek = User::create([
            'name' => 'Drs. H. Mulyadi, M.Pd',
            'email' => 'kepsek@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $kepsek->assignRole('Kepala Sekolah');

        $admin = User::create([
            'name' => 'Faiz Admin',
            'email' => 'admin@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $admin->assignRole('Admin Sekolah');

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Drs. H. Mulyadi, M.Pd');
        $response->assertSee('Faiz Admin');
        $response->assertSee('Pengaturan Tanda Tangan Laporan (Bisa Diedit)');
        $response->assertSee('NIP Kepala Sekolah');
        $response->assertSee('NIP Guru / Penandatangan');
    }

    public function test_report_export_pdf_renders_image_layout_nip_and_signatures()
    {
        $school = School::create(['npsn' => '12345678', 'name' => 'SMA Test', 'address' => 'Jl. Pendidikan, Makassar']);
        SchoolSetting::create([
            'school_id' => $school->id,
            'time_in' => '07:00:00',
            'time_late' => '07:15:00',
            'time_out' => '15:00:00',
            'lesson_duration' => 45,
            'break_duration' => 30,
            'break_after_lesson' => 4,
        ]);

        $classroom = Classroom::create([
            'school_id' => $school->id,
            'level' => 'X',
            'name' => 'X-A',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
            'nis' => '12345',
            'name' => 'Ahmad Santoso',
            'gender' => 'L',
            'parent_phone' => '08123456',
            'parent_email' => 'wali.ahmad@gmail.com',
            'qr_code_string' => Str::random(10),
            'is_active' => true,
        ]);

        $teacher = User::create([
            'name' => 'Guru Budi',
            'email' => 'guru@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $teacher->assignRole('Guru');

        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Matematika',
        ]);

        $schedule = Schedule::create([
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Senin',
            'start_time' => '07:00:00',
            'end_time' => '07:45:00',
        ]);

        $today = date('Y-m-d');

        // Create gate attendance
        GateAttendance::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'date' => $today,
            'scan_in' => '06:55:00',
            'scan_out' => '14:30:00',
            'status' => 'Hadir'
        ]);

        // Create class attendance for Jam 1
        ClassAttendance::create([
            'school_id' => $school->id,
            'schedule_id' => $schedule->id,
            'student_id' => $student->id,
            'date' => $today,
            'status' => 'Hadir'
        ]);

        $kepsek = User::create([
            'name' => 'Kepsek Sukardi',
            'email' => 'kepsek@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $kepsek->assignRole('Kepala Sekolah');

        $admin = User::create([
            'name' => 'Faiz Petugas',
            'email' => 'admin@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $admin->assignRole('Admin Sekolah');

        $response = $this->actingAs($admin)->post(route('admin.reports.export'), [
            'school_id' => $school->id,
            'start_date' => $today,
            'end_date' => $today,
            'format' => 'pdf',
            'report_city' => 'Makassar',
            'report_date' => $today,
            'kepsek_name' => 'Drs. Sukardi, M.Pd',
            'kepsek_nip' => '19750101 200003 1 001',
            'signer_title' => 'Guru',
            'exporter_name' => 'Faiz Petugas',
            'exporter_nip' => '19880202 201201 1 004',
        ]);

        $response->assertStatus(200);
        $response->assertSee('status kehadiran permatkul (perjam)');
        $response->assertSee('absen check in');
        $response->assertSee('absen check out');
        $response->assertSee('mengetahui');
        $response->assertSee('kepala sekolah');
        $response->assertSee('guru');
        $response->assertSee('Drs. Sukardi, M.Pd');
        $response->assertSee('NIP. 19750101 200003 1 001');
        $response->assertSee('Faiz Petugas');
        $response->assertSee('NIP. 19880202 201201 1 004');
        $response->assertSee('Makassar');
        $response->assertSee('12345');
        $response->assertSee('Ahmad Santoso');
        $response->assertSee('06:55');
        $response->assertSee('14:30');
    }

    public function test_report_export_csv_contains_matching_format_and_nip()
    {
        $school = School::create(['npsn' => '12345678', 'name' => 'SMA Test']);
        $admin = User::create([
            'name' => 'Faiz Petugas',
            'email' => 'admin@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $admin->assignRole('Admin Sekolah');

        $today = date('Y-m-d');

        $response = $this->actingAs($admin)->post(route('admin.reports.export'), [
            'school_id' => $school->id,
            'start_date' => $today,
            'end_date' => $today,
            'format' => 'csv',
            'report_city' => 'Makassar',
            'kepsek_name' => 'Nama Kepsek Kustom',
            'kepsek_nip' => '198001012001',
            'signer_title' => 'Guru',
            'exporter_name' => 'Nama Exporter Kustom',
            'exporter_nip' => '198501012002',
        ]);

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringContainsString('absen check in', $content);
        $this->assertStringContainsString('absen check out', $content);
        $this->assertStringContainsString('mengetahui,', $content);
        $this->assertStringContainsString('kepala sekolah', $content);
        $this->assertStringContainsString('Guru', $content);
        $this->assertStringContainsString('Nama Kepsek Kustom', $content);
        $this->assertStringContainsString('NIP. 198001012001', $content);
        $this->assertStringContainsString('Nama Exporter Kustom', $content);
        $this->assertStringContainsString('NIP. 198501012002', $content);
    }

    public function test_weekly_email_report_can_be_sent_to_student_parent()
    {
        Mail::fake();

        $school = School::create(['npsn' => '12345678', 'name' => 'SMA Test']);
        $classroom = Classroom::create([
            'school_id' => $school->id,
            'level' => 'X',
            'name' => 'X-A',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
            'nis' => '12345',
            'name' => 'Ahmad Santoso',
            'gender' => 'L',
            'parent_phone' => '08123456',
            'parent_email' => 'ortu.ahmad@gmail.com',
            'qr_code_string' => Str::random(10),
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@smatest.sch.id',
            'password' => bcrypt('password123'),
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        $admin->assignRole('Admin Sekolah');

        $response = $this->actingAs($admin)->post(route('admin.reports.send_weekly_email'), [
            'school_id' => $school->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(WeeklyAttendanceReportMail::class, function ($mail) use ($student) {
            return $mail->hasTo('ortu.ahmad@gmail.com') && $mail->student->id === $student->id;
        });
    }
}
