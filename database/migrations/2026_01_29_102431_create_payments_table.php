<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            /**
             * WHO PAID
             */
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')
                ->references('id')
                ->on('students');

            /**
             * WHAT WAS PAID FOR
             */
            $table->unsignedBigInteger('course_enrollment_id');
            $table->foreign('course_enrollment_id')
                ->references('id')
                ->on('courses_enrollments');

            /**
             * PAYMENT DETAILS
             */
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('NGN');

            /**
             * PAYMENT GATEWAY INFO
             */
            $table->enum('payment_method', [
                'card',
                'bank_transfer',
                'ussd',
                'wallet',
                'manual'
            ]);

            $table->string('gateway')->nullable(); // paystack, flutterwave, stripe
            $table->string('gateway_reference')->nullable()->unique();

            /**
             * PAYMENT STATUS
             */
            $table->enum('status', [
                'pending',
                'successful',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');

            /**
             * BILLING CONTEXT
             */
            $table->enum('billing_cycle', [
                'monthly',
                'quarterly',
                'semi-annual',
                'annual'
            ]);

            /**
             * METADATA (gateway payloads, receipts, etc.)
             */
            $table->json('meta')->nullable();

            /**
             * PAYMENT TIMESTAMP
             */
            $table->timestamp('paid_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            /* INDEXES */
            $table->index('student_id');
            $table->index('course_enrollment_id');
            $table->index('status');
            $table->index('gateway_reference');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
