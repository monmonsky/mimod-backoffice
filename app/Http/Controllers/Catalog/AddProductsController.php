<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddProductsController extends Controller
{
    public function addProducts() {
        return view('pages.catalog.add-products.add-products');
    }
}
