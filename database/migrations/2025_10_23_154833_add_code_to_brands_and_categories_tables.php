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
        // Add code column to brands table
        Schema::table('brands', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->unique()->after('slug');
        });

        // Add code column to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->unique()->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
