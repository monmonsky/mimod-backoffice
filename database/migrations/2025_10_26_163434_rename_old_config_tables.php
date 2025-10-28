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
        // Rename old config tables to keep as backup during migration
        Schema::rename('payment_method_config', 'payment_method_config_old');
        Schema::rename('shipping_method_config', 'shipping_method_config_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original names
        Schema::rename('payment_method_config_old', 'payment_method_config');
        Schema::rename('shipping_method_config_old', 'shipping_method_config');
    }
};
