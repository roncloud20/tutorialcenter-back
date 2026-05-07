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
        Schema::create('past_question_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('past_question_id')
                ->constrained('past_questions')
                ->cascadeOnDelete();

            $table->string('label')->nullable();
            // A, B, C, D

            $table->longText('option_text');

            $table->boolean('is_correct')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_question_options');
    }
};
