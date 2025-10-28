<?php

namespace App\Repositories\Contracts\Catalog;

interface ProductAttributeRepositoryInterface
{
    public function getAll();
    public function getAllActive();
    public function findById($id);
    public function findBySlug($slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getWithValues($id);
}
