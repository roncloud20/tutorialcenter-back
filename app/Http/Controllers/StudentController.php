<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\EmailVerificationService;
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
                // $this->sendEmailVerification($student); // must throw on failure
                app(EmailVerificationService::class)->send($student);
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


    protected function sendPhoneOtp(string $tel): void
    {
        DB::beginTransaction();

        try {
            // 1. Delete any existing OTPs for this phone
            DB::table('phone_otps')
                ->where('tel', $tel)
                ->delete();

            // 2. Generate OTP
            $code = random_int(100000, 999999);

            $message = "Your verification code is {$code}. It expires in 10 minutes.";

            /**
             * 3. Send SMS (SIMULATED)
             * Replace this block when integrating real SMS provider
             */
            $smsSent = true; // simulate success

            // Example real usage later:
            // $smsSent = SmsService::send($tel, $message);

            if (!$smsSent) {
                throw new \Exception('SMS sending failed');
            }

            // 4. Save OTP ONLY if SMS was sent
            DB::table('phone_otps')->insert([
                'tel' => $tel,
                'code' => Hash::make($code),
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // TEMP: log instead of sending SMS
            logger()->info("OTP for {$tel} is {$code}");

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e; // Let controller decide response
        }
    }

    /**
     * Summary of verifyEmail
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     **/

    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            $record = EmailVerification::where('token', $request->token)
                ->where('expires_at', '>', now())
                ->first();

            if (!$record) {
                return response()->json([
                    'message' => 'Invalid or expired verification link.',
                ], 400);
            }

            $student = $record->student;

            Student::where('id', $student)->update([
                'email_verified_at' => now(),
            ]);

            $record->delete();

            return response()->json([
                'message' => 'Email verified successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Email verification failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

    }
    /**
     * Summary of verifyPhoneOtp
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     **/
    public function verifyPhoneOtp(Request $request)
    {
        try {
            $request->validate([
                'tel' => 'required|string|exists:students,tel',
                'otp' => 'required|string',
            ]);

            $student = Student::where('tel', $request->tel)->first();

            if ($student->tel_verified_at) {
                return response()->json([
                    'message' => 'Phone number already verified.',
                ], 400);
            }

            $otpRecord = DB::table('phone_otps')->where('tel', $student->tel)->latest()->first();

            if (!$otpRecord) {
                return response()->json([
                    'message' => 'OTP not found.',
                ], 400);
            }

            if (Carbon::parse($otpRecord->expires_at)->isPast()) {
                return response()->json([
                    'message' => 'OTP expired.',
                ], 400);
            }

            // if ($otpRecord->expires_at->isPast()) {
            //     return response()->json([
            //         'message' => 'OTP expired.',
            //     ], 400);
            // }

            if (!Hash::check($request->otp, $otpRecord->code)) {
                return response()->json([
                    'message' => 'Invalid OTP.',
                ], 400);
            }

            DB::transaction(function () use ($student, $otpRecord) {
                $student->update([
                    'tel_verified_at' => now(),
                ]);

                DB::table('phone_otps')->where('tel', $otpRecord->tel)->delete();
            });

            return response()->json([
                'message' => 'Phone number verified successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Phone verification failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Summary of resendPhoneOtp
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     **/
    public function resendPhoneOtp(Request $request)
    {
        try {
            $request->validate([
                'tel' => 'required|string|exists:students,tel',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                // 'errors' => $e->validator->errors(),

            ], 422);
        }

        $student = Student::where('tel', $request->tel)->first();
        if (!$student) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        if ($student->tel_verified_at) {
            return response()->json([
                'message' => 'Phone number already verified.',
            ], 400);
        }

        

        try {
            DB::beginTransaction();

            // $student->sendPhoneOtp();
            $this->sendPhoneOtp($student->tel);
            DB::commit();

            return response()->json([
                'message' => 'OTP sent successfully.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to send OTP. Try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}