<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\CategoryRepositoryInterface;

class CategoriesController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
    }

    /**
     * Display categories page
     * All data loaded via API
     */
    public function categories()
    {
        return view('pages.catalog.categories.categories');
    }
}
