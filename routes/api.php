<?php

use App\Http\Controllers\GuardianController;
use App\Http\Controllers\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

/*
* Public Routes
*/

/** 
 * Student Routes
 **/

/*
|--------------------------------------------------------------------------
| Student Registration & Verification
|--------------------------------------------------------------------------
*/

Route::prefix('students')->group(function () {

    // Registration
    Route::post('/register', [StudentController::class, 'store']);

    // Email verification
    Route::get('/verify-email', [StudentController::class, 'verifyEmail']);
    Route::post('/resend-email-verification', [StudentController::class, 'resendEmailVerification']);

    // Phone OTP verification
    Route::post('/verify-phone', [StudentController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [StudentController::class, 'resendPhoneOtp']);

    // Biodata completion (NO AUTH REQUIRED, but verification enforced)
    Route::post('/biodata', [StudentController::class, 'biodata']);
});

/** 
 * Staff Routes
 **/

/*
|--------------------------------------------------------------------------
| Staff Registration & Verification
|--------------------------------------------------------------------------
*/

Route::prefix('staffs')->group(function () {

    // Registration (Admin only — enforced in controller)
    Route::post('/register', [StaffController::class, 'store']);

    // Email verification
    Route::get('/verify-email', [StaffController::class, 'verifyEmail']);
    Route::post('/resend-email-verification', [StaffController::class, 'resendEmailVerification']);

    // Phone OTP verification
    Route::post('/verify-phone', [StaffController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [StaffController::class, 'resendPhoneOtp']);

    // Login (restricted until verified)
    Route::post('/login', [StaffController::class, 'login']);
});

/** 
 * Guardian Routes
 **/

/*
|--------------------------------------------------------------------------
| Guardian Registration & Verification
|--------------------------------------------------------------------------
*/

Route::prefix('guardians')->group(function () {
    Route::post('/register', [GuardianController::class, 'store']);
    Route::post('/verify-email', [GuardianController::class, 'verifyEmail']);

    Route::post('/verify-phone', [GuardianController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [GuardianController::class, 'resendPhoneOtp']);

    Route::post('/resend-email', [GuardianController::class, 'resendEmailVerification']);
});




/*
* Admin Only Protected Routes
*/
// Route::middleware(['auth:staff', 'staff.role:admin'])->group(function () {
//     Route::post('/staff/register', [StaffController::class, 'store']); // Staff Registration
// });


