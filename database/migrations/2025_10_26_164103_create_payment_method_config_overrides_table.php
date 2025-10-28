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
        // Create table for method-specific config overrides
        Schema::create('payment_method_config_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->unique(['payment_method_id', 'key']);
            $table->index('payment_method_id');
        });

        // Create table for shipping method config overrides
        Schema::create('shipping_method_config_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')
                ->constrained('shipping_methods')
                ->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->unique(['shipping_method_id', 'key']);
            $table->index('shipping_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_method_config_overrides');
        Schema::dropIfExists('payment_method_config_overrides');
    }
};
