<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_id', 'timezone', 'time_in', 'time_late', 'time_out',
        'late_light_max', 'late_medium_max', 'notify_in', 'notify_out', 'notify_late'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}