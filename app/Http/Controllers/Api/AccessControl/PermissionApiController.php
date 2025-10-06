<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Http\Request;

class PermissionApiController extends Controller
{
    protected $permissionRepo;
    protected $response;

    public function __construct(PermissionRepositoryInterface $permissionRepo, Response $response)
    {
        $this->permissionRepo = $permissionRepo;
        $this->response = $response;
    }

    /**
     * Get all permissions with filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->permissionRepo->query();

            // Filter by module
            if ($request->has('module')) {
                $query->where('module', $request->module);
            }

            // Filter by action
            if ($request->has('action')) {
                $query->where('action', $request->action);
            }

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Search by name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ILIKE', '%' . $search . '%')
                      ->orWhere('display_name', 'ILIKE', '%' . $search . '%');
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 50);
            $permissions = $query->orderBy('module')->orderBy('action')->paginate($perPage);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Permissions retrieved successfully')
                ->setData($permissions);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve permissions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get permissions grouped by module
     */
    public function grouped()
    {
        try {
            $permissions = $this->permissionRepo->getAll();

            // Group by module
            $grouped = $permissions->groupBy('module')->map(function ($items, $module) {
                return [
                    'module' => $module,
                    'permissions' => $items->values()
                ];
            })->values();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Grouped permissions retrieved successfully')
                ->setData($grouped);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve grouped permissions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    /**
     * Get single permission by ID
     */
    public function show($id)
    {
        try {
            $permission = $this->permissionRepo->findById($id);

            if (!$permission) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Permission not found')
                    ->setData([]);

                return response()->json($this->response->generateResponse($result), 404);
            }

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Permission retrieved successfully')
                ->setData($permission);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve permission: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
