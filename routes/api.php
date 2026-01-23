<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

/*
* Public Routes
*/
Route::post('/students/register', [StudentController::class, 'store']); // Student Registration
Route::post('/students/verify-email', [StudentController::class, 'verifyEmail']); // Email Verification
Route::post('/students/resend-email-verification', [StudentController::class, 'resendEmailVerification'])->middleware('throttle:3,10'); // Resend Email Verification
Route::post('/students/verify-phone', [StudentController::class, 'verifyPhoneOtp']); // Phone OTP Verification
Route::post('/students/resend-phone-otp', [StudentController::class, 'resendPhoneOtp'])->middleware('throttle:3,10'); // Resend Phone OTP


