<?php

namespace App\Http\View\Composers;

use App\Repositories\Cache\ModuleCacheRepository;
use Illuminate\View\View;

class SidebarComposer
{
    protected ModuleCacheRepository $moduleCache;

    public function __construct(ModuleCacheRepository $moduleCache)
    {
        $this->moduleCache = $moduleCache;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        try {
            $modules = $this->moduleCache->getVisibleWithChildren();

            // DEBUG: Log before filtering
            \Log::info('SidebarComposer - Before filter: ' . count($modules) . ' modules');

            // Filter modules based on user permissions
            $modules = $this->filterModulesByPermissions($modules);

            // DEBUG: Log after filtering
            $userArray = request()->get('user');
            \Log::info('SidebarComposer - After filter: ' . count($modules) . ' modules', [
                'user_id' => $userArray['id'] ?? null,
                'role_id' => $userArray['role_id'] ?? null,
                'modules' => collect($modules)->pluck('display_name')->toArray()
            ]);
        } catch (\Exception $e) {
            // Fallback to empty array if cache fails
            \Log::warning('Failed to load sidebar modules from cache: ' . $e->getMessage());
            $modules = [];
        }

        $view->with('sidebarModules', $modules ?? []);
    }

    /**
     * Filter modules based on user role permissions
     */
    protected function filterModulesByPermissions($modules)
    {
        // Get user from request (set by auth middleware)
        $userArray = request()->get('user');

        if (!$userArray) {
            // No user yet, return all modules
            // Auth middleware will redirect if needed
            return $modules;
        }

        // Get role_id from user array
        $roleId = $userArray['role_id'] ?? null;

        // If user doesn't have role, return empty sidebar
        if (!$roleId) {
            \Log::warning('User has no role_id', ['user_id' => $userArray['id'] ?? null]);
            return [];
        }

        // Get user role permissions
        $userPermissions = \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role_id', $roleId)
            ->pluck('permissions.name')
            ->toArray();

        // Filter modules
        $filteredModules = [];

        foreach ($modules as $module) {
            // Check if user has permission for this module
            if ($this->hasModulePermission($module, $userPermissions)) {
                // If module has children, filter children too
                if (isset($module->children) && count($module->children) > 0) {
                    $filteredChildren = [];

                    foreach ($module->children as $child) {
                        if ($this->hasModulePermission($child, $userPermissions)) {
                            $filteredChildren[] = $child;
                        }
                    }

                    // Only include parent if it has accessible children
                    if (count($filteredChildren) > 0) {
                        $module->children = $filteredChildren;
                        $filteredModules[] = $module;
                    }
                } else {
                    $filteredModules[] = $module;
                }
            }
        }

        return $filteredModules;
    }

    /**
     * Check if user has permission for a module
     */
    protected function hasModulePermission($module, $userPermissions)
    {
        // If module has children, permission check will be done on children
        // Parent will only show if it has accessible children
        if (isset($module->children) && count($module->children) > 0) {
            return true; // Will be filtered by children check
        }

        // If module doesn't have permission_name, deny access (for security)
        if (!isset($module->permission_name) || empty($module->permission_name)) {
            return false;
        }

        // Check if user has the required permission
        return in_array($module->permission_name, $userPermissions);
    }
}
