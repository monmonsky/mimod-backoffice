<?php

namespace App\Repositories\Orders;

use App\Repositories\Contracts\Orders\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    protected function table()
    {
        return DB::table('orders');
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()
            ->select('orders.*', 'customers.name as customer_name', 'customers.email as customer_email')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
    }

    public function getAllWithRelations()
    {
        $orders = $this->getAll();

        if ($orders->isEmpty()) {
            return $orders;
        }

        $orderIds = $orders->pluck('id')->toArray();

        // Batch load order items with product info
        $orderItemsMap = DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->whereIn('order_items.order_id', $orderIds)
            ->select(
                'order_items.*',
                'products.name as product_name',
                'product_variants.sku',
                'product_variants.size',
                'product_variants.color'
            )
            ->get()
            ->groupBy('order_id');

        // Batch load items count and total
        $orderStats = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->select(
                'order_id',
                DB::raw('COUNT(*) as items_count'),
                DB::raw('SUM(quantity) as total_items')
            )
            ->groupBy('order_id')
            ->get()
            ->keyBy('order_id');

        // Assign data to orders
        foreach ($orders as $order) {
            $order->items = isset($orderItemsMap[$order->id])
                ? $orderItemsMap[$order->id]->toArray()
                : [];

            $stats = $orderStats[$order->id] ?? null;
            $order->items_count = $stats->items_count ?? 0;
            $order->total_items = $stats->total_items ?? 0;
        }

        return $orders;
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
    }

    public function findByIdWithRelations($id)
    {
        $order = $this->table()
            ->select('orders.*', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.phone as customer_phone')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return null;
        }

        // Load order items
        $order->items = DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('order_items.order_id', $id)
            ->select(
                'order_items.*',
                'products.name as product_name',
                'product_variants.sku',
                'product_variants.size',
                'product_variants.color'
            )
            ->get()
            ->toArray();

        return $order;
    }

    public function findByOrderNumber($orderNumber)
    {
        return $this->table()
            ->where('order_number', $orderNumber)
            ->first();
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

    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getByStatus($status)
    {
        return $this->table()
            ->select('orders.*', 'customers.name as customer_name', 'customers.email as customer_email')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.status', $status)
            ->orderBy('orders.created_at', 'desc')
            ->get();
    }

    public function getAllWithRelationsPaginated(array $filters = [], $perPage = 15)
    {
        $query = $this->table()
            ->select('orders.*', 'customers.name as customer_name', 'customers.email as customer_email')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id');

        // Apply filters
        if (!empty($filters['order_number'])) {
            $query->where('orders.order_number', 'like', '%' . $filters['order_number'] . '%');
        }

        if (!empty($filters['customer'])) {
            $query->where(function($q) use ($filters) {
                $q->where('customers.name', 'like', '%' . $filters['customer'] . '%')
                  ->orWhere('customers.email', 'like', '%' . $filters['customer'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('orders.status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('orders.payment_status', $filters['payment_status']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('orders.payment_method', $filters['payment_method']);
        }

        if (!empty($filters['tracking'])) {
            $query->where('orders.tracking_number', 'like', '%' . $filters['tracking'] . '%');
        }

        if (!empty($filters['courier'])) {
            $query->where('orders.courier', $filters['courier']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('orders.created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('orders.created_at', '<=', $filters['date_to']);
        }

        // Filter by min/max total
        if (!empty($filters['min_total'])) {
            $query->where('orders.total', '>=', $filters['min_total']);
        }

        if (!empty($filters['max_total'])) {
            $query->where('orders.total', '<=', $filters['max_total']);
        }

        // Search across multiple fields
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('orders.order_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('customers.name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('customers.email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('orders.tracking_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        // Validate sort fields
        $allowedSortFields = ['created_at', 'updated_at', 'order_number', 'total', 'status', 'payment_status'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy('orders.' . $sortBy, $sortOrder);
        } else {
            $query->orderBy('orders.created_at', 'desc');
        }

        // Paginate
        $orders = $query->paginate($perPage)
                       ->appends(request()->query());

        if ($orders->isEmpty()) {
            return $orders;
        }

        $orderIds = $orders->pluck('id')->toArray();

        // Batch load items count
        $orderStats = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->select(
                'order_id',
                DB::raw('COUNT(*) as items_count'),
                DB::raw('SUM(quantity) as total_items')
            )
            ->groupBy('order_id')
            ->get()
            ->keyBy('order_id');

        // Assign data to orders
        foreach ($orders as $order) {
            $stats = $orderStats[$order->id] ?? null;
            $order->items_count = $stats->items_count ?? 0;
            $order->total_items = $stats->total_items ?? 0;
        }

        return $orders;
    }

    public function getStatistics()
    {
        return DB::table('orders')
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN status = \'processing\' THEN 1 ELSE 0 END) as processing_count'),
                DB::raw('SUM(CASE WHEN status = \'shipped\' THEN 1 ELSE 0 END) as shipped_count'),
                DB::raw('SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('SUM(CASE WHEN status = \'cancelled\' THEN 1 ELSE 0 END) as cancelled_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->first();
    }

    public function getOrderItems($orderId)
    {
        return DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->select(
                'order_items.*',
                'products.name as product_name',
                'product_variants.sku',
                'product_variants.size',
                'product_variants.color'
            )
            ->get();
    }

    public function createOrderItem(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('order_items')->insertGetId($data);
    }

    public function updateOrderItem($itemId, array $data)
    {
        $data['updated_at'] = now();
        return DB::table('order_items')->where('id', $itemId)->update($data);
    }

    public function deleteOrderItem($itemId)
    {
        return DB::table('order_items')->where('id', $itemId)->delete();
    }
}
