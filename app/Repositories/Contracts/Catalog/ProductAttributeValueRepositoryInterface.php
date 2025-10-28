<?php

namespace App\Repositories\Contracts\Catalog;

interface ProductAttributeValueRepositoryInterface
{
    public function getAll();
    public function getAllActive();
    public function findById($id);
    public function findBySlug($attributeId, $slug);
    public function getByAttributeId($attributeId);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function bulkCreate($attributeId, array $values);
}
