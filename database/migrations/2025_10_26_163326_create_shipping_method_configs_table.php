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
        // Create global shipping method configs table
        Schema::create('shipping_method_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('provider', 50); // jne, jnt, rajaongkir, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create config items table (key-value pairs)
        Schema::create('shipping_method_config_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_config_id')
                ->constrained('shipping_method_configs')
                ->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            // Unique constraint: one key per config
            $table->unique(['shipping_method_config_id', 'key']);
            $table->index('shipping_method_config_id');
        });

        // Add foreign key to shipping_methods table
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->foreignId('shipping_method_config_id')
                ->nullable()
                ->after('provider')
                ->constrained('shipping_method_configs')
                ->onDelete('set null');

            $table->index('shipping_method_config_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key from shipping_methods
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_config_id']);
            $table->dropColumn('shipping_method_config_id');
        });

        // Drop tables
        Schema::dropIfExists('shipping_method_config_items');
        Schema::dropIfExists('shipping_method_configs');
    }
};
