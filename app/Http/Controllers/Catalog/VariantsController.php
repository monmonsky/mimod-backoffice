<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariantsController extends Controller
{
    public function variants() {
        // Get all variants with product info
        $variants = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->select(
                'product_variants.*',
                'products.name as product_name',
                'products.slug as product_slug',
                'brands.name as brand_name'
            )
            ->orderBy('products.name', 'asc')
            ->orderBy('product_variants.size', 'asc')
            ->get();

        // Calculate statistics
        $statistics = [
            'total' => $variants->count(),
            'total_stock' => $variants->sum('stock_quantity'),
            'low_stock' => $variants->where('stock_quantity', '<=', 10)->count(),
            'out_of_stock' => $variants->where('stock_quantity', 0)->count(),
        ];

        return view('pages.catalog.variants.variants', compact('variants', 'statistics'));
    }
}
