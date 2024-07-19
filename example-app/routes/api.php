<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\StudentPersonalInfoController;
use App\Http\Controllers\Api\ResetPassword;
use App\Http\Controllers\SchoolYearController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [UserController::class, 'loginUser']);
Route::post('register', [UserController::class, 'registerUser']);
Route::post('registertransfereeJHS', [UserController::class, 'registerTransfereeJHS']);
Route::post('registershsEnroll', [UserController::class, 'registershsEnroll']);
Route::post('registertransfereeSHS', [UserController::class, 'registertransfereeSHS']);

Route::post('/editstudents', [UserController::class, 'updatestud']);

Route::get('/enrollstudent', [UserController::class, 'showstudent']);
Route::get('/pendingstudent', [UserController::class, 'pendingstudent']);
Route::get('/declinestudent', [UserController::class, 'declinedstudent']);

Route::post('/approvestud/{stud_id}', [UserController::class, 'approvestud']);
Route::post('/declinestud/{stud_id}', [UserController::class, 'declinestud']);

Route::post('sendotp', [UserController::class, 'sendotp']);
Route::post('verifcode', [UserController::class, 'verifyotp']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [UserController::class, 'userDetails']);
    Route::get('logout', [UserController::class, 'logout']);

    Route::post('/create-school-year', [SchoolYearController::class, 'store']);
    Route::get('school-years', [SchoolYearController::class, 'index']);
    Route::put('update-school-year/{id}', [SchoolYearController::class, 'update']);
    Route::delete('delete-school-year/{id}', [SchoolYearController::class, 'destroy']);

    Route::post('/reset/password', [ResetPassword::class, 'resetStudent']);
});
