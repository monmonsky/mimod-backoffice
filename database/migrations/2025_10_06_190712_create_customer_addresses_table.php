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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('Reference to customers table');
            $table->string('label', 50)->comment('Address label (Home, Office, etc.)');
            $table->string('recipient_name')->comment('Recipient name');
            $table->string('phone', 20)->comment('Phone number for this address');
            $table->text('address_line')->comment('Full address');
            $table->string('city', 100)->comment('City');
            $table->string('province', 100)->comment('Province/State');
            $table->string('postal_code', 10)->comment('Postal code');
            $table->string('country', 100)->default('Indonesia')->comment('Country');
            $table->boolean('is_default')->default(false)->comment('Default address flag');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude coordinate');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude coordinate');
            $table->text('notes')->nullable()->comment('Address notes (e.g., delivery instructions)');
            $table->timestamps();

            // Foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // Indexes
            $table->index('customer_id');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
