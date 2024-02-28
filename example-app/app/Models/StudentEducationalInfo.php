<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEducationalInfo extends Model
{
    use HasFactory;
    public $table = 'student_education_records';
    public $timestamps = false;
}
