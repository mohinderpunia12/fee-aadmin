<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete()->after('school_id');
            $table->string('profile_photo')->nullable()->after('section');
            $table->string('parent_name')->nullable()->after('profile_photo');
            $table->string('parent_phone_secondary')->nullable()->after('parent_phone');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn(['classroom_id', 'profile_photo', 'parent_name', 'parent_phone_secondary']);
        });
    }
};
