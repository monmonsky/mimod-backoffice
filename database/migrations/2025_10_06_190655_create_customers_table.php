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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 50)->unique()->comment('Unique customer code');
            $table->string('name')->comment('Customer full name');
            $table->string('email')->unique()->comment('Customer email');
            $table->string('phone', 20)->nullable()->comment('Customer phone number');
            $table->date('date_of_birth')->nullable()->comment('Date of birth');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Gender');

            // Customer Segmentation
            $table->enum('segment', ['regular', 'premium', 'vip'])->default('regular')->comment('Customer segment');
            $table->boolean('is_vip')->default(false)->comment('VIP status');
            $table->integer('loyalty_points')->default(0)->comment('Loyalty points');

            // Statistics
            $table->integer('total_orders')->default(0)->comment('Total number of orders');
            $table->decimal('total_spent', 15, 2)->default(0)->comment('Total amount spent');
            $table->decimal('average_order_value', 12, 2)->default(0)->comment('Average order value');
            $table->timestamp('last_order_at')->nullable()->comment('Last order date');
            $table->timestamp('last_login_at')->nullable()->comment('Last login timestamp');

            // Account Info
            $table->string('password')->nullable()->comment('Customer password (if has account)');
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->comment('Account status');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');

            // Preferences
            $table->json('preferences')->nullable()->comment('Customer preferences (newsletter, notifications, etc.)');
            $table->text('notes')->nullable()->comment('Admin notes about customer');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('email');
            $table->index('phone');
            $table->index('segment');
            $table->index('is_vip');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
