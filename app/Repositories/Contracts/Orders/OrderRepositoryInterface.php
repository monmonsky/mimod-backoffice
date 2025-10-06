<?php

namespace App\Repositories\Contracts\Orders;

interface OrderRepositoryInterface
{
    public function query();
    public function getAll();
    public function getAllWithRelations();
    public function findById($id);
    public function findByIdWithRelations($id);
    public function findByOrderNumber($orderNumber);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function updateStatus($id, $status);
    public function getByStatus($status);
    public function getStatistics();
    public function getOrderItems($orderId);
    public function createOrderItem(array $data);
    public function updateOrderItem($itemId, array $data);
    public function deleteOrderItem($itemId);
}
