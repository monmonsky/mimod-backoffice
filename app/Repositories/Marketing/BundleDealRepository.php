<?php

namespace App\Repositories\Marketing;

use App\Repositories\Contracts\Marketing\BundleDealRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BundleDealRepository implements BundleDealRepositoryInterface
{
    protected $tableName = 'bundle_deals';
    protected $itemsTable = 'bundle_deal_items';

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

    public function findBySlug($slug)
    {
        return $this->table()->where('slug', $slug)->first();
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
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addItem($bundleId, $productId, $quantity, $price)
    {
        $data = [
            'bundle_deal_id' => $bundleId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table($this->itemsTable)->insert($data);
    }

    public function removeItem($bundleId, $productId)
    {
        return DB::table($this->itemsTable)
            ->where('bundle_deal_id', $bundleId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function getItems($bundleId)
    {
        return DB::table($this->itemsTable . ' as bdi')
            ->join('products as p', 'bdi.product_id', '=', 'p.id')
            ->where('bdi.bundle_deal_id', $bundleId)
            ->select(
                'bdi.*',
                'p.name as product_name',
                'p.slug as product_slug'
            )
            ->get();
    }

    public function incrementSoldCount($bundleId, $quantity = 1)
    {
        $this->table()
            ->where('id', $bundleId)
            ->increment('sold_count', $quantity);
    }

    public function calculateOriginalPrice($bundleId)
    {
        $total = DB::table($this->itemsTable)
            ->where('bundle_deal_id', $bundleId)
            ->selectRaw('SUM(price * quantity) as total')
            ->first();

        return $total->total ?? 0;
    }

    public function getStatistics()
    {
        $totalBundles = $this->table()->count();
        $activeBundles = $this->table()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $totalSold = $this->table()->sum('sold_count');
        $totalRevenue = DB::table($this->tableName)
            ->selectRaw('SUM(bundle_price * sold_count) as total')
            ->first();

        return (object) [
            'total_bundles' => $totalBundles,
            'active_bundles' => $activeBundles,
            'total_sold' => $totalSold ?? 0,
            'total_revenue' => $totalRevenue->total ?? 0,
        ];
    }
}
