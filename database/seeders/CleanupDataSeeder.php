<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting data cleanup...');

        try {
            // Delete in correct order to respect foreign key constraints
            // 1. Clean up Orders and related tables (must be first due to FK to products/users)
            $this->cleanupOrders();

            // 2. Clean up Products and related tables (must be before brands/categories)
            $this->cleanupProducts();

            // 3. Clean up Categories (can be deleted after products)
            $this->cleanupCategories();

            // 4. Clean up Brands (can be deleted after products)
            $this->cleanupBrands();

            $this->command->info('✓ Data cleanup completed successfully!');

        } catch (\Exception $e) {
            $this->command->error('Error during cleanup: ' . $e->getMessage());
            Log::error('Cleanup seeder error: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup orders and related data
     */
    protected function cleanupOrders()
    {
        $this->command->info('Cleaning up orders...');

        $tables = [
            'order_items',
            'order_status_history',
            'orders',
            'cart_items',
            'carts',
        ];

        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                DB::table($table)->delete();
                $this->command->info("  - Deleted {$count} records from {$table}");
            } catch (\Exception $e) {
                $this->command->warn("  - Skipped {$table} (table not found or error)");
            }
        }
    }

    /**
     * Cleanup products and related data
     */
    protected function cleanupProducts()
    {
        $this->command->info('Cleaning up products...');

        // Delete product-related tables in correct order (child tables first)
        $tables = [
            'product_variant_images',
            'product_variant_stock_histories',
            'product_variants',
            'product_images',
            'product_reviews',
            'product_category', // pivot table
            'wishlists',
            'product_views',
        ];

        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                if ($count > 0) {
                    DB::table($table)->delete();
                    $this->command->info("  - Deleted {$count} records from {$table}");
                } else {
                    $this->command->comment("  - No records in {$table}");
                }
            } catch (\Exception $e) {
                $this->command->warn("  - Skipped {$table} (table not found)");
            }
        }

        // Delete products
        $productsCount = DB::table('products')->count();
        DB::table('products')->delete();
        $this->command->info("  - Deleted {$productsCount} products");
    }

    /**
     * Cleanup categories
     */
    protected function cleanupCategories()
    {
        $this->command->info('Cleaning up categories...');

        $categoriesCount = DB::table('categories')->count();
        DB::table('categories')->delete();
        $this->command->info("  - Deleted {$categoriesCount} categories");
    }

    /**
     * Cleanup brands
     */
    protected function cleanupBrands()
    {
        $this->command->info('Cleaning up brands...');

        $brandsCount = DB::table('brands')->count();
        DB::table('brands')->delete();
        $this->command->info("  - Deleted {$brandsCount} brands");
    }
}
