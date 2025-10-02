<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VariantsController extends Controller
{
    public function variants() {
        return view('pages.catalog.variants.variants');
    }
}
