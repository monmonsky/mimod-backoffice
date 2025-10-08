<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;

class AllProductsController extends Controller
{
    /**
     * Display all products page
     * All data loaded via API
     */
    public function allProducts()
    {
        return view('pages.catalog.all-products.all-products');
    }
}
