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
        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            // Note: status and created_at indexes already exist
            // Composite index for email lookup (frequently used in auth)
            if (!Schema::hasIndex('users', 'idx_users_email_status')) {
                $table->index(['email', 'status'], 'idx_users_email_status');
            }
        });

        // Add indexes for user_roles table
        Schema::table('user_roles', function (Blueprint $table) {
            // Composite index for user role lookup (most common query pattern)
            $table->index(['user_id', 'is_active'], 'idx_user_roles_user_active');

            // Index for role_id (for role-based queries)
            $table->index('role_id', 'idx_user_roles_role_id');

            // Composite index for active role lookup with expiry
            $table->index(['user_id', 'is_active', 'expires_at'], 'idx_user_roles_active_expiry');
        });

        // Add indexes for role_permissions table
        Schema::table('role_permissions', function (Blueprint $table) {
            // Composite index for permission lookup by role
            $table->index(['role_id', 'permission_id'], 'idx_role_perms_role_perm');
        });

        // Add indexes for roles table
        Schema::table('roles', function (Blueprint $table) {
            // Index for active status
            $table->index('is_active', 'idx_roles_is_active');

            // Unique index for name (if not already exists)
            if (!Schema::hasColumn('roles', 'name')) {
                $table->unique('name', 'idx_roles_name_unique');
            }
        });

        // Add indexes for permissions table
        Schema::table('permissions', function (Blueprint $table) {
            // Index for module-based lookups
            $table->index('module', 'idx_permissions_module');

            // Composite index for permission lookup
            $table->index(['module', 'action'], 'idx_permissions_module_action');
        });

        // Add indexes for personal_access_tokens table
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Index for token lookup (most critical for auth)
            $table->index('token', 'idx_tokens_token');

            // Composite index for user tokens lookup
            $table->index(['tokenable_type', 'tokenable_id'], 'idx_tokens_tokenable');

            // Index for expired tokens cleanup
            $table->index('expires_at', 'idx_tokens_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'idx_users_email_status')) {
                $table->dropIndex('idx_users_email_status');
            }
        });

        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropIndex('idx_user_roles_user_active');
            $table->dropIndex('idx_user_roles_role_id');
            $table->dropIndex('idx_user_roles_active_expiry');
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropIndex('idx_role_perms_role_perm');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('idx_roles_is_active');
            if (Schema::hasColumn('roles', 'name')) {
                $table->dropUnique('idx_roles_name_unique');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex('idx_permissions_module');
            $table->dropIndex('idx_permissions_module_action');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex('idx_tokens_token');
            $table->dropIndex('idx_tokens_tokenable');
            $table->dropIndex('idx_tokens_expires_at');
        });
    }
};
