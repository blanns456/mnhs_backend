<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentPersonalInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;

class UserController extends Controller
{
    public function registerUser(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'civil_status' => 'required',
            'birthdate' => 'required',
            'birth_place' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
            'birth_place' => 'required',
            'religion' => 'required',
            'citizenship' => 'required',
            'home_address' => 'required',
            'present_address' => 'required',
            'mother_name' => 'required',
            'father_name' => 'required',
            'mother_occupation' => 'required',
            'father_occupation' => 'required',
            'mother_contactnumber' => 'required',
            'father_contactnumber' => 'required',
            'signature' => 'required',
        ]);
        if($validator->fails()){

            return Response(['message' => $validator->errors()],201);
        }
        
        try{ 
            
            $file = $request->file('imagefilename');
            $extenstion = $file->getClientOriginalExtension();
            $filename =$request->email.time().'.'.$extenstion;
            $file->move('uploads/userimages/', $filename);


            $student_personal_info = new StudentPersonalInfo();
            $student_personal_info->firstname = $request->firstname;
            $student_personal_info->lastname = $request->lastname;
            $student_personal_info->middlename = $request->middlename;
            $student_personal_info->suffix = $request->suffix;
            $student_personal_info->gender = $request->gender;
            $student_personal_info->civil_status = $request->civil_status;
            $student_personal_info->birthdate = $request->birthdate;
            $student_personal_info->birth_place = $request->birth_place;
            $student_personal_info->email = $request->email;
            $student_personal_info->mobile_number = $request->mobile_number;
            $student_personal_info->citizenship = $request->citizenship;
            $student_personal_info->religion = $request->religion;
            $student_personal_info->home_address = $request->home_address;
            $student_personal_info->present_address = $request->present_address;
            $student_personal_info->mother_name = $request->mother_name;
            $student_personal_info->father_name = $request->father_name;
            $student_personal_info->mother_occupation = $request->mother_occupation;
            $student_personal_info->father_occupation = $request->father_occupation;
            $student_personal_info->mother_contactnumber = $request->mother_contactnumber;
            $student_personal_info->father_contactnumber = $request->father_contactnumber;
            $student_personal_info->profile_image = $filename;
            $student_personal_info->signature = $request->signature;
            $student_personal_info->save();


            $educational_info = new StudentEducationalInfo();
            $educational_info->stud_id = $student_personal_info->id;
            $educational_info->school_last_attended_name = $student_personal_info->school_elem;
            $educational_info->school_last_attended_sy = $student_personal_info->school_schoolyr;
            $educational_info->save();

            $user = new User();
            $user->email = $request->email;
            $user->username = $student_personal_info->firstname.$student_personal_info->id;
            $user->password = Hash::make($student_personal_info->firstname."123");
            $user->save();

            
            
      
            return response(['user' =>  "success"],200);
        }catch(Exception $e){
           return response(["message" => $e->getMessage()], 201);
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
   
        if($validator->fails()){

            return Response(['message' => $validator->errors()],401);
        }
   
        if(Auth::attempt($request->all())){

            $user = Auth::user(); 
    
            $success =  $user->createToken('MyApp')->plainTextToken; 
        
            return Response(['token' => $success],200);
        }

        return Response(['message' => 'email or password wrong'],401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function userDetails(): Response
    {
        if (Auth::check()) {

            $user = Auth::user();

            return Response(['data' => $user],200);
        }

        return Response(['data' => 'Unauthorized'],401);
    }

    /**
     * Display the specified resource.
     */
    public function logout(): Response
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();
        
        return Response(['data' => 'User Logout successfully.'],200);
    }
}