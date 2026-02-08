<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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
                'billing_cycle' => 'required|in:monthly,quarterly,semi-annual,annual',
                'gateway' => 'nullable|string',
                'status' => 'required|in:pending,successful,failed,cancelled,refunded',
                'gateway_reference' => 'nullable|string|unique:payments,gateway_reference',
                'meta' => 'nullable|array',
                'paid_at' => 'nullable'
            ]);

            $payment = Payment::create($validated);

            return response()->json([
                'message' => 'Payment created successfully.',
                'data' => $payment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Public: Initiate a payment 
    public function initiate(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_enrollment_id' => 'required|exists:courses_enrollments,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:card,bank_transfer,ussd,wallet,manual',
                'billing_cycle' => 'required|in:monthly,quarterly,semi-annual,annual',
                'gateway' => 'nullable|string',
            ]);

            $payment = Payment::create([
                'student_id' => $validated['student_id'],
                'course_enrollment_id' => $validated['course_enrollment_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'billing_cycle' => $validated['billing_cycle'],
                'gateway' => $validated['gateway'] ?? null,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Payment initialized successfully.',
                'data' => $payment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initiate payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Public: Confirm a payment
    public function confirm(Request $request)
    {
        try {
            $validated = $request->validate([
                'gateway_reference' => 'required|string|unique:payments,gateway_reference',
                'payment_id' => 'required|exists:payments,id',
                'meta' => 'nullable|array',
            ]);

            $payment = Payment::where('id', $validated['payment_id'])
                ->where('status', 'pending')
                ->firstOrFail();

            $payment->update([
                'gateway_reference' => $validated['gateway_reference'],
                'status' => 'successful',
                'meta' => $validated['meta'] ?? null,
                'paid_at' => now(),
            ]);

            return response()->json([
                'message' => 'Payment confirmed successfully.',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to confirm payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Public: Mark a payment as failed
    public function markFailed(int $paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            $payment->update([
                'status' => 'failed',
            ]);

            return response()->json([
                'message' => 'Payment marked as failed.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark payment as failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Public: Cancel a payment
    public function cancel(int $paymentId)
    {
        try {

            $payment = Payment::findOrFail($paymentId);

            $payment->update([
                'status' => 'cancelled',
            ]);

            return response()->json([
                'message' => 'Payment cancelled successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Admin: Refund a payment
    public function refund(int $paymentId)
    {
        try {
            $payment = Payment::where('status', 'successful')
                ->findOrFail($paymentId);

            $payment->update([
                'status' => 'refunded',
            ]);

            return response()->json([
                'message' => 'Payment refunded successfully.',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to refund payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Student: View my payments
    public function myPayments()
    {
        try {

            $payments = Payment::where('student_id', auth()->id())
                ->latest()
                ->get();

            return response()->json([
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Student: View payments for a specific enrollment
    public function paymentsByEnrollment(int $courseEnrollmentId)
    {
        try {
            $payments = Payment::where('course_enrollment_id', $courseEnrollmentId)
                ->where('student_id', auth()->id())
                ->get();

            return response()->json([
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments for the enrollment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Admin: List all payments with filters
    public function index(Request $request)
    {
        try {
            $payments = Payment::query()
                ->when(
                    $request->status,
                    fn($q) =>
                    $q->where('status', $request->status)
                )
                ->when(
                    $request->student_id,
                    fn($q) =>
                    $q->where('student_id', $request->student_id)
                )
                ->latest()
                ->paginate(20);

            return response()->json($payments);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
