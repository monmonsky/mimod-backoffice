<?php

namespace App\Repositories\Customers;

use App\Repositories\Contracts\Customers\CustomerSegmentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerSegmentRepository implements CustomerSegmentRepositoryInterface
{
    protected $tableName = 'customer_segments';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function findByCode($code)
    {
        return $this->table()->where('code', $code)->first();
    }

    public function create(array $data)
    {
        $id = $this->table()->insertGetId($data);
        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();
        $this->table()->where('id', $id)->update($data);
        return $this->findById($id);
    }

    public function delete($id)
    {
        return $this->table()->where('id', $id)->delete();
    }

    public function getActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAutoAssign()
    {
        return $this->table()
            ->where('is_auto_assign', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStatistics()
    {
        $totalSegments = $this->table()->count();
        $activeSegments = $this->table()->where('is_active', true)->count();
        $autoAssignSegments = $this->table()->where('is_auto_assign', true)->count();
        $totalCustomersInSegments = $this->table()->sum('customer_count');

        return (object) [
            'total_segments' => $totalSegments,
            'active_segments' => $activeSegments,
            'auto_assign_segments' => $autoAssignSegments,
            'total_customers_in_segments' => $totalCustomersInSegments,
        ];
    }
}
