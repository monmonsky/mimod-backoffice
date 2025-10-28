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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->string('payment_channel', 100)->nullable()->comment('Specific channel: bca_va, gopay, qris, etc');
            $table->string('transaction_id')->nullable()->unique()->comment('Transaction ID from payment gateway');
            $table->string('reference_id')->nullable()->comment('Additional reference from payment gateway');
            $table->decimal('amount', 15, 2)->comment('Payment amount');
            $table->decimal('fee_amount', 15, 2)->default(0)->comment('Payment gateway fee');
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->string('payment_url')->nullable()->comment('Redirect URL for payment page');
            $table->string('va_number', 50)->nullable()->comment('Virtual Account number');
            $table->text('qr_code')->nullable()->comment('QR code string/URL for QRIS');
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->jsonb('metadata')->nullable()->comment('Store raw response from payment gateway');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('payment_method_id');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('expired_at');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
