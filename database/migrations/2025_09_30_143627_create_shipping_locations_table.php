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
        Schema::create('shipping_locations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['province', 'city'])->index();
            $table->unsignedInteger('rajaongkir_id')->comment('ID from RajaOngkir API');
            $table->string('name');
            $table->unsignedBigInteger('province_id')->nullable()->comment('For cities: reference to province');
            $table->string('type_name')->nullable()->comment('For cities: kabupaten or kota');
            $table->string('postal_code')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->unique(['type', 'rajaongkir_id']);
            $table->index('province_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_locations');
    }
};
