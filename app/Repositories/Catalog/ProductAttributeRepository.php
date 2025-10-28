<?php

namespace App\Repositories\Catalog;

use App\Repositories\Contracts\Catalog\ProductAttributeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeRepository implements ProductAttributeRepositoryInterface
{
    protected $tableName = 'product_attributes';

    public function table()
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
            ->select(
                'product_attributes.*',
                DB::raw('(SELECT COUNT(*) FROM product_attribute_values WHERE product_attribute_values.product_attribute_id = product_attributes.id) as values_count')
            )
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllActive()
    {
        return $this->table()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllWithValues($activeOnly = false)
    {
        $query = $this->table()
            ->select(
                'product_attributes.*',
                DB::raw('(SELECT COUNT(*) FROM product_attribute_values WHERE product_attribute_values.product_attribute_id = product_attributes.id) as values_count')
            )
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        $attributes = $query->get();

        // Load values for each attribute
        foreach ($attributes as $attribute) {
            $valuesQuery = DB::table('product_attribute_values')
                ->where('product_attribute_id', $attribute->id)
                ->orderBy('sort_order', 'asc')
                ->orderBy('value', 'asc');

            if ($activeOnly) {
                $valuesQuery->where('is_active', true);
            }

            $attribute->values = $valuesQuery->get();
        }

        return $attributes;
    }

    public function findById($id)
    {
        return $this->table()
            ->where('id', $id)
            ->first();
    }

    public function findBySlug($slug)
    {
        return $this->table()
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data)
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = $this->table()->insertGetId($data);

        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        // Auto-update slug if name changed
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['updated_at'] = now();

        $this->table()->where('id', $id)->update($data);

        return $this->findById($id);
    }

    public function delete($id)
    {
        // Check if attribute has values
        $hasValues = DB::table('product_attribute_values')
            ->where('product_attribute_id', $id)
            ->exists();

        if ($hasValues) {
            throw new \Exception('Cannot delete attribute with existing values. Please delete all values first.');
        }

        return $this->table()->where('id', $id)->delete();
    }

    public function getWithValues($id)
    {
        $attribute = $this->findById($id);

        if (!$attribute) {
            return null;
        }

        $attribute->values = DB::table('product_attribute_values')
            ->where('product_attribute_id', $id)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('value', 'asc')
            ->get();

        return $attribute;
    }
}
