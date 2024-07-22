<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentEnrollment;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = StudentEnrollment::selectRaw('school_years.school_year, COUNT(student_enrollments.id) as student_count')
            ->join('school_years', 'student_enrollments.school_year_id', '=', 'school_years.id')
            ->groupBy('school_years.school_year')
            ->get();

        return response()->json($enrollments);
    }

}
