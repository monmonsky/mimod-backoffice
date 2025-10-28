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
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Segment name');
            $table->string('code', 50)->unique()->comment('Segment code');
            $table->text('description')->nullable()->comment('Segment description');
            $table->string('color', 20)->nullable()->comment('Color code for UI');

            // Segment Criteria
            $table->integer('min_orders')->nullable()->comment('Minimum orders required');
            $table->integer('max_orders')->nullable()->comment('Maximum orders');
            $table->decimal('min_spent', 15, 2)->nullable()->comment('Minimum total spent');
            $table->decimal('max_spent', 15, 2)->nullable()->comment('Maximum total spent');
            $table->integer('min_loyalty_points')->nullable()->comment('Minimum loyalty points');
            $table->integer('days_since_last_order')->nullable()->comment('Days since last order (for inactive segments)');

            // Additional Criteria (JSON)
            $table->json('custom_criteria')->nullable()->comment('Custom segmentation criteria');

            // Settings
            $table->boolean('is_active')->default(true)->comment('Segment active status');
            $table->boolean('is_auto_assign')->default(false)->comment('Auto-assign customers based on criteria');
            $table->integer('customer_count')->default(0)->comment('Number of customers in this segment');

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
    }
};
