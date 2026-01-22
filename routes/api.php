<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

/*
 * Public Routes
*/
Route::post('/students/register', [StudentController::class, 'store']); // Student Registration
Route::post('/students/verify-email', [StudentController::class, 'verifyEmail']); // Email Verification


