<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEducationRecord extends Model
{
    use HasFactory;

        public function studentPersonalInformation()
    {
        return $this->belongsTo(StudentPersonalInformation::class, 'student_id');
    }
}
