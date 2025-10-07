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
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->decimal('points_per_currency', 10, 2)->default(1); // Points earned per 1 currency unit spent
            $table->decimal('currency_per_point', 10, 2)->default(1); // Currency value of 1 point
            $table->integer('min_points_redeem')->default(100); // Minimum points required to redeem
            $table->integer('points_expiry_days')->nullable(); // Points expiry in days (null = no expiry)
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
    }
};
