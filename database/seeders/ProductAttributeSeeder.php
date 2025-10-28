<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding product attributes...');

        // Create Size attribute
        $sizeAttributeId = DB::table('product_attributes')->insertGetId([
            'name' => 'Size',
            'slug' => 'size',
            'type' => 'select',
            'description' => 'Product size/ukuran',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create size values
        $sizeValues = [
            ['value' => '5-6 tahun', 'slug' => '5-6', 'sort_order' => 1],
            ['value' => '7-8 tahun', 'slug' => '7-8', 'sort_order' => 2],
            ['value' => '9-10 tahun', 'slug' => '9-10', 'sort_order' => 3],
            ['value' => '11-12 tahun', 'slug' => '11-12', 'sort_order' => 4],
        ];

        foreach ($sizeValues as $value) {
            DB::table('product_attribute_values')->insert([
                'product_attribute_id' => $sizeAttributeId,
                'value' => $value['value'],
                'slug' => $value['slug'],
                'meta' => null,
                'is_active' => true,
                'sort_order' => $value['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Color attribute
        $colorAttributeId = DB::table('product_attributes')->insertGetId([
            'name' => 'Color',
            'slug' => 'color',
            'type' => 'color',
            'description' => 'Product color/warna',
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create color values with hex codes
        $colorValues = [
            ['value' => 'Bright Pink', 'slug' => 'bright-pink', 'hex' => '#FF69B4', 'sort_order' => 1],
            ['value' => 'Merah Muda', 'slug' => 'merah-muda', 'hex' => '#FFB6C1', 'sort_order' => 2],
            ['value' => 'Putih', 'slug' => 'putih', 'hex' => '#FFFFFF', 'sort_order' => 3],
            ['value' => 'Hitam', 'slug' => 'hitam', 'hex' => '#000000', 'sort_order' => 4],
            ['value' => 'Biru', 'slug' => 'biru', 'hex' => '#0000FF', 'sort_order' => 5],
            ['value' => 'Hijau', 'slug' => 'hijau', 'hex' => '#00FF00', 'sort_order' => 6],
            ['value' => 'Kuning', 'slug' => 'kuning', 'hex' => '#FFFF00', 'sort_order' => 7],
            ['value' => 'Abu-abu', 'slug' => 'abu-abu', 'hex' => '#808080', 'sort_order' => 8],
        ];

        foreach ($colorValues as $value) {
            DB::table('product_attribute_values')->insert([
                'product_attribute_id' => $colorAttributeId,
                'value' => $value['value'],
                'slug' => $value['slug'],
                'meta' => json_encode(['hex' => $value['hex']]),
                'is_active' => true,
                'sort_order' => $value['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✓ Seeded 2 attributes with their values");
        $this->command->info("  - Size: 4 values");
        $this->command->info("  - Color: 8 values");
    }
}
