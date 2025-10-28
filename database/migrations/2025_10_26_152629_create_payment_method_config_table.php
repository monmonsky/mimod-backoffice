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
        Schema::create('payment_method_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->string('key', 100)->comment('Config key: server_key, client_key, merchant_id, account_number, etc');
            $table->text('value')->comment('Config value (will be encrypted for sensitive data)');
            $table->boolean('is_encrypted')->default(false)->comment('Whether the value is encrypted');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('payment_method_id');
            $table->unique(['payment_method_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_config');
    }
};
