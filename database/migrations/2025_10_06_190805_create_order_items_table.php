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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('variant_id');

            // Product Information (snapshot at time of order)
            $table->string('product_name');
            $table->string('sku', 100);
            $table->string('size', 50)->nullable();
            $table->string('color', 50)->nullable();

            // Pricing
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2); // Price per unit at time of order
            $table->decimal('subtotal', 12, 2); // quantity * price
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2); // subtotal - discount_amount

            $table->timestamps();

            // Foreign Keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('restrict');

            // Indexes
            $table->index('order_id');
            $table->index('variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
