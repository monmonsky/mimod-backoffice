<?php

namespace App\Repositories;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected $tableName = 'permissions';

    /**
     * Get fresh query builder instance
     */
    private function table()
    {
        return DB::table($this->tableName);
    }

    public function getAll()
    {
        return $this->table()->orderBy('name')->get();
    }

    public function getAllWithGroup()
    {
        return $this->table()
            ->leftJoin('permission_group_items', 'permissions.id', '=', 'permission_group_items.permission_id')
            ->leftJoin('permission_groups', 'permission_group_items.group_id', '=', 'permission_groups.id')
            ->select(
                'permissions.*',
                'permission_groups.id as group_id',
                'permission_groups.name as group_name',
                'permission_groups.display_name as group_display_name',
                'permission_groups.description as group_description'
            )
            ->orderBy('permissions.name')
            ->get()
            ->map(function ($permission) {
                // Add permissionGroup object for consistency with relationships
                if ($permission->group_name) {
                    $permission->permissionGroup = (object) [
                        'id' => $permission->group_id,
                        'name' => $permission->group_name,
                        'display_name' => $permission->group_display_name,
                        'description' => $permission->group_description
                    ];
                } else {
                    $permission->permissionGroup = null;
                }
                return $permission;
            });
    }

    public function findById($id)
    {
        $permission = $this->table()->where('id', $id)->first();

        if (!$permission) {
            throw new \Exception("Permission not found");
        }

        return $permission;
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
        return $this->table()->where('id', $id)->delete();
    }

    public function getByGroupId($groupId)
    {
        return $this->table()
            ->join('permission_group_items', 'permissions.id', '=', 'permission_group_items.permission_id')
            ->where('permission_group_items.group_id', $groupId)
            ->select('permissions.*')
            ->orderBy('permissions.name')
            ->get();
    }

    public function getStatistics()
    {
        $total = $this->table()->count();

        $grouped = $this->table()
            ->join('permission_group_items', 'permissions.id', '=', 'permission_group_items.permission_id')
            ->distinct()
            ->count('permissions.id');

        $ungrouped = $total - $grouped;

        return [
            'total' => $total,
            'grouped' => $grouped,
            'ungrouped' => $ungrouped
        ];
    }
}
