<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllProductsController extends Controller
{
    public function allProducts() {
        return view('pages.catalog.all-products.all-products');
    }
}
