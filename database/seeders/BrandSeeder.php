<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding kids clothing brands...');

        $brands = [
            // International Brands
            [
                'name' => "Carter's",
                'slug' => 'carters',
                'description' => 'Leading American baby and children\'s apparel brand known for quality and comfort',
                'is_active' => true,
            ],
            [
                'name' => 'OshKosh B\'gosh',
                'slug' => 'oshkosh-bgosh',
                'description' => 'Classic American children\'s apparel brand specializing in durable play clothes',
                'is_active' => true,
            ],
            [
                'name' => 'Mothercare',
                'slug' => 'mothercare',
                'description' => 'British brand offering clothing and products for expectant mothers, babies and children',
                'is_active' => true,
            ],
            [
                'name' => 'Zara Kids',
                'slug' => 'zara-kids',
                'description' => 'Spanish fashion brand offering trendy clothing for children',
                'is_active' => true,
            ],
            [
                'name' => 'H&M Kids',
                'slug' => 'h-m-kids',
                'description' => 'Swedish multinational clothing retailer with affordable kids fashion',
                'is_active' => true,
            ],
            [
                'name' => 'Gap Kids',
                'slug' => 'gap-kids',
                'description' => 'American worldwide clothing and accessories retailer',
                'is_active' => true,
            ],
            [
                'name' => 'Uniqlo Kids',
                'slug' => 'uniqlo-kids',
                'description' => 'Japanese casual wear designer, manufacturer and retailer',
                'is_active' => true,
            ],
            [
                'name' => 'Next Kids',
                'slug' => 'next-kids',
                'description' => 'British multinational clothing, footwear and home products retailer',
                'is_active' => true,
            ],
            [
                'name' => 'The Children\'s Place',
                'slug' => 'the-childrens-place',
                'description' => 'American children\'s specialty clothing retailer',
                'is_active' => true,
            ],
            [
                'name' => 'Old Navy Kids',
                'slug' => 'old-navy-kids',
                'description' => 'American clothing and accessories retailing company',
                'is_active' => true,
            ],

            // Indonesian & Local Brands
            [
                'name' => 'Little Palmerhaus',
                'slug' => 'little-palmerhaus',
                'description' => 'Indonesian premium children\'s clothing brand',
                'is_active' => true,
            ],
            [
                'name' => 'Gingersnaps',
                'slug' => 'gingersnaps',
                'description' => 'Philippine brand offering stylish and comfortable children\'s wear',
                'is_active' => true,
            ],
            [
                'name' => 'Kids Icon',
                'slug' => 'kids-icon',
                'description' => 'Indonesian children\'s fashion brand with modern designs',
                'is_active' => true,
            ],
            [
                'name' => 'Cuit Cuit',
                'slug' => 'cuit-cuit',
                'description' => 'Indonesian baby and kids clothing brand',
                'is_active' => true,
            ],
            [
                'name' => 'Moejoe',
                'slug' => 'moejoe',
                'description' => 'Indonesian brand for baby and children accessories',
                'is_active' => true,
            ],

            // Sports & Active Brands
            [
                'name' => 'Nike Kids',
                'slug' => 'nike-kids',
                'description' => 'American athletic footwear and apparel corporation',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas Kids',
                'slug' => 'adidas-kids',
                'description' => 'German multinational corporation that designs and manufactures sports shoes, clothing and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Puma Kids',
                'slug' => 'puma-kids',
                'description' => 'German multinational corporation that designs and manufactures athletic and casual footwear, apparel and accessories',
                'is_active' => true,
            ],

            // Character & Licensed Brands
            [
                'name' => 'Disney Kids',
                'slug' => 'disney-kids',
                'description' => 'Licensed children\'s clothing featuring Disney characters',
                'is_active' => true,
            ],
            [
                'name' => 'Marvel Kids',
                'slug' => 'marvel-kids',
                'description' => 'Licensed children\'s clothing featuring Marvel superheroes',
                'is_active' => true,
            ],
            [
                'name' => 'Hello Kitty',
                'slug' => 'hello-kitty',
                'description' => 'Japanese character brand for children',
                'is_active' => true,
            ],

            // Generic/House Brands
            [
                'name' => 'Little Ones',
                'slug' => 'little-ones',
                'description' => 'Affordable everyday clothing for babies and toddlers',
                'is_active' => true,
            ],
            [
                'name' => 'Kids Collection',
                'slug' => 'kids-collection',
                'description' => 'Quality children\'s clothing at great prices',
                'is_active' => true,
            ],
            [
                'name' => 'Junior Style',
                'slug' => 'junior-style',
                'description' => 'Trendy and comfortable fashion for kids',
                'is_active' => true,
            ],
            [
                'name' => 'Baby Comfort',
                'slug' => 'baby-comfort',
                'description' => 'Soft and comfortable clothing for newborns and babies',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
                'description' => $brand['description'],
                'logo' => null,
                'is_active' => $brand['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✓ " . count($brands) . " brands seeded successfully");
        $this->command->info("  - 10 international brands");
        $this->command->info("  - 5 Indonesian/local brands");
        $this->command->info("  - 3 sports brands");
        $this->command->info("  - 3 character/licensed brands");
        $this->command->info("  - 4 generic/house brands");
    }
}
