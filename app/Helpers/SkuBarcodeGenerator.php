<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class SkuBarcodeGenerator
{
    /**
     * Generate SKU for product variant
     *
     * @param int $productId
     * @param int $brandId
     * @param int $categoryId
     * @param string|null $color
     * @param string|null $size
     * @param int $stock
     * @return string
     */
    public static function generateSKU($productId, $brandId, $categoryId, $color = null, $size = null, $stock = 0)
    {
        $prefix = config('app.sku_prefix', 'MM');

        // Get brand code
        $brand = DB::table('brands')->where('id', $brandId)->first();
        $brandCode = $brand && $brand->code ? $brand->code : 'XX';

        // Get category code
        $category = $categoryId ? DB::table('categories')->where('id', $categoryId)->first() : null;
        $categoryCode = $category && $category->code ? $category->code : 'XXX';

        // Generate size code (remove spaces and special chars)
        $sizeCode = strtoupper(str_replace([' ', '-', '/'], '', $size ?? 'OS'));

        // Generate color code
        $colorCode = self::generateColorCode($color);

        // Format: MM-{brand}-{category}-{size}-{color}-{stock}
        return "{$prefix}-{$brandCode}-{$categoryCode}-{$sizeCode}-{$colorCode}-{$stock}";
    }


    /**
     * Generate Barcode for product variant
     *
     * @param int $variantId
     * @return string
     */
    public static function generateBarcode($variantId = null)
    {
        $type = config('app.barcode_type', 'EAN13');

        switch ($type) {
            case 'UPC':
                return self::generateUPC($variantId);

            case 'CODE128':
                return self::generateCode128($variantId);

            case 'EAN13':
            default:
                return self::generateEAN13($variantId);
        }
    }

    /**
     * Generate EAN-13 Barcode
     * Format: {Company Prefix}{Product Code}{Check Digit}
     */
    private static function generateEAN13($variantId)
    {
        $companyPrefix = config('app.barcode_company_prefix', '899123');

        // Get next product code (sequential)
        $lastBarcode = DB::table('product_variants')
            ->whereNotNull('barcode')
            ->where('barcode', 'like', $companyPrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBarcode) {
            // Extract product code from last barcode
            $lastProductCode = (int) substr($lastBarcode->barcode, strlen($companyPrefix), 6);
            $productCode = $lastProductCode + 1;
        } else {
            $productCode = 1;
        }

        // Pad product code to 6 digits
        $productCodeStr = str_pad($productCode, 6, '0', STR_PAD_LEFT);

        // Build barcode without check digit
        $barcodeWithoutCheck = $companyPrefix . $productCodeStr;

        // Calculate check digit
        $checkDigit = self::calculateEAN13CheckDigit($barcodeWithoutCheck);

        return $barcodeWithoutCheck . $checkDigit;
    }

    /**
     * Generate UPC-A Barcode (12 digits)
     */
    private static function generateUPC($variantId)
    {
        $companyPrefix = config('app.barcode_company_prefix', '899123');

        // Similar to EAN13 but 12 digits total
        $lastBarcode = DB::table('product_variants')
            ->whereNotNull('barcode')
            ->where('barcode', 'like', $companyPrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBarcode) {
            $lastProductCode = (int) substr($lastBarcode->barcode, strlen($companyPrefix), 5);
            $productCode = $lastProductCode + 1;
        } else {
            $productCode = 1;
        }

        $productCodeStr = str_pad($productCode, 5, '0', STR_PAD_LEFT);
        $barcodeWithoutCheck = $companyPrefix . $productCodeStr;
        $checkDigit = self::calculateUPCCheckDigit($barcodeWithoutCheck);

        return $barcodeWithoutCheck . $checkDigit;
    }

    /**
     * Generate Code128 Barcode
     */
    private static function generateCode128($variantId)
    {
        $prefix = config('app.sku_prefix', 'MM');
        $timestamp = substr(time(), -6);
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return "{$prefix}{$timestamp}{$random}";
    }

    /**
     * Calculate EAN-13 Check Digit
     */
    private static function calculateEAN13CheckDigit($code)
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $code[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit;
    }

    /**
     * Calculate UPC-A Check Digit
     */
    private static function calculateUPCCheckDigit($code)
    {
        $sum = 0;

        for ($i = 0; $i < 11; $i++) {
            $digit = (int) $code[$i];
            $sum += ($i % 2 === 0) ? $digit * 3 : $digit;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit;
    }

    /**
     * Generate color code from color name
     */
    private static function generateColorCode($color)
    {
        if (empty($color)) {
            return 'DEF';
        }

        $colorMap = [
            'merah' => 'RED',
            'biru' => 'BLU',
            'hijau' => 'GRN',
            'kuning' => 'YLW',
            'pink' => 'PNK',
            'ungu' => 'PRP',
            'hitam' => 'BLK',
            'putih' => 'WHT',
            'abu-abu' => 'GRY',
            'abu' => 'GRY',
            'coklat' => 'BRN',
            'orange' => 'ORG',
            'oranye' => 'ORG',
            'clear blue' => 'CB',
            'navy blue' => 'NB',
            'dark blue' => 'DB',
            'light blue' => 'LB',
            'sky blue' => 'SB',
        ];

        $colorLower = strtolower($color);

        if (isset($colorMap[$colorLower])) {
            return $colorMap[$colorLower];
        }

        // For multi-word colors, take first letter of each word
        // Example: "Clear Blue" → "CB", "Dark Red" → "DR"
        if (preg_match_all('/\b(\w)/', $color, $matches)) {
            $code = strtoupper(implode('', $matches[1]));
            // Limit to 2-3 characters
            return substr($code, 0, min(3, strlen($code)));
        }

        // Fallback: take first 2-3 characters
        return strtoupper(substr(str_replace(' ', '', $color), 0, 2));
    }

    /**
     * Generate code from name (for brands/categories without code)
     */
    private static function generateCodeFromName($name, $length = 3)
    {
        // Remove special characters and spaces
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $name);

        // Take first N characters
        $code = strtoupper(substr($clean, 0, $length));

        // Pad if too short
        if (strlen($code) < $length) {
            $code = str_pad($code, $length, 'X');
        }

        return $code;
    }

    /**
     * Validate SKU uniqueness
     */
    public static function isSkuUnique($sku, $excludeId = null)
    {
        $query = DB::table('product_variants')->where('sku', $sku);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Validate Barcode uniqueness
     */
    public static function isBarcodeUnique($barcode, $excludeId = null)
    {
        $query = DB::table('product_variants')->where('barcode', $barcode);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Make SKU unique by appending suffix if needed
     */
    public static function ensureUniqueSKU($baseSKU, $excludeId = null)
    {
        $sku = $baseSKU;
        $counter = 1;

        while (!self::isSkuUnique($sku, $excludeId)) {
            $sku = $baseSKU . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;

            // Safety limit
            if ($counter > 100) {
                $sku = $baseSKU . '-' . uniqid();
                break;
            }
        }

        return $sku;
    }
}
