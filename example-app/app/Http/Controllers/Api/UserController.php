<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentPersonalInfo;
use App\Models\StudentEducationalInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController extends Controller
{
    public function registerUser(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:students_personal_information',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:students_personal_information',
            'gradelevel' => 'required',
            'program' => 'required',
            'ip' => 'required',
            'pantawid' => 'required',
            'school_elem' => 'required',
            'school_schoolyr' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'imagefilename' => 'required',
            'signature' => 'required',
        ]);
        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 201);
        }

        try {

            $file = $request->file('imagefilename');
            $extenstion = $file->getClientOriginalExtension();
            $filename = $request->email . time() . '.' . $extenstion;
            $file->move('uploads/userimages/', $filename);

            $student_personal_info = new StudentPersonalInfo();
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

            $educational_info = new StudentEducationalInfo();
            $educational_info->stud_id = $student_personal_info->id;
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
            $educational_info->status = 'jhs';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $educational_info->LRN . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            $this->sendRegistrationEmail($request->email, $request->lrn);


            return response(['user' =>  "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $request->all(),], 200);
        }
    }

    public function registershsEnroll(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:students_personal_information',
            'semester' => 'required',
            'track' => 'required',
            'strand' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:students_personal_information',
            'gradelevel' => 'required',
            'major' => 'required',
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
        ]);
        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 201);
        }

        try {

            $file = $request->file('imagefilename');
            $extenstion = $file->getClientOriginalExtension();
            $filename = $request->email . time() . '.' . $extenstion;
            $file->move('uploads/userimages/', $filename);


            $student_personal_info = new StudentPersonalInfo();
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


            $educational_info = new StudentEducationalInfo();
            $educational_info->stud_id = $student_personal_info->id;
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
            $educational_info->strand = $request->strand;
            $educational_info->special_program = $request->major;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->status = 'shs';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $educational_info->LRN . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' =>  "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $request->all(),], 200);
        }
    }

    public function registerTransfereeJHS(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'schoolID' => 'required',
            'lastgradecompl' => 'required',
            'lastschool' => 'required',
            'lastschool_yr' => 'required',
            'email' => 'required|email|unique:students_personal_information',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:students_personal_information',
            'gradelevel' => 'required',
            'program' => 'required',
            'ip' => 'required',
            'pantawid' => 'required',
            'school_elem' => 'required',
            'school_schoolyr' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'imagefilename' => 'required',
            'signature' => 'required',
        ]);
        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 201);
        }

        try {

            $file = $request->file('imagefilename');
            $extenstion = $file->getClientOriginalExtension();
            $filename = $request->email . time() . '.' . $extenstion;
            $file->move('uploads/userimages/', $filename);


            $student_personal_info = new StudentPersonalInfo();
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


            $educational_info = new StudentEducationalInfo();
            $educational_info->stud_id = $student_personal_info->id;
            $educational_info->LRN = $request->lrn;
            $educational_info->school_elem = $request->school_elem;
            $educational_info->elem_schoolyr = $request->school_schoolyr;
            $educational_info->last_school = $request->lastschool;
            $educational_info->last_schoolyr = $request->lastschool_yr;
            $educational_info->grade_level = $request->gradelevel;
            $educational_info->school_id = $request->schoolID;
            $educational_info->lastgrade_completed = $request->lastgradecompl;
            $educational_info->special_program = $request->program;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->status = 'jhs_transferee';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $educational_info->LRN . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' =>  "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $request->all(),], 200);
        }
    }

    public function registertransfereeSHS(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'schoolID' => 'required',
            'lastgradecompl' => 'required',
            'lastschool' => 'required',
            'lastschool_yr' => 'required',
            'email' => 'required|email|unique:students_personal_information',
            'semester' => 'required',
            'track' => 'required',
            'strand' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'lrn' => 'required|unique:student_education_records',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required|unique:students_personal_information',
            'gradelevel' => 'required',
            'major' => 'required',
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
        ]);
        if ($validator->fails()) {

            return Response(['message' => $validator->errors()], 201);
        }

        try {

            $file = $request->file('imagefilename');
            $extenstion = $file->getClientOriginalExtension();
            $filename = $request->email . time() . '.' . $extenstion;
            $file->move('uploads/userimages/', $filename);


            $student_personal_info = new StudentPersonalInfo();
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


            $educational_info = new StudentEducationalInfo();
            $educational_info->stud_id = $student_personal_info->id;
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
            $educational_info->strand = $request->strand;
            $educational_info->special_program = $request->major;
            $educational_info->m_tounge = $request->m_tounge;
            $educational_info->status = 'shs_transferee';
            $educational_info->account_status = 'pending';
            $educational_info->save();

            $user = new User();
            $user->email = $request->email;
            $user->role_id = 2;
            $user->username = $educational_info->LRN . '@caraga.depEd.gov.ph';
            $user->password = Hash::make('mnhscaraga');
            $user->save();

            $this->sendRegistrationEmail($request->email, $request->lrn);

            return response(['user' =>  "success"], 200);
        } catch (Exception $e) {
            return response(["message" => $request->all(),], 200);
        }
    }

    /**
     * Display a listing of the resource.
     */
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

            $success =  $user->createToken('MyApp')->plainTextToken;

            return Response(['token' => $success], 200);
        }

        return Response(['message' => 'email or password wrong'], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function userDetails()
    {
        if (Auth::check()) {
            $users = Auth::id();

            $user = DB::select("SELECT * FROM `users` JOIN students_personal_information ON users.email = students_personal_information.email JOIN student_education_records ON students_personal_information.id = student_education_records.stud_id WHERE users.id = '$users'");

            return Response(['data' => $user], 200);
        }

        return Response(['data' => 'Unauthorized'], 401);
    }

    public function updatestud(Request $request)
    {

        $id = $request->id;
        $get = StudentPersonalInfo::find($id);
    }

    /**
     * Display the specified resource.
     */
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
            $mail->Username = 'esterlitoroda08@gmail.com';
            $mail->Password = 'qqlgymlynqlufqtn';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->isHTML(true);

            $mail->setFrom('esterlitoroda08@gmail.com');
            $mail->addAddress($email);

            $mail->Subject = 'MNHS Account Information';
            $mail->Body = "<h4>Account: $lrn@caraga.depEd.gov.ph</h4>
                <h4>Password: mnhscaraga</h4>";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error sending email: " . $e->getMessage());
        }
    }
}
