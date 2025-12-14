<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('domain');
            $table->string('support_email')->nullable()->after('logo');
            $table->string('support_phone')->nullable()->after('support_email');
            $table->text('support_address')->nullable()->after('support_phone');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['logo', 'support_email', 'support_phone', 'support_address']);
        });
    }
};
