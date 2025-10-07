<?php

namespace App\Repositories\Contracts\Marketing;

interface BundleDealRepositoryInterface
{
    public function query();
    public function getAll();
    public function findById($id);
    public function findBySlug($slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getActive();
    public function addItem($bundleId, $productId, $quantity, $price);
    public function removeItem($bundleId, $productId);
    public function getItems($bundleId);
    public function incrementSoldCount($bundleId, $quantity = 1);
    public function calculateOriginalPrice($bundleId);
    public function getStatistics();
}
