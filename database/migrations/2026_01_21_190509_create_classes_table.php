<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Schema::create('classes', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('subject');
        //     $table->foreign('subject')->references('id')->on('subjects');
        //     $table->string('title');
        //     $table->text('description');
        //     $table->json('staffs');
        //     $table->foreign('staffs')->references('id')->on('staffs');
        //     $table->enum('status', ["active","inactive"]);
        //     $table->softDeletes()->comment('Use Laravel softDelete module');
        //     $table->timestamps();
        // });


        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('status', ['active', 'inactive'])
                ->default('active')
                ->index();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
