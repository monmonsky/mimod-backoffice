<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing categories and brands
        $categories = DB::table('categories')->pluck('id', 'slug')->toArray();
        $brands = DB::table('brands')->pluck('id', 'slug')->toArray();

        $this->command->info('Starting to seed menus...');

        // Clear existing menus (optional - remove if you want to keep existing)
        DB::table('menus')->truncate();

        // 1. Home Menu (Top Level)
        $homeId = DB::table('menus')->insertGetId([
            'title' => 'Home',
            'slug' => 'home',
            'url' => '/',
            'link_type' => 'static',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => 'home',
            'description' => 'Homepage link',
            'order' => 1,
            'is_clickable' => true,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['header']),
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Produk Menu (Top Level - Parent)
        $produkId = DB::table('menus')->insertGetId([
            'title' => 'Produk',
            'slug' => 'produk',
            'url' => '/products',
            'link_type' => 'static',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => null,
            'description' => 'All products page',
            'order' => 2,
            'is_clickable' => true,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['header']),
            'meta' => json_encode(['has_mega_menu' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2a. Produk Submenu - From Categories
        $categoryOrder = 1;
        foreach ($categories as $slug => $categoryId) {
            // Get category name
            $category = DB::table('categories')->where('id', $categoryId)->first();

            DB::table('menus')->insert([
                'title' => $category->name,
                'slug' => 'produk-' . $slug,
                'url' => '/category/' . $slug,
                'link_type' => 'category',
                'category_id' => $categoryId,
                'brand_id' => null,
                'parent_id' => $produkId,
                'icon' => null,
                'description' => 'Category: ' . $category->name,
                'order' => $categoryOrder++,
                'is_clickable' => true,
                'is_active' => true,
                'target' => '_self',
                'menu_locations' => json_encode(['header']),
                'meta' => json_encode(['category_id' => $categoryId]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($categoryOrder > 6) break; // Limit to 6 categories for menu
        }

        // 3. Sale Menu (Top Level)
        $saleId = DB::table('menus')->insertGetId([
            'title' => 'Sale',
            'slug' => 'sale',
            'url' => '/sale',
            'link_type' => 'static',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => 'tag',
            'description' => 'Sale & Promo products',
            'order' => 3,
            'is_clickable' => true,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['header']),
            'meta' => json_encode(['badge' => ['text' => '50% OFF', 'color' => 'red']]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Brand Menu (Top Level - Parent)
        $brandMenuId = DB::table('menus')->insertGetId([
            'title' => 'Brand',
            'slug' => 'brand',
            'url' => '/brands',
            'link_type' => 'static',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => null,
            'description' => 'All brands page',
            'order' => 4,
            'is_clickable' => true,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['header']),
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4a. Brand Submenu - From Brands
        $brandOrder = 1;
        foreach ($brands as $slug => $brandId) {
            // Get brand name
            $brand = DB::table('brands')->where('id', $brandId)->first();

            DB::table('menus')->insert([
                'title' => $brand->name,
                'slug' => 'brand-' . $slug,
                'url' => '/brand/' . $slug,
                'link_type' => 'brand',
                'category_id' => null,
                'brand_id' => $brandId,
                'parent_id' => $brandMenuId,
                'icon' => null,
                'description' => 'Brand: ' . $brand->name,
                'order' => $brandOrder++,
                'is_clickable' => true,
                'is_active' => true,
                'target' => '_self',
                'menu_locations' => json_encode(['header']),
                'meta' => json_encode(['brand_id' => $brandId]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Tentang Kami Menu (Top Level)
        $tentangId = DB::table('menus')->insertGetId([
            'title' => 'Tentang Kami',
            'slug' => 'tentang-kami',
            'url' => '/about',
            'link_type' => 'static',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => null,
            'description' => 'About us page',
            'order' => 5,
            'is_clickable' => true,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['header']),
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Header menus seeded successfully!');

        // Show summary
        $totalMenus = DB::table('menus')->count();
        $totalParents = DB::table('menus')->whereNull('parent_id')->count();
        $totalChildren = DB::table('menus')->whereNotNull('parent_id')->count();

        $this->command->info("Summary:");
        $this->command->info("- Total Header Menus: {$totalMenus}");
        $this->command->info("- Parent Menus: {$totalParents}");
        $this->command->info("- Submenu Items: {$totalChildren}");
    }
}
