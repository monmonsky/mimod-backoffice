<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");

        // Add new constraint with 'inactive' support
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_status_check
            CHECK (status IN ('active', 'inactive', 'suspended', 'deleted'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");

        // Restore old constraint
        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT users_status_check
            CHECK (status IN ('active', 'suspended', 'deleted'))
        ");
    }
};
