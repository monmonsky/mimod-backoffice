<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('order_items')->delete();
        DB::table('product_variant_images')->delete();
        DB::table('product_variants')->delete();
        DB::table('product_images')->delete();
        DB::table('product_categories')->delete();
        DB::table('products')->delete();
        DB::table('categories')->delete();
        DB::table('brands')->delete();

        // Products for Mini category
        $miniProducts = [
            ['name' => 'Belle Blouse', 'price' => 125000],
            ['name' => 'Breezy Dress', 'price' => 175000],
            ['name' => 'Breezy Puffy Top', 'price' => 135000],
            ['name' => 'Breezy Skort', 'price' => 145000],
            ['name' => 'Breezy Top Girl', 'price' => 115000],
            ['name' => 'Luna Raya', 'price' => 165000],
            ['name' => 'Sienna-Bold Series', 'price' => 185000],
            ['name' => 'Sienna-Soft Series', 'price' => 175000],
        ];

        // Products for Moda category
        $modaProducts = [
            ['name' => 'Breezy Long Sleeve', 'price' => 155000],
            ['name' => 'Breezy Oversized', 'price' => 165000],
            ['name' => 'Breezy Short Boys', 'price' => 135000],
            ['name' => 'Champ Polo', 'price' => 145000],
        ];

        // Sizes and Colors for variants
        $sizes = [
            ['label' => '5-6 Tahun', 'value' => '5-6Y'],
            ['label' => '7-8 Tahun', 'value' => '7-8Y'],
            ['label' => '9-10 Tahun', 'value' => '9-10Y'],
            ['label' => '11-12 Tahun', 'value' => '11-12Y'],
        ];

        $colors = [
            ['name' => 'Cream', 'code' => '#F5F5DC'],
            ['name' => 'Light Blue', 'code' => '#ADD8E6'],
        ];

        $descriptions = [
            'High-quality fabric with comfortable fit for all-day wear',
            'Stylish design perfect for casual and formal occasions',
            'Breathable material ideal for active kids',
            'Premium cotton blend for maximum comfort',
            'Modern design with attention to detail',
            'Durable construction for long-lasting wear',
            'Perfect for school, play, or special events',
            'Easy care and machine washable',
        ];

        // Insert Mini products
        foreach ($miniProducts as $index => $product) {
            $productId = DB::table('products')->insertGetId([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $descriptions[$index % count($descriptions)],
                'brand_id' => $brandId,
                'status' => 'active',
                'age_min' => 5,
                'age_max' => 10,
                'tags' => json_encode(['kids', 'fashion', 'mini', 'girls']),
                'is_featured' => $index < 3, // First 3 products are featured
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Link to Mini category
            DB::table('product_categories')->insert([
                'product_id' => $productId,
                'category_id' => $categoryMini
            ]);

            // Create variants for each size and color combination
            foreach ($sizes as $sizeIndex => $size) {
                foreach ($colors as $colorIndex => $color) {
                    $sku = strtoupper(Str::slug($product['name'])) . '-' . $size['value'] . '-' . strtoupper(substr($color['name'], 0, 2));

                    $variantId = DB::table('product_variants')->insertGetId([
                        'product_id' => $productId,
                        'sku' => $sku,
                        'size' => $size['label'],
                        'color' => $color['name'],
                        'weight_gram' => rand(150, 300),
                        'price' => $product['price'],
                        'compare_at_price' => $product['price'] + rand(20000, 50000),
                        'stock_quantity' => rand(10, 50),
                        'barcode' => '978' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Add variant image (placeholder - in real app would be actual product photos)
                    $imageUrl = 'products/variants/' . Str::slug($product['name']) . '-' . strtolower($color['name']) . '.jpg';
                    DB::table('product_variant_images')->insert([
                        'variant_id' => $variantId,
                        'url' => $imageUrl,
                        'alt_text' => $product['name'] . ' - ' . $color['name'],
                        'is_primary' => true,
                        'sort_order' => 1,
                        'created_at' => now()
                    ]);
                }
            }
        }

        // Insert Moda products
        foreach ($modaProducts as $index => $product) {
            $productId = DB::table('products')->insertGetId([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $descriptions[($index + 4) % count($descriptions)],
                'brand_id' => $brandId,
                'status' => 'active',
                'age_min' => 7,
                'age_max' => 12,
                'tags' => json_encode(['kids', 'fashion', 'moda', strpos($product['name'], 'Boys') !== false ? 'boys' : 'unisex']),
                'is_featured' => $index < 2, // First 2 products are featured
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Link to Moda category
            DB::table('product_categories')->insert([
                'product_id' => $productId,
                'category_id' => $categoryModa
            ]);

            // Create variants for each size and color combination
            foreach ($sizes as $sizeIndex => $size) {
                foreach ($colors as $colorIndex => $color) {
                    $sku = strtoupper(Str::slug($product['name'])) . '-' . $size['value'] . '-' . strtoupper(substr($color['name'], 0, 2));

                    $variantId = DB::table('product_variants')->insertGetId([
                        'product_id' => $productId,
                        'sku' => $sku,
                        'size' => $size['label'],
                        'color' => $color['name'],
                        'weight_gram' => rand(150, 300),
                        'price' => $product['price'],
                        'compare_at_price' => $product['price'] + rand(20000, 50000),
                        'stock_quantity' => rand(10, 50),
                        'barcode' => '978' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Add variant image (placeholder - in real app would be actual product photos)
                    $imageUrl = 'products/variants/' . Str::slug($product['name']) . '-' . strtolower($color['name']) . '.jpg';
                    DB::table('product_variant_images')->insert([
                        'variant_id' => $variantId,
                        'url' => $imageUrl,
                        'alt_text' => $product['name'] . ' - ' . $color['name'],
                        'is_primary' => true,
                        'sort_order' => 1,
                        'created_at' => now()
                    ]);
                }
            }
        }

        $totalVariants = (count($miniProducts) + count($modaProducts)) * count($sizes) * count($colors);

        $this->command->info('Catalog seeded successfully!');
        $this->command->info('- 1 Brand created: Minimoda');
        $this->command->info('- 2 Categories created: Mini, Moda');
        $this->command->info('- ' . (count($miniProducts) + count($modaProducts)) . ' Products created');
        $this->command->info('- ' . $totalVariants . ' Variants created');
        $this->command->info('- ' . $totalVariants . ' Variant Images created (1 per variant)');
    }
}
