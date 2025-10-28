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
        // Drop old config tables that are no longer used
        // We now use the new structure with global configs
        Schema::dropIfExists('payment_method_config_old');
        Schema::dropIfExists('shipping_method_config_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore old tables, data has been migrated
        // This is a one-way migration
    }
};
