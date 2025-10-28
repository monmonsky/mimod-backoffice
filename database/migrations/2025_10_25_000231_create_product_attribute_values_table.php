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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->string('value', 100); // e.g., "5-6 tahun", "Bright Pink", "XL"
            $table->string('slug', 100);
            $table->json('meta')->nullable(); // Can store hex color, image URL, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('product_attribute_id');
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');

            // Unique constraint per attribute
            $table->unique(['product_attribute_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
