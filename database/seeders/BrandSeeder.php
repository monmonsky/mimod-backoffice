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
        $this->command->info('Seeding brand...');

        $brand = [
            'name' => 'Minimoda',
            'slug' => 'minimoda',
            'description' => 'Fashion Muslim Anak Perempuan - Koleksi busana muslim modern untuk anak perempuan dengan desain trendy dan berkualitas',
            'is_active' => true,
        ];

        DB::table('brands')->insert([
            'name' => $brand['name'],
            'slug' => $brand['slug'],
            'description' => $brand['description'],
            'logo' => null,
            'is_active' => $brand['is_active'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✓ Brand 'Minimoda' seeded successfully");
    }
}
