<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use App\Repositories\Contracts\Catalog\CategoryRepositoryInterface;
use App\Repositories\Contracts\Catalog\BrandRepositoryInterface;

class AddProductsController extends Controller
{
    protected $productRepo;
    protected $categoryRepo;
    protected $brandRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        CategoryRepositoryInterface $categoryRepo,
        BrandRepositoryInterface $brandRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->brandRepo = $brandRepo;
    }

    public function addProducts()
    {
        $categories = $this->categoryRepo->getAllActive();
        $brands = $this->brandRepo->getAllActive();

        return view('pages.catalog.add-products.add-products', compact('categories', 'brands'));
    }

    public function edit($id)
    {
        $product = $this->productRepo->findByIdWithRelations($id);

        if (!$product) {
            return redirect()->route('catalog.products.all-products')
                ->with('error', 'Product not found');
        }

        $categories = $this->categoryRepo->getAllActive();
        $brands = $this->brandRepo->getAllActive();

        // Get selected category IDs
        $selectedCategories = collect($product->categories)->pluck('id')->toArray();

        return view('pages.catalog.add-products.add-products', compact(
            'product',
            'categories',
            'brands',
            'selectedCategories'
        ));
    }
}
