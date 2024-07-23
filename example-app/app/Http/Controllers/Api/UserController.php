<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentPersonalInformation;
use App\Models\StudentEducationRecord;
use App\Models\SchoolYear;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Carbon\Carbon;

class UserController extends Controller
{

    private function getActiveSchoolYears()
    {
        return SchoolYear::where('enrollment_status', 'Enrollment Available')->get();
    }

    public function registerUser(Request $request): Response
    {
        $activeSchoolYears = $this->getActiveSchoolYears();

        if ($activeSchoolYears->isEmpty()) {
            return Response(['message' => 'No active school years available for enrollment'], 400);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:student_personal_information',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:student_personal_information',
            'gradelevel' => 'required',
            'program' => 'required',
            'ip' => 'required',
            'pantawid' => 'required',
            'school_elem' => 'required',
            'school_schoolyr' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'imagefilename' => 'required',
            'form_137' => 'required',
            'signature' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['message' => $validator->errors()], 201);
        }

        try {
            // Handle file uploads
            $file = $request->file('imagefilename');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->email . time() . '.' . $extension;
            $file->move('uploads/userimages/', $filename);

            $file = $request->file('form_137');
            $extension = $file->getClientOriginalExtension();
            $filename2 = $request->email . time() . '.' . $extension;
            $file->move('uploads/form137/', $filename2);

            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $request->lrn . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            $student_personal_info = new StudentPersonalInformation();
            $student_personal_info->user_id = $user->id;
            $student_personal_info->firstname = $request->firstname;
            $student_personal_info->lastname = $request->lastname;
            $student_personal_info->middlename = $request->middlename;
            $student_personal_info->suffix = $request->suffix;
            $student_personal_info->age = $request->age;
            $student_personal_info->birthdate = $request->birthdate;
            $student_personal_info->birth_place = $request->birth_place;
            $student_personal_info->email = $request->email;
            $student_personal_info->mobile_number = $request->mobile_number;
            $student_personal_info->gender = $request->gender;
            $student_personal_info->ip = $request->ip;
            $student_personal_info->pantawid = $request->pantawid;
            $student_personal_info->home_address = $request->home_address;
            $student_personal_info->present_address = $request->present_address;
            $student_personal_info->profile_image = $filename;
            $student_personal_info->signature = $request->signature;
            $student_personal_info->father_lastName = $request->father_lastName;
            $student_personal_info->father_firstName = $request->father_firstName;
            $student_personal_info->father_middleName = $request->father_middleName;
            $student_personal_info->father_number = $request->father_number;
            $student_personal_info->mother_lastName = $request->mother_lastName;
            $student_personal_info->mother_firstName = $request->mother_firstName;
            $student_personal_info->mother_middleName = $request->mother_middleName;
            $student_personal_info->mother_number = $request->mother_number;
            $student_personal_info->guardian_lastName = $request->guardian_lastName;
            $student_personal_info->guardian_firstName = $request->guardian_firstName;
            $student_personal_info->guardian_middleName = $request->guardian_middleName;
            $student_personal_info->guardian_number = $request->guardian_number;
            $student_personal_info->save();

            // Create StudentEducationRecord
            $educational_info = new StudentEducationRecord();
            $educational_info->student_id = $student_personal_info->id;
            $educational_info->LRN = $request->lrn;
            $educational_info->school_elem = $request->school_elem;
            $educational_info->elem_schoolyr = $request->school_schoolyr;
            $educational_info->school_jhs = $request->school_jhs;
            $educational_info->jhs_schoolyr = $request->jhs_schoolyr;
            $educational_info->last_schoolyr = $request->last_schoolyr;
            $educational_info->last_school = $request->last_school;
            $educational_info->grade_level = $request->gradelevel;
            $educational_info->special_program = $request->program;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->form_137 = $filename2;
            $educational_info->status = 'jhs';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            // Create StudentEnrollment
            $enrollment = new StudentEnrollment();
            $enrollment->student_id = $student_personal_info->id;
            $enrollment->year_level = $request->gradelevel;
            $enrollment->school_year_id = $activeSchoolYears->first()->id; // Use the first active school year
            $enrollment->enrollment_date = now();
            $enrollment->save();

            // Send registration email
            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' => "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $e->getMessage()], 500);
        }
    }

    public function registershsEnroll(Request $request): Response
    {
        $activeSchoolYears = $this->getActiveSchoolYears();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:student_personal_information',
            'semester' => 'required',
            'track' => 'required',
            // 'strand' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:student_personal_information',
            'gradelevel' => 'required',
            'ip' => 'required',
            'pantawid' => 'required',
            'elementary' => 'required',
            'elementary_yr' => 'required',
            'jhs' => 'required',
            'jhs_yr' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'imagefilename' => 'required',
            'signature' => 'required',
            'form_137' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 201);
        }

        try {
            // Create user account
            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $request->lrn . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            // $user->created_at = Carbon::now();
            $user->save();

            // Handle profile image upload
            $profileImageFile = $request->file('imagefilename');
            $profileImageExtension = $profileImageFile->getClientOriginalExtension();
            $profileImageName = $request->email . time() . '.' . $profileImageExtension;
            $profileImageFile->move('uploads/userimages/', $profileImageName);

            // Handle form 137 upload
            $form137File = $request->file('form_137');
            $form137Extension = $form137File->getClientOriginalExtension();
            $form137Name = $request->email . '_form137_' . time() . '.' . $form137Extension;
            $form137File->move('uploads/form137/', $form137Name);

            // Save student personal information
            $student_personal_info = new StudentPersonalInformation();
            $student_personal_info->user_id = $user->id; // Assign the user_id
            $student_personal_info->firstname = $request->firstname;
            $student_personal_info->lastname = $request->lastname;
            $student_personal_info->middlename = $request->middle_name;
            $student_personal_info->civil_status = $request->civil_status;
            $student_personal_info->suffix = $request->suffix;
            $student_personal_info->age = $request->age;
            $student_personal_info->birthdate = $request->birthdate;
            $student_personal_info->birth_place = $request->birth_place;
            $student_personal_info->email = $request->email;
            $student_personal_info->mobile_number = $request->mobile_number;
            $student_personal_info->gender = $request->gender;
            $student_personal_info->ip = $request->ip;
            $student_personal_info->pantawid = $request->pantawid;
            $student_personal_info->home_address = $request->home_address;
            $student_personal_info->present_address = $request->present_address;
            $student_personal_info->profile_image = $profileImageName;
            $student_personal_info->signature = $request->signature;
            $student_personal_info->father_lastName = $request->father_lastName;
            $student_personal_info->father_firstName = $request->father_firstName;
            $student_personal_info->father_middleName = $request->father_middleName;
            $student_personal_info->father_number = $request->father_number;
            $student_personal_info->mother_lastName = $request->mother_lastName;
            $student_personal_info->mother_firstName = $request->mother_firstName;
            $student_personal_info->mother_middleName = $request->mother_middleName;
            $student_personal_info->mother_number = $request->mother_number;
            $student_personal_info->guardian_lastName = $request->guardian_lastName;
            $student_personal_info->guardian_firstName = $request->guardian_firstName;
            $student_personal_info->guardian_middleName = $request->guardian_middleName;
            $student_personal_info->guardian_number = $request->guardian_number;
            $student_personal_info->save();

            // Save student education record
            $educational_info = new StudentEducationRecord();
            $educational_info->student_id = $student_personal_info->id;
            $educational_info->LRN = $request->lrn;
            $educational_info->school_elem = $request->elementary;
            $educational_info->elem_schoolyr = $request->elementary_yr;
            $educational_info->school_jhs = $request->jhs;
            $educational_info->jhs_schoolyr = $request->jhs_yr;
            $educational_info->last_schoolyr = $request->last_schoolyr;
            $educational_info->last_school = $request->last_school;
            $educational_info->grade_level = $request->gradelevel;
            $educational_info->school_id = $request->schoolID;
            $educational_info->lastgrade_completed = $request->lastgradecompl;
            $educational_info->semester = $request->semester;
            $educational_info->track = $request->track;
            $educational_info->form_137 = $form137Name;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->status = 'shs';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            // Create StudentEnrollment
            $enrollment = new StudentEnrollment();
            $enrollment->student_id = $student_personal_info->id;
            $enrollment->year_level = $request->gradelevel;
            $enrollment->school_year_id = $activeSchoolYears->first()->id; // Use the first active school year
            $enrollment->enrollment_date = now();
            $enrollment->save();

            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' =>  "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $request->all()], 200);
        }
    }

    // public function registerTransfereeJHS(Request $request): Response
    // {
    //     $activeSchoolYears = $this->getActiveSchoolYears();
    //     $validator = Validator::make($request->all(), [
    //         'schoolID' => 'required',
    //         'lastgradecompl' => 'required',
    //         'lastschool' => 'required',
    //         'lastschool_yr' => 'required',
    //         'email' => 'required|email|unique:student_personal_information',
    //         'firstname' => 'required',
    //         'lastname' => 'required',
    //         'gender' => 'required',
    //         'lrn' => 'required|unique:student_education_records',
    //         'birthdate' => 'required',
    //         'birth_place' => 'required',
    //         'mobile_number' => 'required|unique:student_personal_information',
    //         'gradelevel' => 'required',
    //         'program' => 'required',
    //         'ip' => 'required',
    //         'pantawid' => 'required',
    //         'school_elem' => 'required',
    //         'school_schoolyr' => 'required',
    //         'home_address' => 'required',
    //         'present_address' => 'required',
    //         'imagefilename' => 'required',
    //         'form_137' => 'required',
    //         'signature' => 'required',
    //     ]);
    //     if ($validator->fails()) {

    //         return Response(['message' => $validator->errors()], 201);
    //     }

    //     try {

    //         $profileImageFile = $request->file('imagefilename');
    //         $profileImageExtension = $profileImageFile->getClientOriginalExtension();
    //         $profileImageName = $request->email . time() . '.' . $profileImageExtension;
    //         $profileImageFile->move('uploads/userimages/', $profileImageName);

    //         // Handle form 137 upload
    //         $form137File = $request->file('form_137');
    //         $form137Extension = $form137File->getClientOriginalExtension();
    //         $form137Name = $request->email . '_form137_' . time() . '.' . $form137Extension;
    //         $form137File->move('uploads/form137/', $form137Name);

    //         $user = new User();
    //         $user->email = $request->email;
    //         $user->role_id = 2;
    //         $user->username = $request->lrn . '@caraga.depEd.gov.ph';
    //         $user->password = Hash::make('mnhscaraga');
    //         $user->save();

    //         $student_personal_info = new StudentPersonalInformation();
    //         $student_personal_info->user_id = $user->id;
    //         $student_personal_info->firstname = $request->firstname;
    //         $student_personal_info->firstname = $request->firstname;
    //         $student_personal_info->lastname = $request->lastname;
    //         $student_personal_info->middlename = $request->middlename;
    //         $student_personal_info->suffix = $request->suffix;
    //         $student_personal_info->age = $request->age;
    //         $student_personal_info->birthdate = $request->birthdate;
    //         $student_personal_info->birth_place = $request->birth_place;
    //         $student_personal_info->email = $request->email;
    //         $student_personal_info->mobile_number = $request->mobile_number;
    //         $student_personal_info->gender = $request->gender;
    //         $student_personal_info->ip = $request->ip;
    //         $student_personal_info->pantawid = $request->pantawid;
    //         $student_personal_info->home_address = $request->home_address;
    //         $student_personal_info->present_address = $request->present_address;
    //         $student_personal_info->profile_image = $profileImageName;
    //         $student_personal_info->signature = $request->signature;
    //         $student_personal_info->father_lastName = $request->father_lastName;
    //         $student_personal_info->father_firstName = $request->father_firstName;
    //         $student_personal_info->father_middleName = $request->father_middleName;
    //         $student_personal_info->father_number = $request->father_number;
    //         $student_personal_info->mother_lastName = $request->mother_lastName;
    //         $student_personal_info->mother_firstName = $request->mother_firstName;
    //         $student_personal_info->mother_middleName = $request->mother_middleName;
    //         $student_personal_info->mother_number = $request->mother_number;
    //         $student_personal_info->guardian_lastName = $request->guardian_lastName;
    //         $student_personal_info->guardian_firstName = $request->guardian_firstName;
    //         $student_personal_info->guardian_middleName = $request->guardian_middleName;
    //         $student_personal_info->guardian_number = $request->guardian_number;
    //         $student_personal_info->save();

    //         $educational_info = new StudentEducationRecord();
    //         $educational_info->student_id = $student_personal_info->id;
    //         $educational_info->LRN = $request->lrn;
    //         $educational_info->school_elem = $request->school_elem;
    //         $educational_info->form_137 = $form137Name;
    //         $educational_info->elem_schoolyr = $request->school_schoolyr;
    //         $educational_info->last_school = $request->lastschool;
    //         $educational_info->last_schoolyr = $request->lastschool_yr;
    //         $educational_info->grade_level = $request->gradelevel;
    //         $educational_info->school_id = $request->schoolID;
    //         $educational_info->lastgrade_completed = $request->lastgradecompl;
    //         $educational_info->special_program = $request->program;
    //         $educational_info->m_tounge = $request->m_tounge;
    //         $educational_info->status = 'jhs_transferee';
    //         $educational_info->account_status = 'pending';
    //         $educational_info->save();

    //         $enrollment = new StudentEnrollment();
    //         $enrollment->student_id = $student_personal_info->id;
    //         $enrollment->year_level = $request->gradelevel;
    //         $enrollment->school_year_id = $activeSchoolYears->first()->id;
    //         $enrollment->enrollment_date = now();
    //         $enrollment->save();

    //         $this->sendRegistrationEmail($request->email, $request->lrn);

    //         return response(['user' =>  "success"], 200);
    //     } catch (Exception $e) {
    //         return response(["message" => $request->all(),], 200);
    //     }
    // }
    public function registerTransfereeJHS(Request $request): Response
    {
        $activeSchoolYears = $this->getActiveSchoolYears();

        $validator = Validator::make($request->all(), [
            'schoolID' => 'required',
            'lastgradecompl' => 'required',
            'lastschool' => 'required',
            'lastschool_yr' => 'required',
            'email' => 'required|email|unique:student_personal_information',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:student_personal_information',
            'gradelevel' => 'required',
            'program' => 'required',
            'ip' => 'required',
            'pantawid' => 'required',
            'school_elem' => 'required',
            'school_schoolyr' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'imagefilename' => 'required',
            'form_137' => 'required',
            'signature' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 201);
        }

        try {
            // Handle profile image upload
            $profileImageFile = $request->file('imagefilename');
            $profileImageExtension = $profileImageFile->getClientOriginalExtension();
            $profileImageName = $request->email . time() . '.' . $profileImageExtension;
            $profileImageFile->move('uploads/userimages/', $profileImageName);

            // Handle form 137 upload
            $form137File = $request->file('form_137');
            $form137Extension = $form137File->getClientOriginalExtension();
            $form137Name = $request->email . '_form137_' . time() . '.' . $form137Extension;
            $form137File->move('uploads/form137/', $form137Name);

            // Create user
            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $request->lrn . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            // Create student personal information
            $studentPersonalInfo = new StudentPersonalInformation();
            $studentPersonalInfo->user_id = $user->id;
            $studentPersonalInfo->firstname = $request->firstname;
            $studentPersonalInfo->lastname = $request->lastname;
            $studentPersonalInfo->middlename = $request->middlename;
            $studentPersonalInfo->suffix = $request->suffix;
            $studentPersonalInfo->age = $request->age;
            $studentPersonalInfo->birthdate = $request->birthdate;
            $studentPersonalInfo->birth_place = $request->birth_place;
            $studentPersonalInfo->email = $request->email;
            $studentPersonalInfo->mobile_number = $request->mobile_number;
            $studentPersonalInfo->gender = $request->gender;
            $studentPersonalInfo->ip = $request->ip;
            $studentPersonalInfo->pantawid = $request->pantawid;
            $studentPersonalInfo->home_address = $request->home_address;
            $studentPersonalInfo->present_address = $request->present_address;
            $studentPersonalInfo->profile_image = $profileImageName;
            $studentPersonalInfo->signature = $request->signature;
            $studentPersonalInfo->father_lastName = $request->father_lastName;
            $studentPersonalInfo->father_firstName = $request->father_firstName;
            $studentPersonalInfo->father_middleName = $request->father_middleName;
            $studentPersonalInfo->father_number = $request->father_number;
            $studentPersonalInfo->mother_lastName = $request->mother_lastName;
            $studentPersonalInfo->mother_firstName = $request->mother_firstName;
            $studentPersonalInfo->mother_middleName = $request->mother_middleName;
            $studentPersonalInfo->mother_number = $request->mother_number;
            $studentPersonalInfo->guardian_lastName = $request->guardian_lastName;
            $studentPersonalInfo->guardian_firstName = $request->guardian_firstName;
            $studentPersonalInfo->guardian_middleName = $request->guardian_middleName;
            $studentPersonalInfo->guardian_number = $request->guardian_number;
            $studentPersonalInfo->save();

            // Create student education record
            $educationRecord = new StudentEducationRecord();
            $educationRecord->student_id = $studentPersonalInfo->id;
            $educationRecord->LRN = $request->lrn;
            $educationRecord->school_elem = $request->school_elem;
            $educationRecord->form_137 = $form137Name;
            $educationRecord->elem_schoolyr = $request->school_schoolyr;
            $educationRecord->last_school = $request->lastschool;
            $educationRecord->last_schoolyr = $request->lastschool_yr;
            $educationRecord->grade_level = $request->gradelevel;
            $educationRecord->school_id = $request->schoolID;
            $educationRecord->lastgrade_completed = $request->lastgradecompl;
            $educationRecord->special_program = $request->program;
            $educationRecord->m_tounge = $request->m_tounge;
            $educationRecord->status = 'jhs_transferee';
            $educationRecord->account_status = 'pending';
            $educationRecord->save();

            // Enroll student
            $enrollment = new StudentEnrollment();
            $enrollment->student_id = $studentPersonalInfo->id;
            $enrollment->year_level = $request->gradelevel;
            $enrollment->school_year_id = $activeSchoolYears->first()->id;
            $enrollment->enrollment_date = now();
            $enrollment->save();

            // Send registration email
            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' => 'success'], 200);
        } catch (Exception $e) {
            return response(['message' => $request->all()], 200);
        }
    }

    public function registertransfereeSHS(Request $request): Response
    {
        $activeSchoolYears = $this->getActiveSchoolYears();
        $validator = Validator::make($request->all(), [
            'schoolID' => 'required',
            'lastgradecompl' => 'required',
            'lastschool' => 'required',
            'lastschool_yr' => 'required',
            'email' => 'required|email|unique:student_personal_information',
            'semester' => 'required',
            'track' => 'required',
            // 'strand' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:student_personal_information',
            'gradelevel' => 'required',
            'ip' => 'required',
            'pantawid' => 'required',
            'elementary' => 'required',
            'elementary_yr' => 'required',
            'jhs' => 'required',
            'jhs_yr' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'imagefilename' => 'required',
            'form_137' => 'required',
            'signature' => 'required',
        ]);
        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 201);
        }

        try {

            $profileImageFile = $request->file('imagefilename');
            $profileImageExtension = $profileImageFile->getClientOriginalExtension();
            $profileImageName = $request->email . time() . '.' . $profileImageExtension;
            $profileImageFile->move('uploads/userimages/', $profileImageName);

            // Handle form 137 upload
            $form137File = $request->file('form_137');
            $form137Extension = $form137File->getClientOriginalExtension();
            $form137Name = $request->email . '_form137_' . time() . '.' . $form137Extension;
            $form137File->move('uploads/form137/', $form137Name);

            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $request->lrn . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            $student_personal_info = new StudentPersonalInformation();
            $student_personal_info->user_id = $user->id;
            $student_personal_info->firstname = $request->firstname;
            $student_personal_info->lastname = $request->lastname;
            $student_personal_info->middlename = $request->middle_name;
            $student_personal_info->civil_status = $request->civil_status;
            $student_personal_info->suffix = $request->suffix;
            $student_personal_info->age = $request->age;
            $student_personal_info->birthdate = $request->birthdate;
            $student_personal_info->birth_place = $request->birth_place;
            $student_personal_info->email = $request->email;
            $student_personal_info->mobile_number = $request->mobile_number;
            $student_personal_info->gender = $request->gender;
            $student_personal_info->ip = $request->ip;
            $student_personal_info->pantawid = $request->pantawid;
            $student_personal_info->home_address = $request->home_address;
            $student_personal_info->present_address = $request->present_address;
            $student_personal_info->profile_image = $profileImageName;
            $student_personal_info->signature = $request->signature;
            $student_personal_info->father_lastName = $request->father_lastName;
            $student_personal_info->father_firstName = $request->father_firstName;
            $student_personal_info->father_middleName = $request->father_middleName;
            $student_personal_info->father_number = $request->father_number;
            $student_personal_info->mother_lastName = $request->mother_lastName;
            $student_personal_info->mother_firstName = $request->mother_firstName;
            $student_personal_info->mother_middleName = $request->mother_middleName;
            $student_personal_info->mother_number = $request->mother_number;
            $student_personal_info->guardian_lastName = $request->guardian_lastName;
            $student_personal_info->guardian_firstName = $request->guardian_firstName;
            $student_personal_info->guardian_middleName = $request->guardian_middleName;
            $student_personal_info->guardian_number = $request->guardian_number;
            $student_personal_info->save();


            $educational_info = new StudentEducationRecord();
            $educational_info->student_id = $student_personal_info->id;
            $educational_info->LRN = $request->lrn;
            $educational_info->school_elem = $request->elementary;
            $educational_info->elem_schoolyr = $request->elementary_yr;
            $educational_info->school_jhs = $request->jhs;
            $educational_info->jhs_schoolyr = $request->jhs_yr;
            $educational_info->last_school = $request->lastschool;
            $educational_info->last_schoolyr = $request->lastschool_yr;
            $educational_info->grade_level = $request->gradelevel;
            $educational_info->school_id = $request->schoolID;
            $educational_info->lastgrade_completed = $request->lastgradecompl;
            $educational_info->semester = $request->semester;
            $educational_info->track = $request->track;
            $educational_info->form_137 = $form137Name;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->status = 'shs_transferee';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            $enrollment = new StudentEnrollment();
            $enrollment->student_id = $student_personal_info->id;
            $enrollment->year_level = $request->gradelevel;
            $enrollment->school_year_id = $activeSchoolYears->first()->id;
            $enrollment->enrollment_date = now();
            $enrollment->save();

            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' =>  "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $request->all(),], 200);
        }
    }

    public function loginUser(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 401);
        }

        if (Auth::attempt($request->all())) {

            $user = Auth::user();
            $studrole = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')->where('users.id', $user->id)->select('users.role_id')->value('roles.role_id');

            $success =  $user->createToken('MyApp')->plainTextToken;

            return Response(['token' => $success, 'role' => $studrole], 200);
        }

        return Response(['message' => 'email or password wrong'], 401);
    }

    public function userDetails()
    {
        if (Auth::check()) {
            $users = Auth::id();

            $user = DB::select("SELECT * FROM `users` JOIN student_personal_information ON users.id = student_personal_information.user_id JOIN student_education_records ON student_personal_information.id = student_education_records.student_id WHERE users.id = '$users'");

            return Response(['data' => $user], 200);
        }

        return Response(['data' => 'Unauthorized'], 401);
    }

    public function showstudent()
    {

        $enrolled = DB::select("SELECT * FROM `users` JOIN student_personal_information ON users.id = student_personal_information.user_id JOIN student_education_records ON student_personal_information.id = student_education_records.student_id WHERE student_education_records.account_status = 'enrolled' and users.role_id = '2'");

        return response($enrolled, 201);
    }

    public function pendingstudent()
    {

        $pending = DB::select("SELECT student_personal_information.id as studid, CONCAT(firstname, ' ', lastname) as studname, student_education_records.grade_level as gradelevel, student_education_records.LRN as LRN, student_education_records.account_status as status FROM `users` JOIN student_personal_information ON users.email = student_personal_information.email JOIN student_education_records ON student_personal_information.id = student_education_records.student_id WHERE student_education_records.account_status = 'pending' and users.role_id = '2'");

        return response($pending, 201);
    }

    public function declinedstudent()
    {
        $declined = DB::select("SELECT * FROM `users` JOIN student_personal_information ON users.email = student_personal_information.email JOIN student_education_records ON student_personal_information.id = student_education_records.student_id WHERE student_education_records.account_status = 'declined' and users.role_id = '2'");

        return response($declined, 201);
    }

    public function approvestud(string $id)
    {

        $x = StudentEducationRecord::where('student_id', $id)->first();
        $x->account_status = 'enrolled';
        $x->save();

        // $this->approvalMail($x->email);

        return response([["message" => "Success"]], 201);
    }

    public function declinestud(string $id)
    {

        $x = StudentEducationRecord::where('student_id', $id)->first();
        $x->account_status = 'declined';
        $x->save();

        return response([["message" => "Success"]], 201);
    }

    public function updatestud(Request $request)
    {
        // dd($request);

        $validator = Validator::make($request->all(), [
            'first_name' => 'max:255|nullable',
            'middle_name' => 'max:255|nullable|string',
            'last_name' => 'max:255|nullable',
            'suffix' => 'max:255|nullable|string',
            'gender' => 'max:255|nullable',
            'age' => 'max:255|nullable',
            'lrn' => 'max:255|nullable',
            'religion' => 'max:255|nullable|string',
            'contact_number' => 'numeric|max:255',
            // 'email' => 'max:255|nullable',
            'birthdate' => 'nullable|date:Y-m-d',
            'birth_place' => 'max:255|nullable',
            'home_address' => 'max:255|nullable',
            'present_address' => 'max:255|nullable',
            'elementary' => 'max:255|nullable',
            'elementary_yr' => 'max:255|nullable',
            'jhs' => 'max:255|nullable',
            'jhs_yr' => 'max:255|nullable',
            'shs_school' => 'max:255|nullable|string',
            'shs_yr' => 'max:255|nullable',
            'last_school' => 'max:255|nullable',
            'last_school_year' => 'max:255|nullable',
            // 'profile' => '',
            // 'signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Response(['message' => $validator->errors()], 201);
        }

        $id = $request->studid;
        $studpersonal = StudentPersonalInformation::where('user_id', $id)->first();
        // $studpersonal = DB::table('student_personal_information')->where('id', $id)->first();

        if ($studpersonal) {
            $studpersonal->firstname = $request->first_name;
            $studpersonal->lastname = $request->last_name;
            $studpersonal->middlename = $request->middle_name;
            $studpersonal->civil_status = $request->civil_status;
            $studpersonal->suffix = $request->suffix;
            $studpersonal->age = $request->age;
            $studpersonal->birthdate = $request->birthdate;
            $studpersonal->birth_place = $request->birth_place;
            // $studpersonal->email = $request->email;
            $studpersonal->mobile_number = $request->mobile_number;
            $studpersonal->gender = $request->gender;
            $studpersonal->ip = $request->ip;
            $studpersonal->pantawid = $request->pantawid;
            $studpersonal->home_address = $request->home_address;
            $studpersonal->present_address = $request->present_address;
            $studpersonal->father_lastName = $request->father_lastName;
            $studpersonal->father_firstName = $request->father_firstName;
            $studpersonal->father_middleName = $request->father_middleName;
            $studpersonal->father_number = $request->father_number;
            $studpersonal->mother_lastName = $request->mother_lastName;
            $studpersonal->mother_firstName = $request->mother_firstName;
            $studpersonal->mother_middleName = $request->mother_middleName;
            $studpersonal->mother_number = $request->mother_number;
            $studpersonal->guardian_lastName = $request->guardian_lastName;
            $studpersonal->guardian_firstName = $request->guardian_firstName;
            $studpersonal->guardian_middleName = $request->guardian_middleName;
            $studpersonal->guardian_number = $request->guardian_number;
            // $file = $request->file('profile');
            // $extenstion = $file->getClientOriginalExtension();
            // $filename = $request->unique_id . time() . '.' . $extenstion;
            // $file->move('uploads/userimages/', $filename);

            // $studpersonal->signature = $request->signature;
            // $studpersonal->profile_image = $filename;
            $studpersonal->update();
        } else {
            return response()->json(['message' => 'Student info not found'], 404);
        }

        $educational_info = $studpersonal->educationRecord;

        if ($educational_info) {
            $educational_info->LRN = $request->lrn;
            $educational_info->school_elem = $request->elementary;
            $educational_info->elem_schoolyr = $request->elementary_yr;
            $educational_info->school_jhs = $request->jhs;
            $educational_info->jhs_schoolyr = $request->jhs_yr;
            $educational_info->last_school = $request->last_school;
            $educational_info->last_schoolyr = $request->last_schoolyr;
            $educational_info->grade_level = $request->enrolling_for;
            $educational_info->school_id = $request->schoolID;
            $educational_info->lastgrade_completed = $request->lastgradecompl;
            // $educational_info->semester = $request->semester;
            $educational_info->special_program = $request->special_program;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->update();
        } else {
            return response()->json(['message' => 'Educational info not found'], 404);
        }

        return response(['message' => 'Update Success'], 201);
    }

    public function logout(): Response
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return Response(['data' => 'User Logout successfully.'], 200);
    }

    private function sendRegistrationEmail($email, $lrn)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mnhsystem1@gmail.com';
            $mail->Password = 'sbmylwmffhjaugmt';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->isHTML(true);

            $mail->setFrom('mnhsystem1@gmail.com');
            $mail->addAddress($email);

            $mail->Subject = 'MNHS Account Information';
            $mail->Body = "<h4>Account: $lrn@caraga.depEd.gov.ph</h4>
                <h4>Password: mnhscaraga</h4>";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error sending email: " . $e->getMessage());
        }
    }

    private function approvalMail($email)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'esterlitoroda08@gmail.com';
            $mail->Password = 'qqlgymlynqlufqtn';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->isHTML(true);

            $mail->setFrom('mnhsystem1@gmail.com');
            $mail->addAddress('rodajohvincent35@gmail.com');

            $mail->Subject = 'MNHS Online Enrollment System';
            $mail->Body = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Enrollment Confirmation</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        background-color: #f4f4f4;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        color: #333;
                        text-align: center;
                    }
                    p {
                        color: #555;
                        font-size: 16px;
                        line-height: 1.6;
                    }
                </style>
                </head>
                <body>
                <div class="container">
                    <h1>Enrollment Confirmation</h1>
                    <p>Congratulations! You are successfully enrolled.</p>
                </div>
                </body>
                </html>
            ';
            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error sending email: " . $e->getMessage());
        }
    }

    public function sendotp(Request $request)
    {

        $email = $request->input('email');
        $student = StudentPersonalInformation::where('email', $email)->first();
        $verificationCode = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $start = Carbon::now('Asia/Manila');
        $expire = Carbon::parse($start)->addMinutes(5);

        if ($student) {
            DB::table('pass_resets')->insert([
                'user_id' => $student->user_id,
                'email' => $email,
                'otp' => $verificationCode,
                'start' => $start,
                'expire' => $expire,
            ]);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'esterlitoroda08@gmail.com';
            $mail->Password = 'qqlgymlynqlufqtn';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->isHTML(true);

            $mail->setFrom('jamesbadang16@gmail.com');
            $mail->addAddress($request->input('email'));

            $mail->Subject = 'OTP Verification Code';
            $mail->Body = '<html lang="en">
            <head>
              <meta charset="UTF-8" />
              <meta name="viewport" content="width=device-width, initial-scale=1.0" />
              <title>OTP Verification</title>
              <style>
                /* Reset CSS */
                body,
                h1,
                p {
                  margin: 0;
                  padding: 0;
                }

                body {
                  font-family: Arial, sans-serif;
                  background-color: #f4f4f4;
                }

                .container {
                  max-width: 500px;
                  margin: 20px auto;
                  padding: 20px;
                  background-color: #ffffff;
                  border-radius: 10px;
                  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                h1 {
                  color: #333333;
                  font-size: 24px;
                  text-align: center;
                  margin-bottom: 20px;
                }

                p {
                  color: #555555;
                  line-height: 1.6;
                  margin-bottom: 20px;
                  text-align: center;
                }

                .otp {
                  background-color: #f9f9f9;
                  padding: 10px;
                  text-align: center;
                  border-radius: 5px;
                  font-size: 28px;
                  margin: 0 auto 20px auto;
                  max-width: 80%;
                }

                .button {
                  background-color: #0066ff;
                  color: #ffffff;
                  text-decoration: none;
                  padding: 10px 20px;
                  border-radius: 5px;
                  display: block;
                  text-align: center;
                  margin: 0 auto;
                  width: fit-content;
                }

              </style>
            </head>

            <body>
              <div class="container">
                <h1>OTP Verification</h1>
                <p>Good day Student,</p>
                <p>Here is your OTP CODE please do not share:</p>
                <div class="otp">' . $verificationCode . '</div> <!-- Insert verification code here -->
                <p>Please proceed to log in to complete the required updates to your account information.</p>

                <a href="https://genesys.asc-bislig.com/#/login" target="_blank" class="button">Log in</a>
              </div>
            </body>
            </html>';
            $mail->send();
            return response()->json([
                'message' => 'User found',
                'data' => $student
            ], 201);
        } else {

            return response()->json([
                'message' => 'User not found'
            ], 201);
        }
    }

    public function verifyotp(Request $request)
    {

        $otpcode = $request->input('otpcode');

        $check = DB::table('pass_resets')->select('*')
            ->where('otp', $otpcode)
            ->first();

        if ($check) {
            return response(['message' => 'Verified', $check], 201);
        } else {
            return response(['message' => 'Not verified'], 201);
        }
    }

    public function resetPassword(Request $request)
    {

        $resetData = DB::table('pass_resets')
            ->join('users', 'pass_resets.user_id', '=', 'users.id')
            ->where('otp', $request->checkcode)
            ->first();

        if (!$resetData) {
            return response()->json(['message' => 'Invalid Code'], 201);
        }

        $user = User::find($resetData->user_id);
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            return response()->json(['status' => 'Success'], 201);
        } else {
            return response()->json(['status' => 'Failed to reset password'], 500);
        }
    }
}
