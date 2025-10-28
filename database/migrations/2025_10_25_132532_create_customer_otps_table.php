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
        Schema::create('customer_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('otp', 10);
            $table->enum('type', ['email_verification', 'phone_verification', 'password_reset'])->default('email_verification');
            $table->timestamp('expires_at');
            $table->timestamp('created_at');

            // Indexes
            $table->index('customer_id');
            $table->index(['customer_id', 'type']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_otps');
    }
};
