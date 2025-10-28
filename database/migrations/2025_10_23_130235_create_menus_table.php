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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('slug', 100)->unique();
            $table->string('url', 255)->nullable();
            $table->enum('link_type', ['static', 'category', 'brand', 'page', 'custom', 'none'])->default('static');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('icon', 50)->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_clickable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->jsonb('menu_locations')->nullable(); // ['header', 'footer', 'sidebar', 'mobile']
            $table->jsonb('meta')->nullable(); // For flexible additional data
            $table->timestamps();

            // Indexes
            $table->index('parent_id');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('is_active');
            $table->index('order');

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
