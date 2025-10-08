<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\BrandRepositoryInterface;

class BrandsController extends Controller
{
    protected $brandRepo;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepo = $brandRepository;
    }

    /**
     * Display brands page
     * All data loaded via API
     */
    public function brands()
    {
        return view('pages.catalog.brands.brands');
    }
}
