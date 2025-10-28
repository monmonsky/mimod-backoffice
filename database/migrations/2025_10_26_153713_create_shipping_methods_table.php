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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('unique identifier: jne_reg, jnt_express, rajaongkir_jne, etc');
            $table->string('name', 200)->comment('Display name: JNE REG, JNT Express, etc');
            $table->string('type', 50)->comment('manual, rajaongkir, custom');
            $table->string('provider', 50)->nullable()->comment('jne, jnt, sicepat, pos, gosend, grab, rajaongkir, custom');
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_cost', 15, 2)->default(0)->comment('Base shipping cost for manual type');
            $table->decimal('cost_per_kg', 15, 2)->default(0)->comment('Cost per kg for manual type');
            $table->integer('min_weight')->nullable()->comment('Minimum weight in grams');
            $table->integer('max_weight')->nullable()->comment('Maximum weight in grams');
            $table->string('estimated_delivery')->nullable()->comment('Estimated delivery time (e.g., 2-3 days)');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('provider');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
