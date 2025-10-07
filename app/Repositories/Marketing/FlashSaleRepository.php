<?php

namespace App\Repositories\Marketing;

use App\Repositories\Contracts\Marketing\FlashSaleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class FlashSaleRepository implements FlashSaleRepositoryInterface
{
    protected $tableName = 'flash_sales';
    protected $productsTable = 'flash_sale_products';

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

    public function getActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUpcoming()
    {
        return $this->table()
            ->where('is_active', true)
            ->where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->get();
    }

    public function getExpired()
    {
        return $this->table()
            ->where('end_time', '<', now())
            ->orderBy('end_time', 'desc')
            ->get();
    }

    public function addProduct($flashSaleId, $productId, array $data)
    {
        $data['flash_sale_id'] = $flashSaleId;
        $data['product_id'] = $productId;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table($this->productsTable)->insert($data);
    }

    public function removeProduct($flashSaleId, $productId)
    {
        return DB::table($this->productsTable)
            ->where('flash_sale_id', $flashSaleId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function getProducts($flashSaleId)
    {
        return DB::table($this->productsTable . ' as fsp')
            ->join('products as p', 'fsp.product_id', '=', 'p.id')
            ->where('fsp.flash_sale_id', $flashSaleId)
            ->select(
                'fsp.*',
                'p.name as product_name',
                'p.slug as product_slug'
            )
            ->get();
    }

    public function incrementSoldCount($flashSaleProductId, $quantity = 1)
    {
        DB::table($this->productsTable)
            ->where('id', $flashSaleProductId)
            ->increment('sold_count', $quantity);
    }

    public function getStatistics()
    {
        $totalFlashSales = $this->table()->count();
        $activeFlashSales = $this->table()
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();
        $upcomingFlashSales = $this->table()
            ->where('is_active', true)
            ->where('start_time', '>', now())
            ->count();
        $totalProducts = DB::table($this->productsTable)->count();

        return (object) [
            'total_flash_sales' => $totalFlashSales,
            'active_flash_sales' => $activeFlashSales,
            'upcoming_flash_sales' => $upcomingFlashSales,
            'total_products' => $totalProducts,
        ];
    }
}
