<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\SchoolRepositoryInterface;
use App\Repositories\SchoolRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use App\Repositories\ClassroomRepository;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\StudentRepository;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\AttendanceRepository;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Repositories\SubjectRepository;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Repositories\ScheduleRepository;
use App\Repositories\Contracts\ClassAttendanceRepositoryInterface;
use App\Repositories\ClassAttendanceRepository;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\PermissionRepository;
use App\Repositories\Contracts\CounselingRepositoryInterface;
use App\Repositories\CounselingRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SchoolRepositoryInterface::class, SchoolRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClassroomRepositoryInterface::class, ClassroomRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(ClassAttendanceRepositoryInterface::class, ClassAttendanceRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(CounselingRepositoryInterface::class, CounselingRepository::class);
    }

    public function boot(): void {}
}