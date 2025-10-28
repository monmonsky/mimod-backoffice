<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cleanup expired tokens first
        DB::statement("DELETE FROM personal_access_tokens WHERE expires_at < NOW()");

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Add composite index for token lookup with expires_at check
            // This optimizes: WHERE token = ? AND (expires_at IS NULL OR expires_at > NOW())
            $table->index(['token', 'expires_at'], 'idx_token_expires_lookup');

            // Add index for last_used_at for cleanup queries
            $table->index(['last_used_at'], 'idx_last_used_at');
        });

        // Note: Run VACUUM manually after migration:
        // php artisan db:statement "VACUUM ANALYZE personal_access_tokens"
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex('idx_token_expires_lookup');
            $table->dropIndex('idx_last_used_at');
        });
    }
};
