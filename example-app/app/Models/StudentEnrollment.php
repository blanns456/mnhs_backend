<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    public function studentPersonalInformation()
    {
        return $this->belongsTo(StudentPersonalInformation::class, 'student_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
