<?php

namespace App\Repositories;

use App\Repositories\Contracts\PermissionGroupRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PermissionGroupRepository implements PermissionGroupRepositoryInterface
{
    protected $tableName = 'permission_groups';

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

    public function getAllWithCount()
    {
        return $this->table()
            ->leftJoin('permission_group_items', 'permission_groups.id', '=', 'permission_group_items.group_id')
            ->select(
                'permission_groups.*',
                DB::raw('COUNT(permission_group_items.permission_id) as permissions_count')
            )
            ->groupBy(
                'permission_groups.id',
                'permission_groups.name',
                'permission_groups.display_name',
                'permission_groups.description',
                'permission_groups.is_active',
                'permission_groups.created_at',
                'permission_groups.updated_at'
            )
            ->orderBy('permission_groups.name')
            ->get();
    }

    public function findById($id)
    {
        $group = $this->table()->where('id', $id)->first();

        if (!$group) {
            throw new \Exception("Permission group not found");
        }

        return $group;
    }

    public function findByIdWithCount($id)
    {
        $group = $this->table()
            ->leftJoin('permission_group_items', 'permission_groups.id', '=', 'permission_group_items.group_id')
            ->select(
                'permission_groups.*',
                DB::raw('COUNT(permission_group_items.permission_id) as permissions_count')
            )
            ->where('permission_groups.id', $id)
            ->groupBy(
                'permission_groups.id',
                'permission_groups.name',
                'permission_groups.display_name',
                'permission_groups.description',
                'permission_groups.is_active',
                'permission_groups.created_at',
                'permission_groups.updated_at'
            )
            ->first();

        if (!$group) {
            throw new \Exception("Permission group not found");
        }

        return $group;
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

    public function hasPermissions($id)
    {
        $count = DB::table('permission_group_items')
            ->where('group_id', $id)
            ->count();
        return $count > 0;
    }
}
