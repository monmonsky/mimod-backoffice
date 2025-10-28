<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brandIds = DB::table('brands')->pluck('id')->toArray();
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        if (empty($brandIds)) {
            $this->command->error('No brands found. Please create brands first.');
            return;
        }

        if (empty($categoryIds)) {
            $this->command->error('No categories found. Please create categories first.');
            return;
        }

        // Product data templates
        $productTypes = [
            ['type' => 'Kemeja', 'tags' => ['kemeja', 'formal', 'casual'], 'ages' => [3, 12]],
            ['type' => 'Kaos', 'tags' => ['kaos', 'casual', 'santai'], 'ages' => [2, 10]],
            ['type' => 'Dress', 'tags' => ['dress', 'pesta', 'casual'], 'ages' => [3, 12]],
            ['type' => 'Celana Jeans', 'tags' => ['jeans', 'celana', 'casual'], 'ages' => [4, 14]],
            ['type' => 'Romper', 'tags' => ['romper', 'baby', 'cute'], 'ages' => [0, 3]],
            ['type' => 'Jumpsuit', 'tags' => ['jumpsuit', 'fashionable', 'trendy'], 'ages' => [3, 10]],
            ['type' => 'Rok', 'tags' => ['rok', 'skirt', 'cute'], 'ages' => [3, 12]],
            ['type' => 'Jaket', 'tags' => ['jaket', 'outerwear', 'hangat'], 'ages' => [3, 14]],
            ['type' => 'Sweater', 'tags' => ['sweater', 'hangat', 'nyaman'], 'ages' => [2, 12]],
            ['type' => 'Cardigan', 'tags' => ['cardigan', 'outer', 'stylish'], 'ages' => [3, 12]],
        ];

        $colors = ['Merah', 'Biru', 'Hijau', 'Kuning', 'Pink', 'Ungu', 'Hitam', 'Putih', 'Abu-abu', 'Coklat'];
        $patterns = ['Polos', 'Motif Bunga', 'Motif Hewan', 'Striped', 'Polkadot', 'Karakter Kartun'];
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];

        // Image URLs (using placeholder images)
        $imageUrls = [
            'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=500',
            'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?w=500',
            'https://images.unsplash.com/photo-1596464716127-f2a82984de30?w=500',
            'https://images.unsplash.com/photo-1622290291468-a28f7a7dc6a8?w=500',
            'https://images.unsplash.com/photo-1519457431-44ccd64a579b?w=500',
            'https://images.unsplash.com/photo-1514090458221-65cd468d1df7?w=500',
            'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=500',
            'https://images.unsplash.com/photo-1596464716127-f2a82984de30?w=500',
        ];

        $this->command->info('Starting to seed 50 products...');

        for ($i = 1; $i <= 50; $i++) {
            $productType = $productTypes[array_rand($productTypes)];
            $color = $colors[array_rand($colors)];
            $pattern = $patterns[array_rand($patterns)];

            $productName = "{$productType['type']} Anak {$pattern} {$color}";
            $slug = Str::slug($productName) . '-' . time() . '-' . $i;

            $description = "Koleksi {$productType['type']} anak terbaru dengan desain {$pattern} warna {$color}. " .
                          "Terbuat dari bahan berkualitas tinggi yang nyaman dan aman untuk kulit anak. " .
                          "Cocok untuk berbagai acara, mulai dari casual hingga semi formal. " .
                          "Tersedia dalam berbagai ukuran untuk anak usia {$productType['ages'][0]}-{$productType['ages'][1]} tahun.";

            $tags = json_encode(array_merge($productType['tags'], [$color, $pattern]));

            $seoMeta = json_encode([
                'title' => $productName . ' - Pakaian Anak Berkualitas | Minimoda',
                'description' => substr($description, 0, 160),
                'keywords' => implode(', ', $productType['tags']) . ", pakaian anak, fashion anak, {$color}, {$pattern}"
            ]);

            // Insert product
            $productId = DB::table('products')->insertGetId([
                'name' => $productName,
                'slug' => $slug,
                'description' => $description,
                'brand_id' => $brandIds[array_rand($brandIds)],
                'age_min' => $productType['ages'][0],
                'age_max' => $productType['ages'][1],
                'tags' => $tags,
                'status' => rand(0, 10) > 1 ? 'active' : 'draft', // 90% active
                'seo_meta' => $seoMeta,
                'view_count' => rand(10, 500),
                'is_featured' => rand(0, 10) > 7 ? true : false, // 30% featured
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach to categories (1-3 categories per product)
            $numCategories = rand(1, 3);
            $selectedCategories = array_rand(array_flip($categoryIds), $numCategories);
            if (!is_array($selectedCategories)) {
                $selectedCategories = [$selectedCategories];
            }

            foreach ($selectedCategories as $categoryId) {
                DB::table('product_categories')->insert([
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                ]);
            }

            // Create variants (2-4 variants per product)
            $numVariants = rand(2, 4);
            $variantColors = array_rand(array_flip($colors), min($numVariants, count($colors)));
            if (!is_array($variantColors)) {
                $variantColors = [$variantColors];
            }

            foreach ($variantColors as $variantIndex => $variantColor) {
                foreach ($sizes as $sizeIndex => $size) {
                    // Not all sizes for all colors
                    if (rand(0, 10) < 7) { // 70% chance to have this size
                        $basePrice = rand(50, 300) * 1000; // 50k - 300k
                        $comparePrice = $basePrice + (rand(20, 50) * 1000); // Add 20k-50k for compare price

                        $variantId = DB::table('product_variants')->insertGetId([
                            'product_id' => $productId,
                            'sku' => 'SKU-' . strtoupper(Str::random(8)),
                            'size' => $size,
                            'color' => $variantColor,
                            'weight_gram' => rand(100, 500),
                            'price' => $basePrice,
                            'compare_at_price' => rand(0, 10) > 5 ? $comparePrice : null, // 50% has compare price
                            'stock_quantity' => rand(0, 100),
                            'reserved_quantity' => 0,
                            'barcode' => rand(0, 10) > 5 ? '8990' . rand(100000000, 999999999) : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create variant images (1-3 images per variant)
                        $numVariantImages = rand(1, 3);
                        for ($vImgIndex = 0; $vImgIndex < $numVariantImages; $vImgIndex++) {
                            DB::table('product_variant_images')->insert([
                                'variant_id' => $variantId,
                                'url' => $imageUrls[array_rand($imageUrls)],
                                'alt_text' => "{$productName} - {$variantColor} - Size {$size} - Gambar " . ($vImgIndex + 1),
                                'is_primary' => $vImgIndex === 0, // First image is primary
                                'sort_order' => $vImgIndex,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Create product images (2-5 images per product)
            $numImages = rand(2, 5);
            for ($imgIndex = 0; $imgIndex < $numImages; $imgIndex++) {
                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'url' => $imageUrls[array_rand($imageUrls)],
                    'alt_text' => $productName . ' - Gambar ' . ($imgIndex + 1),
                    'is_primary' => $imgIndex === 0, // First image is primary
                    'sort_order' => $imgIndex,
                    'created_at' => now(),
                ]);
            }

            if ($i % 10 == 0) {
                $this->command->info("Seeded {$i} products...");
            }
        }

        $this->command->info('Successfully seeded 50 products with variants and images!');

        // Show summary
        $totalProducts = DB::table('products')->count();
        $totalVariants = DB::table('product_variants')->count();
        $totalImages = DB::table('product_images')->count();
        $totalVariantImages = DB::table('product_variant_images')->count();

        $this->command->info("Summary:");
        $this->command->info("- Total Products: {$totalProducts}");
        $this->command->info("- Total Variants: {$totalVariants}");
        $this->command->info("- Total Product Images: {$totalImages}");
        $this->command->info("- Total Variant Images: {$totalVariantImages}");
    }
}
