<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StudentEmailVerification;

class StudentController extends Controller
{
    /**
     * Summary of store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
    **/
    

public function store(Request $request)
{
    // 1. Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'nullable|email|unique:students,email|required_without:tel',
        'tel' => [
            'nullable',
            'string',
            'unique:students,tel',
            'required_without:email',
            'regex:/^(\+234|234|0)(70|80|81|90|91)\d{8}$/',
        ],
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*#?&]/',
        ],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {
        // 2. Create student (NOT committed yet)
        $student = Student::create([
            'email' => $request->email,
            'tel' => $request->tel,
            'password' => Hash::make($request->password),
        ]);

        // 3. Verification logic (must succeed)
        if ($student->email) {
            $this->sendEmailVerification($student); // must throw on failure
        }

        if ($student->tel) {
            $this->sendPhoneOtp($student->tel); // must throw on failure
        }

        // 4. All good → commit
        DB::commit();

        return response()->json([
            'message' => 'Registration successful. Verification required.',
            'student' => $student,
        ], 201);

    } catch (\Throwable $e) {
        // 5. Something failed → rollback
        DB::rollBack();

        return response()->json([
            'message' => 'Registration failed. Verification could not be sent.',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}


    /**
     * Summary of sendEmailVerification
     * @param Student $student
     * @return void
    **/
    protected function sendEmailVerification(Student $student)
    {
        $token = Str::uuid();

        // Store token (recommended in a table, simplified for now)
        cache()->put(
            'email_verification_' . $token,
            $student->id,
            now()->addMinutes(30)
        );

        $student->notify(new StudentEmailVerification($token));
    }

    /**
     * Summary of sendPhoneOtp
     * @param string $tel
     * @return void
    **/
    protected function sendPhoneOtp(string $tel)
    {
        $code = rand(100000, 999999);

        DB::table('phone_otps')->insert([
            'tel' => $tel,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // TEMP: log instead of sending SMS
        logger()->info("OTP for {$tel} is {$code}");

        // Later: integrate Termii, Twilio, Africa's Talking
    }


}