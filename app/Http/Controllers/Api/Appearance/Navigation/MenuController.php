<?php

namespace App\Http\Controllers\Api\Appearance\Navigation;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Appearance\Navigation\Contracts\MenuRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    protected $menuRepository;
    protected $response;

    public function __construct(MenuRepositoryInterface $menuRepository, Response $response)
    {
        $this->menuRepository = $menuRepository;
        $this->response = $response;
    }

    /**
     * Get all menus (for admin management)
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'parent_id' => $request->input('parent_id'),
                'active' => $request->input('active'),
                'location' => $request->input('location'),
                'link_type' => $request->input('link_type'),
                'search' => $request->input('search'),
                'per_page' => $request->input('per_page', 50),
            ];

            $menus = $this->menuRepository->getAllMenus($filters);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Menus retrieved successfully')
                ->setData($menus);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve menus: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single menu
     */
    public function show($id)
    {
        try {
            $menu = $this->menuRepository->findById($id);

            if (!$menu) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Menu not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Menu retrieved successfully')
                ->setData($menu);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve menu: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Create new menu
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:100',
                'slug' => 'nullable|string|max:100|unique:menus,slug',
                'link_type' => 'required|in:static,category,brand,page,custom,none',
                'url' => 'nullable|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'parent_id' => 'nullable|exists:menus,id',
                'icon' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'order' => 'nullable|integer|min:0',
                'is_clickable' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'target' => 'nullable|in:_self,_blank',
                'menu_locations' => 'nullable|array',
                'meta' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage($validator->errors()->first())
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            // Validate link_type specific requirements
            $data = $validator->validated();

            if ($data['link_type'] === 'category' && empty($data['category_id'])) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Category ID is required when link type is category')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            if ($data['link_type'] === 'brand' && empty($data['brand_id'])) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Brand ID is required when link type is brand')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            if ($data['link_type'] === 'static' && empty($data['url'])) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('URL is required when link type is static')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $menu = $this->menuRepository->create($data);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('201')
                ->setMessage('Menu created successfully')
                ->setData($menu);

            return response()->json($this->response->generateResponse($result), 201);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to create menu: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Update menu
     */
    public function update(Request $request, $id)
    {
        try {
            $menu = $this->menuRepository->findById($id);

            if (!$menu) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Menu not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:100',
                'slug' => 'sometimes|string|max:100|unique:menus,slug,' . $id,
                'link_type' => 'sometimes|required|in:static,category,brand,page,custom,none',
                'url' => 'nullable|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'parent_id' => 'nullable|exists:menus,id',
                'icon' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'order' => 'nullable|integer|min:0',
                'is_clickable' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'target' => 'nullable|in:_self,_blank',
                'menu_locations' => 'nullable|array',
                'meta' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage($validator->errors()->first())
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $data = $validator->validated();

            // Prevent self as parent
            if (isset($data['parent_id']) && $data['parent_id'] == $id) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage('Menu cannot be its own parent')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $updated = $this->menuRepository->update($id, $data);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Menu updated successfully')
                ->setData($updated);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to update menu: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Delete menu
     */
    public function destroy($id)
    {
        try {
            $menu = $this->menuRepository->findById($id);

            if (!$menu) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Menu not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->menuRepository->delete($id);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Menu deleted successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to delete menu: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Reorder menus (drag & drop)
     */
    public function reorder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:menus,id',
                'orders.*.order' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('422')
                    ->setMessage($validator->errors()->first())
                    ->setData(['errors' => $validator->errors()]);

                return response()->json($this->response->generateResponse($result), 422);
            }

            $this->menuRepository->reorder($request->input('orders'));

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Menu order updated successfully')
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to reorder menus: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get parent menus for dropdown
     */
    public function getParents(Request $request)
    {
        try {
            $menus = $this->menuRepository->getMenusForSelect();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Parent menus retrieved successfully')
                ->setData($menus);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve parent menus: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get menu tree by location (for frontend/public)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenuByLocation(Request $request)
    {
        try {
            $location = $request->input('location', 'header');
            $activeOnly = $request->input('active_only', true);

            $menuTree = $this->menuRepository->getMenuTree($location, $activeOnly);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Menu retrieved successfully')
                ->setData($menuTree);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve menu: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get all menu locations (header, footer, etc) - for frontend/public
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllLocations()
    {
        try {
            $menus = [
                'header' => $this->menuRepository->getMenuTree('header', true),
                'footer' => $this->menuRepository->getMenuTree('footer', true),
                'mobile' => $this->menuRepository->getMenuTree('mobile', true),
            ];

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('All menus retrieved successfully')
                ->setData($menus);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve menus: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
