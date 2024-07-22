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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('year_level');
            $table->unsignedBigInteger('school_year_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('student_personal_information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
