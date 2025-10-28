<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding categories...');

        // All categories are top-level (no parent)
        $categories = [
            ['name' => 'Outerwear', 'slug' => 'outerwear', 'sort_order' => 1],
            ['name' => 'Kemeja & Blouse', 'slug' => 'kemeja-blouse', 'sort_order' => 2],
            ['name' => 'Pakaian Muslim Anak Perempuan', 'slug' => 'pakaian-muslim-anak-perempuan', 'sort_order' => 3],
            ['name' => 'Kemeja', 'slug' => 'kemeja', 'sort_order' => 4],
            ['name' => 'Kaos Polo', 'slug' => 'kaos-polo', 'sort_order' => 5],
            ['name' => 'Kotak Kado', 'slug' => 'kotak-kado', 'sort_order' => 6],
            ['name' => 'Dress', 'slug' => 'dress', 'sort_order' => 7],
            ['name' => 'Romper, Jumpsuit & Overall', 'slug' => 'romper-jumpsuit-overall', 'sort_order' => 8],
            ['name' => 'Rok', 'slug' => 'rok', 'sort_order' => 9],
            ['name' => 'Celana Pendek', 'slug' => 'celana-pendek', 'sort_order' => 10],
            ['name' => 'Celana Panjang', 'slug' => 'celana-panjang', 'sort_order' => 11],
            ['name' => 'Kaos', 'slug' => 'kaos', 'sort_order' => 12],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => null,
                'description' => null,
                'image' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✓ Seeded " . count($categories) . " categories");
    }
}
