<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPersonalInformation extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function educationRecord()
    {
        return $this->hasOne(StudentEducationRecord::class, 'student_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id');
    }
}
