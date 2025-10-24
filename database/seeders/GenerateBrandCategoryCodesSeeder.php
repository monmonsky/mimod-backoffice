<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateBrandCategoryCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to generate Brand and Category codes...');

        // Generate Brand Codes
        $this->generateBrandCodes();

        // Generate Category Codes
        $this->generateCategoryCodes();

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Brand and Category codes generated!');
        $this->command->info('========================================');
    }

    /**
     * Generate codes for brands
     */
    private function generateBrandCodes()
    {
        $brands = DB::table('brands')
            ->whereNull('code')
            ->orWhere('code', '')
            ->get();

        $this->command->info("Found {$brands->count()} brands without codes");

        $usedCodes = DB::table('brands')
            ->whereNotNull('code')
            ->pluck('code')
            ->toArray();

        $generated = 0;

        foreach ($brands as $brand) {
            $code = $this->generateCode($brand->name, 2, $usedCodes);

            DB::table('brands')
                ->where('id', $brand->id)
                ->update(['code' => $code]);

            $usedCodes[] = $code;
            $generated++;

            $this->command->info("✓ Brand '{$brand->name}' → Code: {$code}");
        }

        $this->command->info("Generated {$generated} brand codes");
    }

    /**
     * Generate codes for categories
     */
    private function generateCategoryCodes()
    {
        $categories = DB::table('categories')
            ->whereNull('code')
            ->orWhere('code', '')
            ->get();

        $this->command->info("Found {$categories->count()} categories without codes");

        $usedCodes = DB::table('categories')
            ->whereNotNull('code')
            ->pluck('code')
            ->toArray();

        $generated = 0;

        foreach ($categories as $category) {
            $code = $this->generateCode($category->name, 3, $usedCodes);

            DB::table('categories')
                ->where('id', $category->id)
                ->update(['code' => $code]);

            $usedCodes[] = $code;
            $generated++;

            $this->command->info("✓ Category '{$category->name}' → Code: {$code}");
        }

        $this->command->info("Generated {$generated} category codes");
    }

    /**
     * Generate a unique code from name
     *
     * @param string $name
     * @param int $length
     * @param array $usedCodes
     * @return string
     */
    private function generateCode($name, $length, $usedCodes)
    {
        // Remove special characters, numbers, and spaces
        $clean = preg_replace('/[^A-Za-z]/', '', $name);

        // Strategy 1: Take first N letters
        $code = strtoupper(substr($clean, 0, $length));

        // Ensure minimum length
        if (strlen($code) < $length) {
            $code = str_pad($code, $length, 'X');
        }

        // If code already used, try variations
        if (in_array($code, $usedCodes)) {
            // Strategy 2: Take consonants only
            $consonants = preg_replace('/[AEIOU]/i', '', $clean);
            $code = strtoupper(substr($consonants, 0, $length));

            if (strlen($code) < $length) {
                $code = str_pad($code, $length, 'X');
            }
        }

        // If still duplicate, try first + last letters
        if (in_array($code, $usedCodes)) {
            $firstLetter = strtoupper(substr($clean, 0, 1));
            $lastLetters = strtoupper(substr($clean, -($length - 1)));
            $code = $firstLetter . $lastLetters;

            if (strlen($code) < $length) {
                $code = str_pad($code, $length, 'X');
            }
        }

        // If still duplicate, append numbers
        if (in_array($code, $usedCodes)) {
            $baseCode = substr($code, 0, $length - 1);
            $counter = 1;

            do {
                $code = $baseCode . $counter;
                $counter++;
            } while (in_array($code, $usedCodes) && $counter < 100);
        }

        return $code;
    }
}
