<?php

namespace App\Repositories\Contracts;

interface PermissionGroupRepositoryInterface
{
    public function getAll();
    public function getAllWithCount();
    public function findById($id);
    public function findByIdWithCount($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function hasPermissions($id);
}
