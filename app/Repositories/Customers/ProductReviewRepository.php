<?php

namespace App\Repositories\Customers;

use App\Repositories\Contracts\Customers\ProductReviewRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductReviewRepository implements ProductReviewRepositoryInterface
{
    protected $tableName = 'product_reviews';

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

    public function getByProduct($productId)
    {
        return $this->table()
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByCustomer($customerId)
    {
        return $this->table()
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPending()
    {
        return $this->table()
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getApproved()
    {
        return $this->table()
            ->where('is_approved', true)
            ->orderBy('approved_at', 'desc')
            ->get();
    }

    public function approve($id, $approvedBy)
    {
        $data = [
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approvedBy,
            'updated_at' => now(),
        ];

        $this->table()->where('id', $id)->update($data);
        return $this->findById($id);
    }

    public function respond($id, $response, $respondedBy)
    {
        $data = [
            'admin_response' => $response,
            'responded_at' => now(),
            'updated_at' => now(),
        ];

        $this->table()->where('id', $id)->update($data);
        return $this->findById($id);
    }

    public function getStatistics()
    {
        $totalReviews = $this->table()->count();
        $pendingReviews = $this->table()->where('is_approved', false)->count();
        $approvedReviews = $this->table()->where('is_approved', true)->count();
        $averageRating = $this->table()->avg('rating');

        return (object) [
            'total_reviews' => $totalReviews,
            'pending_reviews' => $pendingReviews,
            'approved_reviews' => $approvedReviews,
            'average_rating' => $averageRating ?? 0,
        ];
    }
}
