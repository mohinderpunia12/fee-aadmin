<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parent_payment_amounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('parent_phone')->index();
            $table->string('parent_name')->nullable();
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['school_id', 'parent_phone']);
            $table->index(['school_id', 'parent_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_payment_amounts');
    }
};
