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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->string('module', 50);
            $table->string('action', 50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('module');
            $table->index(['module', 'action']);
            $table->index('is_active');
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('group_name', 50)->nullable()->comment('Group name for sidebar grouping');
            $table->string('route', 255)->nullable();
            $table->string('permission_name')->nullable();
            $table->string('component', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            // Foreign key
            $table->foreign('parent_id')->references('id')->on('modules')->onDelete('cascade');

            // Indexes
            $table->index('parent_id');
            $table->index('is_active');
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('role_id');
            $table->integer('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint
            $table->unique(['user_id', 'role_id']);
            
            // Indexes
            $table->index('user_id');
            $table->index('role_id');
            $table->index('is_active');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->integer('granted_by')->nullable();
            $table->timestamp('granted_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('granted_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint
            $table->unique(['role_id', 'permission_id']);
            
            // Indexes
            $table->index('role_id');
            $table->index('permission_id');
        });

        Schema::create('role_modules', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->integer('module_id');
            $table->boolean('can_view')->default(true);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_export')->default(false);
            $table->json('custom_permissions')->nullable();
            $table->integer('granted_by')->nullable();
            $table->timestamp('granted_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('granted_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint
            $table->unique(['role_id', 'module_id']);
            
            // Indexes
            $table->index('role_id');
            $table->index('module_id');
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->integer('granted_by')->nullable();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_granted')->default(true);
            $table->text('reason')->nullable();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('granted_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint
            $table->unique(['user_id', 'permission_id']);
            
            // Indexes
            $table->index('user_id');
            $table->index('permission_id');
            $table->index('is_granted');
        });

        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
        });

        Schema::create('permission_group_items', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->integer('permission_id');
            
            // Foreign keys
            $table->foreign('group_id')->references('id')->on('permission_groups')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['group_id', 'permission_id']);
            
            // Indexes
            $table->index('group_id');
            $table->index('permission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('role_modules');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('permission_group_items');
    }
};
