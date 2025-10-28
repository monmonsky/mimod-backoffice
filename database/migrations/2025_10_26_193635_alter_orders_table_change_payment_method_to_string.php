<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL requires dropping the constraint and recreating the column
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_method_check');

        // Change payment_method from ENUM to VARCHAR
        DB::statement('ALTER TABLE orders ALTER COLUMN payment_method TYPE VARCHAR(200)');

        // Also change courier to VARCHAR if needed (it's already string but let's ensure it's long enough)
        DB::statement('ALTER TABLE orders ALTER COLUMN courier TYPE VARCHAR(200)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM (this is destructive, data may be lost)
        DB::statement("ALTER TABLE orders ALTER COLUMN payment_method TYPE VARCHAR(50)");
        DB::statement("
            ALTER TABLE orders
            ADD CONSTRAINT orders_payment_method_check
            CHECK (payment_method IN ('bank_transfer', 'credit_card', 'e_wallet', 'cod'))
        ");

        DB::statement('ALTER TABLE orders ALTER COLUMN courier TYPE VARCHAR(50)');
    }
};
