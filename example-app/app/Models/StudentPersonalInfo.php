<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPersonalInfo extends Model
{
    use HasFactory;
    public $table = 'students_personal_information';
    public $timestamps = false;
}
