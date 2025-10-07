<?php

namespace App\Repositories\Contracts\Customers;

interface ProductReviewRepositoryInterface
{
    public function query();
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByProduct($productId);
    public function getByCustomer($customerId);
    public function getPending();
    public function getApproved();
    public function approve($id, $approvedBy);
    public function respond($id, $response, $respondedBy);
    public function getStatistics();
}
