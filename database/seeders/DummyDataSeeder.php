<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Sekolah
        $school = School::create([
            'name' => 'SMA Negeri 1 Simulasi',
            'npsn' => '12345678',
            'address' => 'Jl. Pendidikan No. 1, Jakarta',
            'phone' => '021-1234567',
            'email' => 'info@sman1simulasi.sch.id',
        ]);

        // 2. Buat Pengaturan Sekolah
        SchoolSetting::create([
            'school_id' => $school->id,
            'time_in' => '07:00:00',
            'time_late' => '07:15:00',
            'time_out' => '15:00:00',
            'notify_in' => true,
            'notify_out' => true,
        ]);

        // 3. Buat Pengguna (Users)
        $usersData = [
            ['name' => 'Budi Admin', 'email' => 'admin@sekolah.com', 'role' => 'Admin Sekolah'],
            ['name' => 'Siti Kepsek', 'email' => 'kepsek@sekolah.com', 'role' => 'Kepala Sekolah'],
            ['name' => 'Agus BK', 'email' => 'bk@sekolah.com', 'role' => 'Guru BK'],
            ['name' => 'Rina Piket', 'email' => 'piket@sekolah.com', 'role' => 'Petugas Piket'],
            ['name' => 'Dedi Guru', 'email' => 'guru1@sekolah.com', 'role' => 'Guru'],
            ['name' => 'Nisa Guru', 'email' => 'guru2@sekolah.com', 'role' => 'Guru'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = User::create([
                'school_id' => $school->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            $user->assignRole($data['role']);
            $users[$data['role']][] = $user;
        }

        // 4. Buat Mata Pelajaran
        $subjects = [];
        $subjectNames = ['Matematika', 'Bahasa Indonesia', 'IPA'];
        foreach ($subjectNames as $name) {
            $subject = Subject::create([
                'school_id' => $school->id,
                'name' => $name,
            ]);
            // Assign ke Guru
            $guru = $users['Guru'][array_rand($users['Guru'])];
            $subject->teachers()->attach($guru->id);
            $subjects[] = $subject;
        }

        // 5. Buat Kelas
        $classrooms = [];
        $classLevels = ['X', 'XI'];
        foreach ($classLevels as $index => $level) {
            $classroom = Classroom::create([
                'school_id' => $school->id,
                'level' => $level,
                'name' => $level . ' IPA 1',
                'teacher_id' => $users['Guru'][$index]->id, // Wali Kelas
            ]);
            $classrooms[] = $classroom;
        }

        // 6. Buat Siswa
        foreach ($classrooms as $classroom) {
            for ($i = 1; $i <= 5; $i++) {
                Student::create([
                    'school_id' => $school->id,
                    'classroom_id' => $classroom->id,
                    'nis' => '1000' . rand(10, 99) . $classroom->level . $i,
                    'name' => 'Siswa ' . $i . ' ' . $classroom->name,
                    'gender' => $i % 2 == 0 ? 'P' : 'L',
                    'parent_phone' => '08123456789' . $i,
                    'qr_code_string' => Str::random(10),
                    'is_active' => true,
                ]);
            }
        }

        // 7. Buat Jadwal (Roster)
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($classrooms as $classroom) {
            foreach ($subjects as $index => $subject) {
                Schedule::create([
                    'school_id' => $school->id,
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $subject->teachers->first()->id,
                    'day_of_week' => $days[$index % count($days)],
                    'start_time' => '07:00:00',
                    'end_time' => '08:30:00',
                ]);
            }
        }
    }
}
