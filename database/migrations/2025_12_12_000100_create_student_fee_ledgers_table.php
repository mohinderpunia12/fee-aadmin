<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            // Example: 2025-2026
            $table->string('academic_year', 15);

            // Whole year fee (for this academic year)
            $table->decimal('annual_fee_total', 10, 2)->default(0);

            // Previous year carried balance (opening)
            $table->decimal('opening_balance', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['student_id', 'academic_year']);
            $table->index(['school_id', 'academic_year']);
            $table->index(['school_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_ledgers');
    }
};

