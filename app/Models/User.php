<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke tabel schools
     * Satu User (Admin Sekolah/Guru) dimiliki oleh Satu Sekolah
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    // Relasi ke Mata Pelajaran (Banyak ke Banyak)
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    // Relasi ke Kelas sebagai Wali Kelas (Satu ke Satu)
    public function homeroomClass()
    {
        return $this->hasOne(Classroom::class, 'teacher_id');
    }
}