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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('unique identifier: bank_transfer_bca, midtrans_bca_va, midtrans_gopay, etc');
            $table->string('name', 200)->comment('Display name: BCA Virtual Account, GoPay, etc');
            $table->string('type', 50)->comment('bank_transfer, e_wallet, qris, cod, credit_card, etc');
            $table->string('provider', 50)->nullable()->comment('manual, midtrans, xendit, null');
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable()->comment('Payment instructions for customer (HTML supported)');
            $table->decimal('fee_percentage', 5, 2)->default(0)->comment('Fee in percentage (e.g., 2.5 for 2.5%)');
            $table->decimal('fee_fixed', 15, 2)->default(0)->comment('Fixed fee amount');
            $table->decimal('min_amount', 15, 2)->nullable()->comment('Minimum transaction amount');
            $table->decimal('max_amount', 15, 2)->nullable()->comment('Maximum transaction amount');
            $table->integer('expired_duration')->default(1440)->comment('Payment expiration in minutes (default 24 hours)');
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
        Schema::dropIfExists('payment_methods');
    }
};
