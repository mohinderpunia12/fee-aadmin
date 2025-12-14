<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('grade')->nullable();
            $table->string('section')->nullable();
            $table->integer('capacity')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'grade', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
