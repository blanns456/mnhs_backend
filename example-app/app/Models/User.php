<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function studentPersonalInformation()
    {
        return $this->hasOne(StudentPersonalInformation::class);
    }

    public function passReset()
    {
        return $this->hasOne(PassReset::class);
    }
}
