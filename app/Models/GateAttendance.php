<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GateAttendance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'school_id', 'student_id', 'date', 'scan_in', 'scan_out', 'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}