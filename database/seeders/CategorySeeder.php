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
        $this->command->info('Seeding kids clothing categories...');

        // Parent Categories (Top Level)
        $parentCategories = [
            [
                'name' => 'Pakaian Anak Laki-laki',
                'slug' => 'pakaian-anak-laki-laki',
                'description' => 'Koleksi lengkap pakaian untuk anak laki-laki usia 0-12 tahun',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Pakaian Anak Perempuan',
                'slug' => 'pakaian-anak-perempuan',
                'description' => 'Koleksi lengkap pakaian untuk anak perempuan usia 0-12 tahun',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Pakaian Bayi',
                'slug' => 'pakaian-bayi',
                'description' => 'Pakaian untuk bayi baru lahir hingga 2 tahun',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'description' => 'Aksesoris pelengkap untuk anak',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Sepatu Anak',
                'slug' => 'sepatu-anak',
                'description' => 'Sepatu untuk anak laki-laki dan perempuan',
                'sort_order' => 50,
                'is_active' => true,
            ],
        ];

        $parentIds = [];
        foreach ($parentCategories as $category) {
            $category['created_at'] = now();
            $category['updated_at'] = now();
            $category['parent_id'] = null;

            $id = DB::table('categories')->insertGetId($category);
            $parentIds[$category['slug']] = $id;
        }

        // Child Categories for "Pakaian Anak Laki-laki"
        $boysCategories = [
            ['name' => 'Kaos Anak Laki-laki', 'slug' => 'kaos-anak-laki-laki', 'sort_order' => 1],
            ['name' => 'Kemeja Anak Laki-laki', 'slug' => 'kemeja-anak-laki-laki', 'sort_order' => 2],
            ['name' => 'Celana Pendek Anak Laki-laki', 'slug' => 'celana-pendek-anak-laki-laki', 'sort_order' => 3],
            ['name' => 'Celana Panjang Anak Laki-laki', 'slug' => 'celana-panjang-anak-laki-laki', 'sort_order' => 4],
            ['name' => 'Jaket & Sweater Anak Laki-laki', 'slug' => 'jaket-sweater-anak-laki-laki', 'sort_order' => 5],
            ['name' => 'Setelan Anak Laki-laki', 'slug' => 'setelan-anak-laki-laki', 'sort_order' => 6],
        ];

        foreach ($boysCategories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => $parentIds['pakaian-anak-laki-laki'],
                'description' => null,
                'image' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Child Categories for "Pakaian Anak Perempuan"
        $girlsCategories = [
            ['name' => 'Kaos Anak Perempuan', 'slug' => 'kaos-anak-perempuan', 'sort_order' => 1],
            ['name' => 'Blouse & Kemeja Anak Perempuan', 'slug' => 'blouse-kemeja-anak-perempuan', 'sort_order' => 2],
            ['name' => 'Dress & Rok', 'slug' => 'dress-rok', 'sort_order' => 3],
            ['name' => 'Celana Pendek Anak Perempuan', 'slug' => 'celana-pendek-anak-perempuan', 'sort_order' => 4],
            ['name' => 'Celana Panjang & Legging', 'slug' => 'celana-panjang-legging', 'sort_order' => 5],
            ['name' => 'Jaket & Cardigan Anak Perempuan', 'slug' => 'jaket-cardigan-anak-perempuan', 'sort_order' => 6],
            ['name' => 'Setelan Anak Perempuan', 'slug' => 'setelan-anak-perempuan', 'sort_order' => 7],
        ];

        foreach ($girlsCategories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => $parentIds['pakaian-anak-perempuan'],
                'description' => null,
                'image' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Child Categories for "Pakaian Bayi"
        $babyCategories = [
            ['name' => 'Jumper & Romper Bayi', 'slug' => 'jumper-romper-bayi', 'sort_order' => 1],
            ['name' => 'Baju Tidur Bayi', 'slug' => 'baju-tidur-bayi', 'sort_order' => 2],
            ['name' => 'Setelan Bayi', 'slug' => 'setelan-bayi', 'sort_order' => 3],
            ['name' => 'Kaos Kaki Bayi', 'slug' => 'kaos-kaki-bayi', 'sort_order' => 4],
            ['name' => 'Topi & Sarung Tangan Bayi', 'slug' => 'topi-sarung-tangan-bayi', 'sort_order' => 5],
        ];

        foreach ($babyCategories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => $parentIds['pakaian-bayi'],
                'description' => null,
                'image' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Child Categories for "Aksesoris"
        $accessoriesCategories = [
            ['name' => 'Topi Anak', 'slug' => 'topi-anak', 'sort_order' => 1],
            ['name' => 'Tas Anak', 'slug' => 'tas-anak', 'sort_order' => 2],
            ['name' => 'Kacamata Anak', 'slug' => 'kacamata-anak', 'sort_order' => 3],
            ['name' => 'Ikat Pinggang Anak', 'slug' => 'ikat-pinggang-anak', 'sort_order' => 4],
            ['name' => 'Kaos Kaki Anak', 'slug' => 'kaos-kaki-anak', 'sort_order' => 5],
            ['name' => 'Aksesoris Rambut', 'slug' => 'aksesoris-rambut', 'sort_order' => 6],
        ];

        foreach ($accessoriesCategories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => $parentIds['aksesoris'],
                'description' => null,
                'image' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Child Categories for "Sepatu Anak"
        $shoesCategories = [
            ['name' => 'Sepatu Sneakers Anak', 'slug' => 'sepatu-sneakers-anak', 'sort_order' => 1],
            ['name' => 'Sepatu Sandal Anak', 'slug' => 'sepatu-sandal-anak', 'sort_order' => 2],
            ['name' => 'Sepatu Sekolah', 'slug' => 'sepatu-sekolah', 'sort_order' => 3],
            ['name' => 'Sepatu Olahraga Anak', 'slug' => 'sepatu-olahraga-anak', 'sort_order' => 4],
            ['name' => 'Sepatu Boot Anak', 'slug' => 'sepatu-boot-anak', 'sort_order' => 5],
        ];

        foreach ($shoesCategories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => $parentIds['sepatu-anak'],
                'description' => null,
                'image' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $totalCategories = count($parentCategories)
            + count($boysCategories)
            + count($girlsCategories)
            + count($babyCategories)
            + count($accessoriesCategories)
            + count($shoesCategories);

        $this->command->info("✓ {$totalCategories} categories seeded successfully");
        $this->command->info("  - 5 parent categories");
        $this->command->info("  - " . (count($boysCategories) + count($girlsCategories) + count($babyCategories) + count($accessoriesCategories) + count($shoesCategories)) . " child categories");
    }
}
