<?php

namespace App\Repositories\Contracts\Customers;

interface CustomerGroupRepositoryInterface
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
    public function getMembers($groupId);
    public function addMember($groupId, $customerId);
    public function removeMember($groupId, $customerId);
    public function isMember($groupId, $customerId);
}
