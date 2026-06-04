<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Classroom extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    // Tambahkan 2 baris ini untuk mengunci ID sebagai teks murni (String)
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'school_id',
        'level',
        'name',
        'teacher_id'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}