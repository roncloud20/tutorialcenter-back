<?php

namespace App\Http\Controllers;

use App\Models\CoursesEnrollment;
use App\Models\Payment;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Public: Store a new payment
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_enrollment_id' => 'required|exists:courses_enrollments,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:card,bank_transfer,ussd,wallet,manual',
                'billing_cycle' => 'required|in:monthly,quarterly,semi_annual,annual',
                'gateway' => 'nullable|string',
                'status' => 'required|in:pending,successful,failed,cancelled,refunded',
                'gateway_reference' => 'nullable|string|unique:payments,gateway_reference',
                'meta' => 'nullable|array',
                'paid_at' => 'nullable|date',
            ]);

            $payment = Payment::create($validated);

            CoursesEnrollment::where('id', $validated['course_enrollment_id'])
                ->update(['status' => 'active']);

            return response()->json([
                'message' => 'Payment created successfully.',
                'payment' => $payment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Student: View my payments
    public function myPayments(Request $request)
    {
        try {
            $studentId = $request->user()->id;
            $payments = Payment::with(['enrollment' => function ($query) {
                $query->withTrashed()->with('course');
            }])->where('student_id', $studentId)->latest()->get();
            $paymentsData = $payments->map(function ($payment) {
                return [
                    ...$payment->toArray(),
                    'course_title' => optional($payment->enrollment?->course)->title ?? null,
                ];
            });
            return response()->json([
                'payments' => $paymentsData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Admin: View all payments with filters
    public function index(Request $request)
    {
        try {
            $query = Payment::with(['student', 'enrollment.course']);

            if ($request->filled('student_id')) {
                $query->where('student_id', $request->student_id);
            }

            if ($request->filled('course_id')) {
                $query->whereHas('enrollment', function ($q) use ($request) {
                    $q->where('course_id', $request->course_id);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $payments = $query->latest()->paginate(20);
            return response()->json([
                'payments' => $payments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
