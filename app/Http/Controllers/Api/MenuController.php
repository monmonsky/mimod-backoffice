<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Menu\MenuRepositoryInterface;
use Illuminate\Http\Request;

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
     * Get menu tree by location (for frontend)
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
     * Get all menu locations (header, footer, etc)
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
