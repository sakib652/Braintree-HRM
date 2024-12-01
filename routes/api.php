<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LeaveTypeController;
use App\Http\Controllers\Api\UserLeaveController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\LeaveAttachmentController;


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

Route::get('/unauthenticate', function () {
    return response()->json([
        "status" => 401,
        "message" => "Please Login",
    ], 401);
})->name('unauthenticate');

//Country
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);
Route::post('/countries', [CountryController::class, 'store']);
Route::put('/countries/{id}', [CountryController::class, 'update']);
Route::delete('/countries/{id}', [CountryController::class, 'destroy']);

//Branch
Route::get('/branches', [BranchController::class, 'index']);
Route::post('/branches', [BranchController::class, 'store']);
Route::get('/branches/{id}', [BranchController::class, 'show']);
Route::put('/branches/{id}', [BranchController::class, 'update']);
Route::delete('/branches/{id}', [BranchController::class, 'destroy']);

//Leave Type
Route::get('/leave-types', [LeaveTypeController::class, 'index']);
Route::post('/leave-types', [LeaveTypeController::class, 'store']);
Route::get('/leave-types/{id}', [LeaveTypeController::class, 'show']);
Route::put('/leave-types/{id}', [LeaveTypeController::class, 'update']);
Route::delete('/leave-types/{id}', [LeaveTypeController::class, 'destroy']);

//User Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/upload-photo', [AttendanceController::class, 'uploadPhoto']);

Route::middleware(['auth:api'])->group(function () {

    //show profile
    Route::get('/profile', [AuthController::class, 'profile']);

    // Edit Profile
    Route::post('/user/edit', [AuthController::class, 'editProfile']);

    //Update Password
    Route::post('/user/update-password', [AuthController::class, 'updatePassword']);

    //Remove User
    Route::post('/users/remove-company', [AuthController::class, 'removeCompany']);

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    //Attendance
    Route::get('attendances', [AttendanceController::class, 'index']);
    Route::post('attendances', [AttendanceController::class, 'store']);
    // Route::get('attendances/{id}', [AttendanceController::class, 'show']);
    // Route::put('attendances/{id}', [AttendanceController::class, 'update']);
    // Route::delete('attendances/{id}', [AttendanceController::class, 'destroy']);

    //User Leave
    Route::get('/user-leaves', [UserLeaveController::class, 'index']);
    Route::post('/user-leaves', [UserLeaveController::class, 'store']);
    Route::get('/user-leaves/{id}', [UserLeaveController::class, 'show']);
    Route::put('/user-leaves/{id}', [UserLeaveController::class, 'update']);
    Route::delete('/user-leaves/{id}', [UserLeaveController::class, 'destroy']);

    //Leave Attachment
    Route::post('/leave-attachments/upload', [LeaveAttachmentController::class, 'uploadAttachment']);
    Route::post('/leave-attachments', [LeaveAttachmentController::class, 'store']);
    Route::put('/leave-attachments/{id}', [LeaveAttachmentController::class, 'update']);

    //Company
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::post('/companies/search', [CompanyController::class, 'search']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::post('/companies/update', [CompanyController::class, 'update']);
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy']);
    Route::get('/companies/{id}/users', [CompanyController::class, 'getUsersByCompany']);
    Route::get('/companies/leaves-count/{id}', [CompanyController::class, 'getLeavesCount']);
    Route::get('/companies/attendance-count/{id}', [CompanyController::class, 'getAttendanceCount']);

    Route::post('/notifications/send-approval-request', [NotificationController::class, 'sendApprovalRequest']);
    Route::post('/notifications/approve', [NotificationController::class, 'approveRequest']);
    Route::get('/notifications/company-details/{user_id}', [NotificationController::class, 'getUserCompanyDetails']);

});


// Route::middleware(['auth:employee'])->group(function () {


// });



