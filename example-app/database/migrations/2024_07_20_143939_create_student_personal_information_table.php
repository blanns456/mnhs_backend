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
        Schema::create('student_personal_information', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middlename')->nullable();
            $table->string('civil_status');
            $table->string('suffix')->nullable();
            $table->integer('age');
            $table->date('birthdate');
            $table->string('birth_place');
            $table->string('email')->unique();
            $table->string('mobile_number');
            $table->string('gender');
            $table->string('ip')->nullable();
            $table->string('pantawid')->nullable();
            $table->string('home_address');
            $table->string('present_address');
            $table->string('profile_image')->nullable();
            $table->text('signature');
            $table->string('father_lastName');
            $table->string('father_firstName');
            $table->string('father_middleName')->nullable();
            $table->string('father_number');
            $table->string('mother_lastName');
            $table->string('mother_firstName');
            $table->string('mother_middleName')->nullable();
            $table->string('mother_number');
            $table->string('guardian_lastName')->nullable();
            $table->string('guardian_firstName')->nullable();
            $table->string('guardian_middleName')->nullable();
            $table->string('guardian_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_personal_information');
    }
};
