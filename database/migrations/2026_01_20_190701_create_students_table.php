<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration class for creating the students table
return new class extends Migration
{
    /**
     * Run the migrations.
     * This method creates the students table with all necessary columns
     */
    public function up(): void
    {
        // Create the students table with specified columns and constraints
        Schema::create('students', function (Blueprint $table) {
            // Primary key ID column (auto-incrementing)
            $table->id();

            // Basic student information
            $table->string('first_name')->nullable();
            $table->string('surname')->nullable();
            
            // Contact information (with unique constraints)
            $table->string('email')->unique();
            $table->string('tel')->unique();
            
            // Authentication field
            $table->string('password');
            
            // Demographics
            $table->enum('gender', ['male', 'female', 'other'])->default('female');
            
            // Media and personal details
            $table->string('profile_picture')->nullable();
            $table->date('date_of_birth');
            
            // Verification timestamps
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('tel_verified_at')->nullable();
            
            // Location information
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            
            // Academic information
            $table->string('department')->nullable();
            
            // Additional information stored as JSON (for guardian contacts)
            $table->json('guardians')->nullable();
            
            // Soft delete timestamp (for logical deletion)
            $table->datetime('deleted_at')->nullable();
            
            // Record timestamps (created_at and updated_at)
            $table->datetime('created_at');
            $table->datetime('updated_at');

            // Add soft deletes functionality (enables logical deletion)
            $table->softDeletes();
            
            // Add automatically managed timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * This method drops the students table when rolling back the migration
     */
    public function down(): void
    {
        // Drop the students table if it exists
        Schema::dropIfExists('students');
    }
};
