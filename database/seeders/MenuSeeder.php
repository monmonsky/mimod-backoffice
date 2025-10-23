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
        // DB::table('menus')->truncate();

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
            'menu_locations' => json_encode(['header', 'mobile']),
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
            'menu_locations' => json_encode(['header', 'mobile']),
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
                'menu_locations' => json_encode(['header', 'mobile']),
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
            'menu_locations' => json_encode(['header', 'mobile']),
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
            'menu_locations' => json_encode(['header', 'mobile']),
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
                'menu_locations' => json_encode(['header', 'mobile']),
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
            'menu_locations' => json_encode(['header', 'footer', 'mobile']),
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Footer Menus
        $this->seedFooterMenus();

        $this->command->info('Menus seeded successfully!');

        // Show summary
        $totalMenus = DB::table('menus')->count();
        $totalParents = DB::table('menus')->whereNull('parent_id')->count();
        $totalChildren = DB::table('menus')->whereNotNull('parent_id')->count();

        $this->command->info("Summary:");
        $this->command->info("- Total Menus: {$totalMenus}");
        $this->command->info("- Parent Menus: {$totalParents}");
        $this->command->info("- Submenu Items: {$totalChildren}");
    }

    /**
     * Seed footer menus
     */
    private function seedFooterMenus(): void
    {
        // Footer Column 1: Tentang Minimoda
        $footerCol1Id = DB::table('menus')->insertGetId([
            'title' => 'Tentang Minimoda',
            'slug' => 'footer-tentang',
            'url' => '#',
            'link_type' => 'none',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => null,
            'description' => 'Footer column 1',
            'order' => 100,
            'is_clickable' => false,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['footer']),
            'meta' => json_encode(['footer_column' => 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $footerItems1 = [
            ['title' => 'Tentang Kami', 'url' => '/about'],
            ['title' => 'Cara Berbelanja', 'url' => '/how-to-shop'],
            ['title' => 'Kebijakan Privasi', 'url' => '/privacy-policy'],
            ['title' => 'Syarat & Ketentuan', 'url' => '/terms'],
        ];

        foreach ($footerItems1 as $index => $item) {
            DB::table('menus')->insert([
                'title' => $item['title'],
                'slug' => 'footer-' . Str::slug($item['title']),
                'url' => $item['url'],
                'link_type' => 'static',
                'category_id' => null,
                'brand_id' => null,
                'parent_id' => $footerCol1Id,
                'icon' => null,
                'description' => null,
                'order' => $index + 1,
                'is_clickable' => true,
                'is_active' => true,
                'target' => '_self',
                'menu_locations' => json_encode(['footer']),
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Footer Column 2: Layanan
        $footerCol2Id = DB::table('menus')->insertGetId([
            'title' => 'Layanan',
            'slug' => 'footer-layanan',
            'url' => '#',
            'link_type' => 'none',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => null,
            'description' => 'Footer column 2',
            'order' => 101,
            'is_clickable' => false,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['footer']),
            'meta' => json_encode(['footer_column' => 2]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $footerItems2 = [
            ['title' => 'Hubungi Kami', 'url' => '/contact'],
            ['title' => 'FAQ', 'url' => '/faq'],
            ['title' => 'Lacak Pesanan', 'url' => '/track-order'],
            ['title' => 'Retur & Pengembalian', 'url' => '/returns'],
        ];

        foreach ($footerItems2 as $index => $item) {
            DB::table('menus')->insert([
                'title' => $item['title'],
                'slug' => 'footer-' . Str::slug($item['title']),
                'url' => $item['url'],
                'link_type' => 'static',
                'category_id' => null,
                'brand_id' => null,
                'parent_id' => $footerCol2Id,
                'icon' => null,
                'description' => null,
                'order' => $index + 1,
                'is_clickable' => true,
                'is_active' => true,
                'target' => '_self',
                'menu_locations' => json_encode(['footer']),
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Footer Column 3: Ikuti Kami
        $footerCol3Id = DB::table('menus')->insertGetId([
            'title' => 'Ikuti Kami',
            'slug' => 'footer-social',
            'url' => '#',
            'link_type' => 'none',
            'category_id' => null,
            'brand_id' => null,
            'parent_id' => null,
            'icon' => null,
            'description' => 'Footer column 3',
            'order' => 102,
            'is_clickable' => false,
            'is_active' => true,
            'target' => '_self',
            'menu_locations' => json_encode(['footer']),
            'meta' => json_encode(['footer_column' => 3]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $footerItems3 = [
            ['title' => 'Instagram', 'url' => 'https://instagram.com/minimoda', 'icon' => 'instagram'],
            ['title' => 'Facebook', 'url' => 'https://facebook.com/minimoda', 'icon' => 'facebook'],
            ['title' => 'TikTok', 'url' => 'https://tiktok.com/@minimoda', 'icon' => 'tiktok'],
            ['title' => 'WhatsApp', 'url' => 'https://wa.me/628123456789', 'icon' => 'whatsapp'],
        ];

        foreach ($footerItems3 as $index => $item) {
            DB::table('menus')->insert([
                'title' => $item['title'],
                'slug' => 'footer-' . Str::slug($item['title']),
                'url' => $item['url'],
                'link_type' => 'custom',
                'category_id' => null,
                'brand_id' => null,
                'parent_id' => $footerCol3Id,
                'icon' => $item['icon'],
                'description' => null,
                'order' => $index + 1,
                'is_clickable' => true,
                'is_active' => true,
                'target' => '_blank',
                'menu_locations' => json_encode(['footer']),
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
