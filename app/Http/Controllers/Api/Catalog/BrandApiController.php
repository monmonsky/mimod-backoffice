<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Catalog\BrandRepository;
use Illuminate\Http\Request;

class BrandApiController extends Controller
{
    protected $brandRepo;
    protected $response;

    public function __construct(BrandRepository $brandRepo, Response $response)
    {
        $this->brandRepo = $brandRepo;
        $this->response = $response;
    }

    /**
     * Get all active brands
     */
    public function index()
    {
        try {
            $brands = $this->brandRepo->getAllActive();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brands retrieved successfully')
                ->setData($brands);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve brands: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single brand
     */
    public function show($id)
    {
        try {
            $brand = $this->brandRepo->findById($id);

            if (!$brand) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Brand not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Brand retrieved successfully')
                ->setData($brand);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve brand: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
