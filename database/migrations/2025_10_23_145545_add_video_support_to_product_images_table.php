<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add video support to product_images table
     */
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // Add media_type column to differentiate between image and video
            $table->enum('media_type', ['image', 'video'])
                ->default('image')
                ->after('url')
                ->comment('Type of media: image or video');

            // Add thumbnail_url for video preview image
            $table->string('thumbnail_url', 500)
                ->nullable()
                ->after('media_type')
                ->comment('Thumbnail image URL for video preview');

            // Add duration for video length in seconds
            $table->integer('duration')
                ->nullable()
                ->after('thumbnail_url')
                ->comment('Video duration in seconds');

            // Add file_size in bytes
            $table->bigInteger('file_size')
                ->nullable()
                ->after('duration')
                ->comment('File size in bytes');
        });

        // Add same columns to product_variant_images
        Schema::table('product_variant_images', function (Blueprint $table) {
            $table->enum('media_type', ['image', 'video'])
                ->default('image')
                ->after('url')
                ->comment('Type of media: image or video');

            $table->string('thumbnail_url', 500)
                ->nullable()
                ->after('media_type')
                ->comment('Thumbnail image URL for video preview');

            $table->integer('duration')
                ->nullable()
                ->after('thumbnail_url')
                ->comment('Video duration in seconds');

            $table->bigInteger('file_size')
                ->nullable()
                ->after('duration')
                ->comment('File size in bytes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'thumbnail_url', 'duration', 'file_size']);
        });

        Schema::table('product_variant_images', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'thumbnail_url', 'duration', 'file_size']);
        });
    }
};
