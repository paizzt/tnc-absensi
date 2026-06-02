<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassAttendance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'school_id', 'schedule_id', 'student_id', 'date', 'status', 'notes'
    ];

    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function student() { return $this->belongsTo(Student::class); }
}