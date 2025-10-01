<?php

namespace App\Repositories\AccessControl;

use App\Repositories\Contracts\AccessControl\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleRepositoryInterface
{
    protected $tableName = 'roles';

    /**
     * Get fresh query builder instance
     */
    private function table()
    {
        return DB::table($this->tableName);
    }

    public function getAll()
    {
        return $this->table()->orderBy('priority', 'desc')->get();
    }

    public function getAllWithCounts()
    {
        return $this->table()
            ->leftJoin('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->select(
                'roles.*',
                DB::raw('COUNT(DISTINCT user_roles.user_id) as users_count')
            )
            ->groupBy(
                'roles.id',
                'roles.name',
                'roles.display_name',
                'roles.description',
                'roles.is_active',
                'roles.is_system',
                'roles.priority',
                'roles.created_at',
                'roles.updated_at'
            )
            ->orderBy('roles.priority', 'desc')
            ->get();
    }

    public function findById($id)
    {
        $role = $this->table()->where('id', $id)->first();

        if (!$role) {
            throw new \Exception("Role not found");
        }

        return $role;
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = $this->table()->insertGetId($data);

        return $this->table()->where('id', $id)->first();
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();

        $this->table()->where('id', $id)->update($data);

        return $this->table()->where('id', $id)->first();
    }

    public function delete($id)
    {
        // Check if role is system role
        $role = $this->findById($id);
        if ($role->is_system) {
            throw new \Exception("Cannot delete system role");
        }

        // Check if role has users
        $usersCount = DB::table('user_roles')->where('role_id', $id)->count();
        if ($usersCount > 0) {
            throw new \Exception("Cannot delete role with assigned users");
        }

        return $this->table()->where('id', $id)->delete();
    }

    public function toggleActive($id)
    {
        $role = $this->findById($id);

        if ($role->is_system) {
            throw new \Exception("Cannot toggle system role status");
        }

        $this->table()->where('id', $id)->update([
            'is_active' => !$role->is_active,
            'updated_at' => now()
        ]);

        return $this->table()->where('id', $id)->first();
    }

    public function getStatistics()
    {
        $total = $this->table()->count();
        $active = $this->table()->where('is_active', true)->count();
        $system = $this->table()->where('is_system', true)->count();
        $usersAssigned = DB::table('user_roles')->distinct('user_id')->count('user_id');

        return [
            'total' => $total,
            'active' => $active,
            'system' => $system,
            'users_assigned' => $usersAssigned
        ];
    }

    public function syncModules($roleId, $modules)
    {
        // Delete existing module assignments
        DB::table('role_modules')->where('role_id', $roleId)->delete();

        if (empty($modules)) {
            return;
        }

        // Insert new module assignments
        $inserts = [];
        foreach ($modules as $moduleData) {
            // Expected format: ['module_id' => 1, 'can_view' => true, 'can_create' => false, ...]
            $inserts[] = [
                'role_id' => $roleId,
                'module_id' => $moduleData['module_id'],
                'can_view' => $moduleData['can_view'] ?? false,
                'can_create' => $moduleData['can_create'] ?? false,
                'can_update' => $moduleData['can_update'] ?? false,
                'can_delete' => $moduleData['can_delete'] ?? false,
                'can_export' => $moduleData['can_export'] ?? false,
                'custom_permissions' => null,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        DB::table('role_modules')->insert($inserts);
    }

    public function syncPermissions($roleId, $permissions)
    {
        // Delete existing permission assignments
        DB::table('role_permissions')->where('role_id', $roleId)->delete();

        if (empty($permissions)) {
            return;
        }

        // Insert new permission assignments
        $inserts = [];
        foreach ($permissions as $permissionId) {
            $inserts[] = [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'granted_by' => null,
                'granted_at' => now(),
            ];
        }

        DB::table('role_permissions')->insert($inserts);
    }

    public function getRoleModules($roleId)
    {
        return DB::table('role_modules')
            ->join('modules', 'role_modules.module_id', '=', 'modules.id')
            ->where('role_modules.role_id', $roleId)
            ->select(
                'modules.id',
                'modules.name',
                'modules.display_name',
                'role_modules.can_view',
                'role_modules.can_create',
                'role_modules.can_update',
                'role_modules.can_delete',
                'role_modules.can_export'
            )
            ->get();
    }

    public function getRolePermissions($roleId)
    {
        return DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role_id', $roleId)
            ->select('permissions.id', 'permissions.name', 'permissions.display_name')
            ->get();
    }
}
