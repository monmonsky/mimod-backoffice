<?php

namespace App\Repositories\Contracts;

interface PermissionRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllWithGroup();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByGroupId($groupId);
}
