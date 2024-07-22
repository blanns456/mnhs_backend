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
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users');
            $table->string('status');
            $table->string('enrollment_status');
            $table->string('school_year');
            $table->date('school_start');
            $table->date('school_end');
            $table->date('enrollment_start');
            $table->date('enrollment_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};
