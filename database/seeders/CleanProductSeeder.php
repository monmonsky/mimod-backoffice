<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder will delete all products, variants, and images
     * to allow fresh product data to be seeded.
     */
    public function run(): void
    {
        $this->command->info('Starting to clean product data...');

        // Disable foreign key checks temporarily
        DB::statement('SET CONSTRAINTS ALL DEFERRED');

        // 1. Delete product variant images
        $variantImagesCount = DB::table('product_variant_images')->count();
        if ($variantImagesCount > 0) {
            DB::table('product_variant_images')->delete();
            $this->command->info("✓ Deleted {$variantImagesCount} product variant images");
        }

        // 2. Delete product images
        $productImagesCount = DB::table('product_images')->count();
        if ($productImagesCount > 0) {
            DB::table('product_images')->delete();
            $this->command->info("✓ Deleted {$productImagesCount} product images");
        }

        // 3. Delete product variants
        $variantsCount = DB::table('product_variants')->count();
        if ($variantsCount > 0) {
            DB::table('product_variants')->delete();
            $this->command->info("✓ Deleted {$variantsCount} product variants");
        }

        // 4. Delete product-category relationships
        $productCategoriesCount = DB::table('product_categories')->count();
        if ($productCategoriesCount > 0) {
            DB::table('product_categories')->delete();
            $this->command->info("✓ Deleted {$productCategoriesCount} product-category relationships");
        }

        // 5. Delete products
        $productsCount = DB::table('products')->count();
        if ($productsCount > 0) {
            DB::table('products')->delete();
            $this->command->info("✓ Deleted {$productsCount} products");
        }

        // Reset sequences/auto-increment
        DB::statement("ALTER SEQUENCE product_variant_images_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE product_images_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE product_variants_id_seq RESTART WITH 1");
        DB::statement("ALTER SEQUENCE products_id_seq RESTART WITH 1");

        $this->command->info('✓ Reset all auto-increment sequences');

        // Re-enable foreign key checks
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Product data cleaned successfully!');
        $this->command->info('========================================');
        $this->command->info('Summary:');
        $this->command->info("- Product Variant Images: {$variantImagesCount} deleted");
        $this->command->info("- Product Images: {$productImagesCount} deleted");
        $this->command->info("- Product Variants: {$variantsCount} deleted");
        $this->command->info("- Product Categories: {$productCategoriesCount} deleted");
        $this->command->info("- Products: {$productsCount} deleted");
        $this->command->info('');
        $this->command->info('You can now run ProductDummySeeder to create fresh data:');
        $this->command->info('php artisan db:seed --class=ProductDummySeeder');
    }
}
