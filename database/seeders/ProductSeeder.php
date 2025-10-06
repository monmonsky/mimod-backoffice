<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding kids clothing products...');

        DB::table('products')->truncate();

        // Get necessary IDs
        $cartersBrandId = DB::table('brands')->where('slug', 'carters')->value('id');
        $oshkoshBrandId = DB::table('brands')->where('slug', 'oshkosh-bgosh')->value('id');
        $mothercareId = DB::table('brands')->where('slug', 'mothercare')->value('id');

        // Get category IDs
        $kaosBoysId = DB::table('categories')->where('slug', 'kaos-anak-laki-laki')->value('id');
        $kaosGirlsId = DB::table('categories')->where('slug', 'kaos-anak-perempuan')->value('id');
        $dressId = DB::table('categories')->where('slug', 'dress-rok')->value('id');
        $celanaBoysId = DB::table('categories')->where('slug', 'celana-pendek-anak-laki-laki')->value('id');

        $products = [
            [
                'name' => 'Carter\'s Baby Boy Cotton Bodysuit',
                'slug' => 'carters-baby-boy-cotton-bodysuit',
                'description' => 'Soft cotton bodysuit for baby boys. Perfect for everyday wear.',
                'brand_id' => $cartersBrandId,
                'age_min' => 0,
                'age_max' => 24,
                'tags' => json_encode(['baby', 'bodysuit', 'cotton', 'everyday']),
                'status' => 'active',
                'is_featured' => true,
                'categories' => [$kaosBoysId],
                'variants' => [
                    ['sku' => 'CTR-BB-0-3M-BL', 'size' => '0-3M', 'color' => 'Blue', 'weight_gram' => 80, 'price' => 125000, 'compare_at_price' => 150000, 'stock_quantity' => 50],
                    ['sku' => 'CTR-BB-3-6M-BL', 'size' => '3-6M', 'color' => 'Blue', 'weight_gram' => 90, 'price' => 135000, 'compare_at_price' => 160000, 'stock_quantity' => 45],
                    ['sku' => 'CTR-BB-6-9M-BL', 'size' => '6-9M', 'color' => 'Blue', 'weight_gram' => 100, 'price' => 145000, 'compare_at_price' => 170000, 'stock_quantity' => 40],
                    ['sku' => 'CTR-BB-0-3M-GR', 'size' => '0-3M', 'color' => 'Green', 'weight_gram' => 80, 'price' => 125000, 'stock_quantity' => 30],
                ],
            ],
            [
                'name' => 'OshKosh B\'gosh Girls Denim Dress',
                'slug' => 'oshkosh-girls-denim-dress',
                'description' => 'Classic denim dress with cute print. Comfortable and durable.',
                'brand_id' => $oshkoshBrandId,
                'age_min' => 24,
                'age_max' => 96,
                'tags' => json_encode(['girls', 'dress', 'denim', 'casual']),
                'status' => 'active',
                'is_featured' => true,
                'categories' => [$dressId, $kaosGirlsId],
                'variants' => [
                    ['sku' => 'OSH-GD-2Y-DEN', 'size' => '2Y', 'color' => 'Denim Blue', 'weight_gram' => 200, 'price' => 250000, 'compare_at_price' => 300000, 'stock_quantity' => 25],
                    ['sku' => 'OSH-GD-3Y-DEN', 'size' => '3Y', 'color' => 'Denim Blue', 'weight_gram' => 220, 'price' => 275000, 'compare_at_price' => 325000, 'stock_quantity' => 20],
                    ['sku' => 'OSH-GD-4Y-DEN', 'size' => '4Y', 'color' => 'Denim Blue', 'weight_gram' => 240, 'price' => 300000, 'compare_at_price' => 350000, 'stock_quantity' => 15],
                ],
            ],
            [
                'name' => 'Mothercare Baby Girl Floral Romper',
                'slug' => 'mothercare-baby-girl-floral-romper',
                'description' => 'Adorable floral print romper for baby girls. Soft and breathable fabric.',
                'brand_id' => $mothercareId,
                'age_min' => 0,
                'age_max' => 18,
                'tags' => json_encode(['baby', 'girls', 'romper', 'floral']),
                'status' => 'active',
                'is_featured' => false,
                'categories' => [$kaosGirlsId],
                'variants' => [
                    ['sku' => 'MTH-BG-0-3M-PK', 'size' => '0-3M', 'color' => 'Pink Floral', 'weight_gram' => 85, 'price' => 180000, 'stock_quantity' => 35],
                    ['sku' => 'MTH-BG-3-6M-PK', 'size' => '3-6M', 'color' => 'Pink Floral', 'weight_gram' => 95, 'price' => 195000, 'stock_quantity' => 30],
                    ['sku' => 'MTH-BG-6-12M-PK', 'size' => '6-12M', 'color' => 'Pink Floral', 'weight_gram' => 105, 'price' => 210000, 'stock_quantity' => 8],
                ],
            ],
            [
                'name' => 'Carter\'s Boy Shorts Set - 2 Pack',
                'slug' => 'carters-boy-shorts-set-2-pack',
                'description' => '2-pack cotton shorts for boys. Perfect for summer.',
                'brand_id' => $cartersBrandId,
                'age_min' => 12,
                'age_max' => 60,
                'tags' => json_encode(['boys', 'shorts', 'summer', 'cotton', 'pack']),
                'status' => 'active',
                'is_featured' => false,
                'categories' => [$celanaBoysId],
                'variants' => [
                    ['sku' => 'CTR-BS-12M-NAV', 'size' => '12M', 'color' => 'Navy/Gray', 'weight_gram' => 120, 'price' => 165000, 'stock_quantity' => 40],
                    ['sku' => 'CTR-BS-18M-NAV', 'size' => '18M', 'color' => 'Navy/Gray', 'weight_gram' => 130, 'price' => 175000, 'stock_quantity' => 35],
                    ['sku' => 'CTR-BS-2Y-NAV', 'size' => '2Y', 'color' => 'Navy/Gray', 'weight_gram' => 140, 'price' => 185000, 'stock_quantity' => 30],
                    ['sku' => 'CTR-BS-3Y-NAV', 'size' => '3Y', 'color' => 'Navy/Gray', 'weight_gram' => 150, 'price' => 195000, 'stock_quantity' => 5],
                ],
            ],
            [
                'name' => 'OshKosh Boys Graphic T-Shirt',
                'slug' => 'oshkosh-boys-graphic-tshirt',
                'description' => 'Cool graphic t-shirt for boys. 100% cotton material.',
                'brand_id' => $oshkoshBrandId,
                'age_min' => 24,
                'age_max' => 120,
                'tags' => json_encode(['boys', 'tshirt', 'graphic', 'casual']),
                'status' => 'active',
                'is_featured' => true,
                'categories' => [$kaosBoysId],
                'variants' => [
                    ['sku' => 'OSH-BT-2Y-BL', 'size' => '2Y', 'color' => 'Blue', 'weight_gram' => 100, 'price' => 95000, 'stock_quantity' => 60],
                    ['sku' => 'OSH-BT-3Y-BL', 'size' => '3Y', 'color' => 'Blue', 'weight_gram' => 110, 'price' => 105000, 'stock_quantity' => 55],
                    ['sku' => 'OSH-BT-4Y-BL', 'size' => '4Y', 'color' => 'Blue', 'weight_gram' => 120, 'price' => 115000, 'stock_quantity' => 50],
                    ['sku' => 'OSH-BT-2Y-RD', 'size' => '2Y', 'color' => 'Red', 'weight_gram' => 100, 'price' => 95000, 'stock_quantity' => 45],
                    ['sku' => 'OSH-BT-3Y-RD', 'size' => '3Y', 'color' => 'Red', 'weight_gram' => 110, 'price' => 105000, 'stock_quantity' => 3],
                ],
            ],
        ];

        $userId = DB::table('users')->first()->id ?? 1;

        foreach ($products as $productData) {
            // Extract variants and categories
            $variants = $productData['variants'];
            $categories = $productData['categories'];
            unset($productData['variants'], $productData['categories']);

            // Add timestamps and created_by
            $productData['created_by'] = $userId;
            $productData['created_at'] = now();
            $productData['updated_at'] = now();

            // Insert product
            $productId = DB::table('products')->insertGetId($productData);

            // Insert categories
            foreach ($categories as $categoryId) {
                if ($categoryId) {
                    DB::table('product_categories')->insert([
                        'product_id' => $productId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // Insert variants
            foreach ($variants as $variant) {
                $variant['product_id'] = $productId;
                $variant['created_at'] = now();
                $variant['updated_at'] = now();
                DB::table('product_variants')->insert($variant);
            }

            $this->command->info("  ✓ Created: {$productData['name']} with " . count($variants) . " variants");
        }

        $totalProducts = count($products);
        $totalVariants = array_sum(array_map(fn($p) => count($p['variants']), $products));

        $this->command->info("✓ {$totalProducts} products seeded successfully");
        $this->command->info("  - {$totalVariants} total variants");
        $this->command->info("  - 3 featured products");
        $this->command->info("  - Products linked to categories and brands");
    }
}
