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
        Schema::create('pass_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('otp');
            $table->timestamp('start');
            $table->timestamp('expire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pass_resets');
    }
};
