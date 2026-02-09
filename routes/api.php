<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/courses', [CourseController::class, 'index']); // Public: List all active courses
Route::get('/subjects', [SubjectController::class, 'index']); // Public: List all active subjects
Route::post('/course/enrollment', [CourseController::class, 'courseEnroll']); // Public: Enroll in a course
Route::post('/subject/enrollment', [SubjectController::class, 'subjectEnroll']); // Public: Enroll in a subject
Route::get('/courses/{courseId}/subjects', [SubjectController::class, 'subjectsByCourse']); // Public: List subjects by course
Route::get('/courses/{courseId}/subjects/{department}',[SubjectController::class, 'subjectsByCourseAndDepartment']); // Public: List subjects by course and department
Route::post('payments', [PaymentController::class, 'store']); // Public: Process payment

/*
|--------------------------------------------------------------------------
| Student Public Routes
|--------------------------------------------------------------------------
*/
Route::prefix('students')->group(function () {

    // Registration
    Route::post('/register', [StudentController::class, 'store']);
    // Email verification
    Route::post('/verify-email', [StudentController::class, 'verifyEmail']);
    Route::post('/resend-email-verification', [StudentController::class, 'resendEmailVerification']);
    // Phone OTP verification
    Route::post('/verify-phone', [StudentController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [StudentController::class, 'resendPhoneOtp']);
    // Biodata completion (NO AUTH REQUIRED, but verification enforced)
    Route::post('/biodata', [StudentController::class, 'biodata']);
    // Course enrollment
    Route::post('/enroll-course', [CourseController::class, 'courseEnroll']);
    // Login Method
    Route::post('/login', [StudentController::class, 'login']);

});

/*
|--------------------------------------------------------------------------
| Student Protected Routes
|--------------------------------------------------------------------------
*/
// Route::prefix('students')->middleware(['auth:sanctum', 'auth:student'])->group(function () {
//     // Route::get('/courses', [StudentController::class, 'fetchCourses']);
// });

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

    Route::middleware('auth:staff')->group(function () {
        // Logout
        Route::post('/logout', [StaffController::class, 'logout']);
    });

});

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
Route::prefix('admin')->middleware(['auth:sanctum', 'auth:staff', 'staff.role:admin'])->group(function () {
    // Course Management
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/update/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/destroy/{id}', [CourseController::class, 'destroy']);
    Route::post('/courses/restore/{id}', [CourseController::class, 'restore']);

    // Subject Management
    Route::get('/subjects/all', [SubjectController::class, 'allSubjects']); // View all subjects (including inactive)
    Route::post('/subjects', [SubjectController::class, 'store']); // Create new subject
    Route::put('/subjects/update/{id}', [SubjectController::class, 'update']); // Update subject
    Route::delete('/subjects/destroy/{id}', [SubjectController::class, 'destroy']); // Soft delete subject
    Route::post('/subjects/restore/{id}', [SubjectController::class, 'restore']); // Restore soft-deleted subject
});




