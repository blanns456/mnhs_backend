<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_education_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('LRN')->unique();
            $table->string('grade_level');
            $table->string('school_elem');
            $table->string('elem_schoolyr');
            $table->string('school_jhs')->nullable();
            $table->string('jhs_schoolyr')->nullable();
            $table->string('last_school')->nullable();
            $table->string('last_schoolyr')->nullable();
            $table->string('school_id')->nullable();
            $table->string('lastgrade_completed')->nullable();
            $table->string('semester')->nullable();
            $table->string('track')->nullable();
            // $table->string('strand')->nullable();
            $table->string('special_program')->nullable();
            $table->string('m_tounge')->nullable();
            $table->string('status');
            $table->string('account_status');
            $table->string('form_137');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('student_personal_information')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_education_records');
    }
};
