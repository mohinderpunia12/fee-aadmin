<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('pricing_tier_1', 10, 2)->nullable();
            $table->decimal('pricing_tier_2', 10, 2)->nullable();
            $table->integer('trial_days')->default(7);
            $table->string('payment_qr_code')->nullable();
            $table->string('payment_upi_id')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('tutorial_video_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
