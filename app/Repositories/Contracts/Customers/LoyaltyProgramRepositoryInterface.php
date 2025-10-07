<?php

namespace App\Repositories\Contracts\Customers;

interface LoyaltyProgramRepositoryInterface
{
    public function query();
    public function getAll();
    public function findById($id);
    public function findByCode($code);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getActive();
    public function getStatistics();
}
