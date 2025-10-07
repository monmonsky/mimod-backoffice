<?php

namespace App\Repositories\Customers;

use App\Repositories\Contracts\Customers\LoyaltyProgramRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramRepository implements LoyaltyProgramRepositoryInterface
{
    protected $tableName = 'loyalty_programs';

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
        $data['created_at'] = now();
        $data['updated_at'] = now();
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
            ->where(function($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStatistics()
    {
        $activePrograms = $this->table()
            ->where('is_active', true)
            ->count();

        $totalPointsIssued = DB::table('loyalty_transactions')
            ->whereIn('transaction_type', ['earn', 'adjust'])
            ->where('points', '>', 0)
            ->sum('points');

        $totalPointsRedeemed = DB::table('loyalty_transactions')
            ->where('transaction_type', 'redeem')
            ->sum(DB::raw('ABS(points)'));

        $activeMembers = DB::table('customers')
            ->where('loyalty_points', '>', 0)
            ->count();

        return (object) [
            'active_programs' => $activePrograms,
            'total_points_issued' => $totalPointsIssued ?? 0,
            'total_points_redeemed' => $totalPointsRedeemed ?? 0,
            'active_members' => $activeMembers,
        ];
    }
}
