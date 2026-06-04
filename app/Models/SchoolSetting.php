<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'time_in',
        'time_late',
        'time_out',
        'notify_in',
        'notify_out',
        'lesson_duration',
        'break_duration',
        'break_after_lesson'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}