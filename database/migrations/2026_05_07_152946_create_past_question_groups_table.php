<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('past_question_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_year_id')
                ->constrained('exam_years')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            // Example: Comprehension Passage 1

            $table->longText('content')->nullable();
            // The comprehension passage or shared instruction

            $table->string('type')->default('comprehension');
            // comprehension, instruction, diagram, case_study

            $table->string('image')->nullable();
            // Optional shared image

            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_question_groups');
    }
};
