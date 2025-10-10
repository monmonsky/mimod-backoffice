<?php

namespace App\Repositories\Contracts\AccessControl;

interface ModuleRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllWithChildren();
    public function getParents();
    public function getActive();
    public function getVisible();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function updateSortOrder($id, $sortOrder);
    public function updateGroupSortOrder($groupName, $baseSortOrder);
    public function toggleActive($id);
    public function toggleVisible($id);
}
