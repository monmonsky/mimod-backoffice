<?php

namespace App\Repositories\Contracts\Marketing;

interface FlashSaleRepositoryInterface
{
    public function query();
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getActive();
    public function getUpcoming();
    public function getExpired();
    public function addProduct($flashSaleId, $productId, array $data);
    public function removeProduct($flashSaleId, $productId);
    public function getProducts($flashSaleId);
    public function incrementSoldCount($flashSaleProductId, $quantity = 1);
    public function getStatistics();
}
