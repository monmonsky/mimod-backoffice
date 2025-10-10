<?php

namespace App\Repositories\Contracts\AccessControl;

interface RoleRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllWithCounts();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleActive($id);
    public function getStatistics();
    public function syncModules($roleId, $modules);
    public function syncPermissions($roleId, $permissions);
    public function getRoleModules($roleId);
    public function getRolePermissions($roleId);
}
