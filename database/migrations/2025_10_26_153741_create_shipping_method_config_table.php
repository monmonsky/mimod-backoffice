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
        Schema::create('shipping_method_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained('shipping_methods')->onDelete('cascade');
            $table->string('key', 100)->comment('Config key: api_key, origin_city_id, origin_province_id, etc');
            $table->text('value')->comment('Config value (will be encrypted for sensitive data)');
            $table->boolean('is_encrypted')->default(false)->comment('Whether the value is encrypted');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('shipping_method_id');
            $table->unique(['shipping_method_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_method_config');
    }
};
