<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WarningLetter extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'school_id', 'student_id', 'sp_level', 'document_path', 'notes'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}