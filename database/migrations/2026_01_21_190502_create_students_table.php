<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('firstname')->nullable();
            $table->string('surname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('tel')->unique()->nullable();
            $table->string('password');

            $table->enum('gender', ['male', 'female', 'others'])->nullable();
            $table->string('profile_picture')->nullable();
            $table->date('date_of_birth')->nullable();

            // Verification
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('tel_verified_at')->nullable();

            // Location Info
            $table->string('location')->nullable()->comment('Country and state');
            $table->string('address')->nullable()->comment('Full address');

            // Department
            $table->enum('department', ['science','art','commerce'])->nullable()
                  ->comment('Student department');

            // Soft deletes & timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};


// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::disableForeignKeyConstraints();

//         Schema::create('students', function (Blueprint $table) {
//             $table->id();
//             $table->string('firstname')->nullable();
//             $table->string('surname')->nullable();
//             $table->string('email')->unique()->nullable();
//             $table->string('tel')->unique()->nullable();
//             $table->string('password');
//             $table->enum('gender', ["male","female","others"])->nullable();
//             $table->string('profile_picture')->nullable();
//             $table->date('date_of_birth')->nullable();
//             $table->timestamp('email_verified_at')->nullable();
//             $table->timestamp('tel_verified_at')->nullable();
//             $table->string('location')->nullable()->comment('This should be the persons country and state');
//             $table->string('address')->nullable()->comment('This should be the persons full address');
//             $table->string('department')->nullable()->comment('This represents the students department, like science, art, or commerce, and any other that will be added in the future');
//             $table->json('guardians')->index()->nullable();
//             $table->foreign('guardians')->references('id')->on('guardians');
//             $table->softDeletes()->comment('Use Laravel softDelete module');
//             $table->timestamps();
//         });

//         Schema::enableForeignKeyConstraints();
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('students');
//     }
// };
