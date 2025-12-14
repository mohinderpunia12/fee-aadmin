<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dateTime('trial_ends_at')->nullable()->after('domain');
            $table->enum('subscription_status', ['trial', 'active', 'expired', 'cancelled'])->default('trial')->after('trial_ends_at');
            $table->dateTime('subscription_expires_at')->nullable()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'subscription_status', 'subscription_expires_at']);
        });
    }
};
