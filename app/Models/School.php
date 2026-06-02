<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'npsn', 'name', 'email', 'phone', 'address', 'logo'
    ];

    public function settings()
    {
        return $this->hasOne(SchoolSetting::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}