<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\CategoryRepository;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    protected $categoryRepo;
    protected $response;

    public function __construct(CategoryRepository $categoryRepo, Response $response)
    {
        $this->categoryRepo = $categoryRepo;
        $this->response = $response;
    }

    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        try {
            $categories = $this->categoryRepo->getAllActive();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Categories retrieved successfully')
                ->setData($categories);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve categories: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get category tree
     */
    public function tree()
    {
        try {
            $categories = $this->categoryRepo->getAllActive();

            $tree = [];
            $categoryMap = [];

            foreach ($categories as $category) {
                $categoryMap[$category->id] = $category;
                $categoryMap[$category->id]->children = [];
            }

            foreach ($categoryMap as $category) {
                if ($category->parent_id && isset($categoryMap[$category->parent_id])) {
                    $categoryMap[$category->parent_id]->children[] = $category;
                } elseif (!$category->parent_id) {
                    $tree[] = $category;
                }
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category tree retrieved successfully')
                ->setData($tree);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve category tree: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single category
     */
    public function show($id)
    {
        try {
            $category = $this->categoryRepo->findById($id);

            if (!$category) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Category not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category retrieved successfully')
                ->setData($category);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve category: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get parent categories only
     */
    public function parents()
    {
        try {
            $categories = $this->categoryRepo->table()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Parent categories retrieved successfully')
                ->setData($categories);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve parent categories: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get children of a category
     */
    public function children($parentId)
    {
        try {
            $children = $this->categoryRepo->table()
                ->where('parent_id', $parentId)
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Category children retrieved successfully')
                ->setData($children);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve category children: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
