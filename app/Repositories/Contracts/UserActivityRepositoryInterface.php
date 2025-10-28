<?php

namespace App\Repositories\Contracts;

interface UserActivityRepositoryInterface
{
    public function query();
    public function getAll($filters = []);
    public function findById($id);
    public function create(array $data);
    public function clearLogs();
    public function getStatistics();
    public function exportLogs($filters = []);
}
