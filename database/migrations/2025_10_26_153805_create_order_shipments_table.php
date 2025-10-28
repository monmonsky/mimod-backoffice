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
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('shipping_method_id')->constrained('shipping_methods')->onDelete('restrict');
            $table->string('courier_code', 50)->nullable()->comment('jne, jnt, sicepat, pos, etc');
            $table->string('service_code', 50)->nullable()->comment('REG, YES, OKE, EXPRESS, etc');
            $table->string('tracking_number')->nullable();
            $table->string('origin_city_id', 20)->nullable()->comment('RajaOngkir city ID');
            $table->string('destination_city_id', 20)->nullable()->comment('RajaOngkir city ID');
            $table->integer('weight')->comment('Weight in grams');
            $table->decimal('cost', 15, 2)->comment('Shipping cost');
            $table->string('estimated_delivery')->nullable()->comment('Estimated delivery (e.g., 2-3 days)');
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'delivered', 'returned', 'cancelled'])->default('pending');
            $table->jsonb('metadata')->nullable()->comment('Store raw response from shipping provider');
            $table->text('notes')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('shipping_method_id');
            $table->index('tracking_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
