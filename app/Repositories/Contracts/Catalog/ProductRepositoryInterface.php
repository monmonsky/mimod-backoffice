<?php

namespace App\Repositories\Contracts\Catalog;

interface ProductRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllWithRelations();
    public function findById($id);
    public function findByIdWithRelations($id);
    public function findBySlug($slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleStatus($id, $status);
    public function toggleFeatured($id);
    public function getStatistics();
    public function syncCategories($productId, array $categoryIds);
    public function getProductVariants($productId);
    public function getProductImages($productId);
}
