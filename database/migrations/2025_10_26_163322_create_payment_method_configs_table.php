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
        // Create global payment method configs table
        Schema::create('payment_method_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('provider', 50); // midtrans, xendit, manual, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create config items table (key-value pairs)
        Schema::create('payment_method_config_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_config_id')
                ->constrained('payment_method_configs')
                ->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            // Unique constraint: one key per config
            $table->unique(['payment_method_config_id', 'key']);
            $table->index('payment_method_config_id');
        });

        // Add foreign key to payment_methods table
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreignId('payment_method_config_id')
                ->nullable()
                ->after('provider')
                ->constrained('payment_method_configs')
                ->onDelete('set null');

            $table->index('payment_method_config_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key from payment_methods
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropForeign(['payment_method_config_id']);
            $table->dropColumn('payment_method_config_id');
        });

        // Drop tables
        Schema::dropIfExists('payment_method_config_items');
        Schema::dropIfExists('payment_method_configs');
    }
};
