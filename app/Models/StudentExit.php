<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'approved_by', 'reason', 'valid_until', 
        'scanned_out_at', 'scanned_in_at', 'status'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'scanned_out_at' => 'datetime',
        'scanned_in_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}