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
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('enrollment_no')->unique();
            $table->string('class');
            $table->string('section')->nullable();
            $table->string('parent_phone')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'enrollment_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
