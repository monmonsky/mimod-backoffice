<?php

namespace App\Http\Controllers\Api\AccessControl;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionApiController extends Controller
{
    protected $permissionRepo;
    protected $response;

    public function __construct(
        PermissionRepositoryInterface $permissionRepo,
        Response $response
    ) {
        $this->permissionRepo = $permissionRepo;
        $this->response = $response;
    }

    /**
     * Get all permissions with optional filters
     */
    public function index(Request $request)
    {
        try {
            $query = $this->permissionRepo->query()
                ->select(
                    'permissions.*',
                    'modules.display_name as module_display_name',
                    'modules.icon as module_icon',
                    'modules.sort_order as module_sort_order'
                )
                ->leftJoin('modules', 'permissions.module', '=', 'modules.name')
                ->orderBy('modules.sort_order')
                ->orderBy('permissions.name');

            // Filter by module
            if ($request->has('module')) {
                $query->where('permissions.module', $request->module);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('permissions.is_active', $request->is_active);
            }

            // Search by name or display_name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('permissions.name', 'ILIKE', '%' . $search . '%')
                      ->orWhere('permissions.display_name', 'ILIKE', '%' . $search . '%')
                      ->orWhere('modules.display_name', 'ILIKE', '%' . $search . '%');
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $permissions = $query->paginate($perPage);

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
    public function grouped(Request $request)
    {
        try {
            $modules = DB::table('modules')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $result = [];

            foreach ($modules as $module) {
                $query = DB::table('permissions')
                    ->where('module', $module->name)
                    ->where('is_active', true)
                    ->orderBy('name');

                // Apply search filter if provided
                if ($request->has('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'ILIKE', '%' . $search . '%')
                          ->orWhere('display_name', 'ILIKE', '%' . $search . '%');
                    });
                }

                $permissions = $query->get();

                // Only include modules that have permissions
                if ($permissions->isNotEmpty()) {
                    $result[] = [
                        'module' => [
                            'id' => $module->id,
                            'name' => $module->name,
                            'display_name' => $module->display_name,
                            'icon' => $module->icon,
                            'group_name' => $module->group_name,
                        ],
                        'permissions' => $permissions
                    ];
                }
            }

            $resultBuilder = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Grouped permissions retrieved successfully')
                ->setData($result);

            return response()->json($this->response->generateResponse($resultBuilder), 200);
        } catch (\Exception $e) {
            $resultBuilder = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve grouped permissions: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($resultBuilder), 500);
        }
    }

    /**
     * Get single permission by ID
     */
    public function show($id)
    {
        try {
            $permission = DB::table('permissions')
                ->select(
                    'permissions.*',
                    'modules.display_name as module_display_name',
                    'modules.icon as module_icon'
                )
                ->leftJoin('modules', 'permissions.module', '=', 'modules.name')
                ->where('permissions.id', $id)
                ->first();

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
