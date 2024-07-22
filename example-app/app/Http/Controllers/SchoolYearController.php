<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DateTime;
use App\Models\SchoolYear;
use App\Http\Requests\SchoolYearRequest;

class SchoolYearController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolYear::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('school_year', 'LIKE', "%{$searchTerm}%")
                ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                ->orWhere('enrollment_status', 'LIKE', "%{$searchTerm}%");
        }

        if ($request->has('school_start')) {
            $query->where('school_start', '>=', $request->school_start);
        }

        if ($request->has('school_end')) {
            $query->where('school_end', '<=', $request->school_end);
        }

        $schoolYears = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($schoolYears);
    }

    public function store(SchoolYearRequest $request)
    {
        $request->validated();
        $admin_id = Auth::user()->id;

        try {
            $enrollmentStart = Carbon::parse($request->enrollment_start)->addDay();
            $enrollmentEnd = Carbon::parse($request->enrollment_end)->addDay();
            $schoolStart = Carbon::parse($request->school_start)->addDay();
            $schoolEnd = Carbon::parse($request->school_end)->addDay();

            // Extract only the year from the input dates
            $year1 = Carbon::parse($request->school_year1)->year;
            $year2 = Carbon::parse($request->school_year2)->year;

            $schoolYear = new SchoolYear();
            $schoolYear->admin_id = $admin_id;
            $schoolYear->status = 'Active';
            $schoolYear->enrollment_status = 'Enrollment Available';
            $schoolYear->school_year = "S.Y. {$year1} - {$year2}";
            $schoolYear->school_start = $schoolStart->format('Y-m-d');
            $schoolYear->school_end = $schoolEnd->format('Y-m-d');
            $schoolYear->enrollment_start = $enrollmentStart->format('Y-m-d');
            $schoolYear->enrollment_end = $enrollmentEnd->format('Y-m-d');
            $schoolYear->save();

            return response()->json(["message" => "School Year Successfully Added!"], 201);
        } catch (\Exception $e) {
            return response()->json(["message" => "ERROR", "error" => $e->getMessage()], 500);
        }
    }

    public function update(SchoolYearRequest $request, $id)
    {
        $request->validated();
        $admin_id = Auth::user()->id;

        try {
            $enrollmentStart = Carbon::parse($request->enrollment_start)->addDay();
            $enrollmentEnd = Carbon::parse($request->enrollment_end)->addDay();
            $schoolStart = Carbon::parse($request->school_start)->addDay();
            $schoolEnd = Carbon::parse($request->school_end)->addDay();

            // Extract only the year from the input dates
            $year1 = Carbon::parse($request->school_year1)->year;
            $year2 = Carbon::parse($request->school_year2)->year;

            $schoolYear = SchoolYear::findOrFail($id);
            $schoolYear->admin_id = $admin_id;
            $schoolYear->status = 'Active';
            $schoolYear->enrollment_status = 'Enrollment Available';
            $schoolYear->school_year = "S.Y. {$year1} - {$year2}";
            $schoolYear->school_start = $schoolStart->format('Y-m-d');
            $schoolYear->school_end = $schoolEnd->format('Y-m-d');
            $schoolYear->enrollment_start = $enrollmentStart->format('Y-m-d');
            $schoolYear->enrollment_end = $enrollmentEnd->format('Y-m-d');
            $schoolYear->save();

            return response()->json(["message" => "School Year Successfully Updated!"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "ERROR", "error" => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schoolYear = SchoolYear::findOrFail($id);
            $schoolYear->delete();

            return response()->json(["message" => "School Year Successfully Deleted!"], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "ERROR", "error" => $e->getMessage()], 500);
        }
    }

    public function showActiveEnrollment()
    {
        $today = Carbon::today()->format('Y-m-d');
        $activeSchoolYears = SchoolYear::where('enrollment_status', 'Enrollment Available')->get();

        $response = [];

        foreach ($activeSchoolYears as $schoolYear) {
            if ($schoolYear->enrollment_end == $today) {
                $schoolYear->enrollment_status = 'Enrollment Done';
                $schoolYear->save();
                $response[] = [
                    'school_year' => $schoolYear->school_year,
                    'status' => 'Enrollment Done'
                ];
            } else {
                $response[] = [
                    'school_year' => $schoolYear->school_year,
                    'status' => 'Enrollment Available'
                ];
            }
        }

        return response()->json($response);
    }

}
