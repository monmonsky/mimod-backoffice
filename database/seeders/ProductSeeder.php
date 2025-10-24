<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding products...');

        DB::table('products')->delete();

        // Get brand
        $brandId = DB::table('brands')->where('slug', 'minimoda')->value('id');

        if (!$brandId) {
            $this->command->error('Brand Minimoda not found. Please run BrandSeeder first.');
            return;
        }

        // Products data from image
        $products = [
            ['name' => 'Breezy Oversized | Oversized Kemeja Cowo', 'price' => 158470, 'categories' => ['kemeja']],
            ['name' => 'Breezy Puffy Top || top anak Perempuan Baju anak | gamis anak perempuan', 'price' => 136500, 'categories' => ['kemeja-blouse']],
            ['name' => 'Breezy Long Pants | Celana anak | Baju Anak', 'price' => 189000, 'categories' => ['celana-panjang']],
            ['name' => 'Sienna Haya Bold Seri | Gamis Anak Perempuan | Baju Anak Perempuan', 'price' => 173400, 'categories' => ['pakaian-muslim-anak-perempuan']],
            ['name' => '- Mini Zip polo | t-shirt anak laki-laki premium', 'price' => 156000, 'categories' => ['kaos-polo']],
            ['name' => 'I-shirt Mini & Moda Tee || Kaos Premium Anak || Laki laki', 'price' => 126000, 'categories' => ['kaos']],
            ['name' => 'Luna Raya | Gamis kids | gamis Anak perempuan', 'price' => 191400, 'categories' => ['pakaian-muslim-anak-perempuan']],
            ['name' => 'Breezy Skort || Rok celana Anak | Skort anak', 'price' => 167750, 'categories' => ['rok']],
            ['name' => 'Minimoda Breezy Dress | Dress Anak Perempuan | Gamis anak', 'price' => 179550, 'compare_at_price' => 315000, 'categories' => ['dress'], 'has_color_variants' => true],
            ['name' => 'Breezy Short Boys || Celana Pendek Anak laki-laki', 'price' => 183000, 'categories' => ['celana-pendek']],
            ['name' => 'Sienna Raya - Soft Seri | Gamis anak perempuan | Baju Muslim', 'price' => 173400, 'categories' => ['pakaian-muslim-anak-perempuan']],
            ['name' => '- Mini Turtleneck | Longsleeve Zipper knit Anak', 'price' => 176025, 'categories' => ['outerwear']],
            ['name' => 'Addition Box  free card', 'price' => 20000, 'categories' => ['kotak-kado'], 'no_variants' => true],
            ['name' => 'Breezy Denim Long Pants | Celana Panjang Anak', 'price' => 167750, 'categories' => ['celana-panjang']],
            ['name' => '- Mini O Sweater | sweater knit premium anak', 'price' => 164000, 'categories' => ['outerwear']],
            ['name' => '- Mini Sleeveless | Vest knit anak perempuan | Rompi Anak', 'price' => 136500, 'categories' => ['outerwear']],
            ['name' => 'Twist Champ Polo | Polo Knit anak Laki laki', 'price' => 179320, 'categories' => ['kaos-polo']],
            ['name' => 'Breezy Jumpsuit anak perempuan | Overall', 'price' => 195000, 'categories' => ['romper-jumpsuit-overall']],
            ['name' => 'Breezy Top Girl || Top Anak Premium || Baju Anak', 'price' => 159250, 'categories' => ['kemeja-blouse']],
            ['name' => 'Twisty Belle Blouse | Blouse Knit | Top Anak perempuan', 'price' => 179330, 'categories' => ['kemeja-blouse']],
        ];

        $createdCount = 0;

        foreach ($products as $productData) {
            // Extract short name (before | or ||)
            $fullName = $productData['name'];
            $shortName = trim(preg_split('/\|{1,2}/', $fullName)[0]);

            $slug = Str::slug($shortName);

            // Create product
            $productId = DB::table('products')->insertGetId([
                'brand_id' => $brandId,
                'name' => $shortName,
                'slug' => $slug,
                'description' => $fullName,
                'age_min' => 5,
                'age_max' => 12,
                'tags' => json_encode(['minimoda', 'anak']),
                'status' => 'active',
                'is_featured' => false,
                
                'updated_at' => now(),
            ]);

            // Attach categories
            foreach ($productData['categories'] as $categorySlug) {
                $categoryId = DB::table('categories')->where('slug', $categorySlug)->value('id');
                if ($categoryId) {
                    DB::table('product_categories')->insert([
                        'product_id' => $productId,
                        'category_id' => $categoryId,
                        
                    ]);
                }
            }

            // Create variants for sizes: 5-6, 7-8, 9-10, 11-12
            if (empty($productData['no_variants'])) {
                $sizes = [
                    ['name' => '5-6 tahun', 'code' => '5-6'],
                    ['name' => '7-8 tahun', 'code' => '7-8'],
                    ['name' => '9-10 tahun', 'code' => '9-10'],
                    ['name' => '11-12 tahun', 'code' => '11-12'],
                ];

                $stockPerVariant = 10;

                // Check if has color variants
                if (!empty($productData['has_color_variants'])) {
                    $colors = [
                        ['name' => 'Bright Pink', 'code' => 'bright-pink'],
                        ['name' => 'Merah Muda', 'code' => 'merah-muda'],
                        ['name' => 'Putih', 'code' => 'putih'],
                    ];

                    foreach ($colors as $color) {
                        foreach ($sizes as $size) {
                            DB::table('product_variants')->insert([
                                'product_id' => $productId,
                                'sku' => 'MM-' . strtoupper(Str::random(6)) . '-' . $color['code'] . '-' . $size['code'],
                                'barcode' => '899' . str_pad(rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                                'size' => $size['name'],
                                'color' => $color['name'],
                                'weight_gram' => 200,
                                'price' => $productData['price'],
                                'compare_at_price' => $productData['compare_at_price'] ?? null,
                                'stock_quantity' => $stockPerVariant,

                                'updated_at' => now(),
                            ]);
                        }
                    }
                } else {
                    foreach ($sizes as $size) {
                        DB::table('product_variants')->insert([
                            'product_id' => $productId,
                            'sku' => 'MM-' . strtoupper(Str::random(6)) . '-' . $size['code'],
                            'barcode' => '899' . str_pad(rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                            'size' => $size['name'],
                            'color' => null,
                            'weight_gram' => 200,
                            'price' => $productData['price'],
                            'compare_at_price' => $productData['compare_at_price'] ?? null,
                            'stock_quantity' => $stockPerVariant,

                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                // No variant, create single variant
                DB::table('product_variants')->insert([
                    'product_id' => $productId,
                    'sku' => 'MM-' . strtoupper(Str::random(8)),
                    'barcode' => '899' . str_pad(rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                    'size' => 'One Size',
                    'color' => null,
                    'weight_gram' => 50,
                    'price' => $productData['price'],
                    'compare_at_price' => null,
                    'stock_quantity' => 100,
                    
                    'updated_at' => now(),
                ]);
            }

            $createdCount++;
        }

        $this->command->info("✓ Seeded {$createdCount} products with variants");
    }
}
