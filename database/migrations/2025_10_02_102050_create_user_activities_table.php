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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action', 100); // login, logout, create, update, delete, view, export, etc.
            $table->string('subject_type', 100)->nullable(); // Model name (User, Role, Product, etc.)
            $table->unsignedBigInteger('subject_id')->nullable(); // ID of the subject
            $table->text('description')->nullable(); // Human readable description
            $table->json('properties')->nullable(); // Additional data (old values, new values, etc.)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('action');
            $table->index('subject_type');
            $table->index('created_at');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
