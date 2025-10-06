<?php

namespace App\Repositories\Contracts\Catalog;

interface BrandRepositoryInterface
{
    public function getAll();
    public function getAllActive();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleActive($id);
    public function updateSortOrder($id, $sortOrder);
    public function getStatistics();
    public function hasProducts($id);
}
