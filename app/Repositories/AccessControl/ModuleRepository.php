<?php

namespace App\Repositories\AccessControl;

use App\Repositories\Contracts\AccessControl\ModuleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected $tableName = 'modules';

    /**
     * Get fresh query builder instance
     */
    private function table()
    {
        return DB::table($this->tableName);
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()->orderBy('sort_order')->get();
    }

    public function getAllWithChildren()
    {
        $parents = $this->table()->whereNull('parent_id')->orderBy('sort_order')->get();

        foreach ($parents as $parent) {
            $parent->children = $this->table()
                ->where('parent_id', $parent->id)
                ->orderBy('sort_order')
                ->get();
        }

        return $parents;
    }

    public function getParents()
    {
        return $this->table()->whereNull('parent_id')->orderBy('sort_order')->get();
    }

    public function getActive()
    {
        return $this->table()->where('is_active', true)->orderBy('sort_order')->get();
    }

    public function getVisible()
    {
        return $this->table()->where('is_visible', true)->orderBy('sort_order')->get();
    }

    public function findById($id)
    {
        $module = $this->table()->where('id', $id)->first();

        if (!$module) {
            throw new \Exception("Module not found");
        }

        // Load parent
        if ($module->parent_id) {
            $module->parent = $this->table()->where('id', $module->parent_id)->first();
        }

        // Load children
        $module->children = $this->table()->where('parent_id', $module->id)->orderBy('sort_order')->get();

        return $module;
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

    public function updateSortOrder($id, $sortOrder)
    {
        $this->table()->where('id', $id)->update([
            'sort_order' => $sortOrder,
            'updated_at' => now()
        ]);

        return $this->table()->where('id', $id)->first();
    }

    public function toggleActive($id)
    {
        $module = $this->table()->where('id', $id)->first();

        if (!$module) {
            throw new \Exception("Module not found");
        }

        $this->table()->where('id', $id)->update([
            'is_active' => !$module->is_active,
            'updated_at' => now()
        ]);

        return $this->table()->where('id', $id)->first();
    }

    public function toggleVisible($id)
    {
        $module = $this->table()->where('id', $id)->first();

        if (!$module) {
            throw new \Exception("Module not found");
        }

        $this->table()->where('id', $id)->update([
            'is_visible' => !$module->is_visible,
            'updated_at' => now()
        ]);

        return $this->table()->where('id', $id)->first();
    }

    public function getStatistics()
    {
        $total = $this->table()->count();
        $active = $this->table()->where('is_active', true)->count();
        $visible = $this->table()->where('is_visible', true)->count();
        $parents = $this->table()->whereNull('parent_id')->count();

        return [
            'total' => $total,
            'active' => $active,
            'visible' => $visible,
            'parents' => $parents
        ];
    }

    /**
     * Update sort order for all modules in a group
     * Multiplies by 10 to create gaps between groups for future insertions
     */
    public function updateGroupSortOrder($groupName, $baseSortOrder)
    {
        // Get all modules in this group
        $modules = $this->table()
            ->where('group_name', $groupName)
            ->orderBy('sort_order')
            ->get();

        // Update each module with incremental sort_order
        foreach ($modules as $index => $module) {
            $this->table()->where('id', $module->id)->update([
                'sort_order' => $baseSortOrder + $index,
                'updated_at' => now()
            ]);
        }

        return true;
    }
}
