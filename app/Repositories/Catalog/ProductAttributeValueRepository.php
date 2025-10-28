<?php

namespace App\Repositories\Catalog;

use App\Repositories\Contracts\Catalog\ProductAttributeValueRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeValueRepository implements ProductAttributeValueRepositoryInterface
{
    protected $tableName = 'product_attribute_values';

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
            ->join('product_attributes', 'product_attribute_values.product_attribute_id', '=', 'product_attributes.id')
            ->select(
                'product_attribute_values.*',
                'product_attributes.name as attribute_name',
                'product_attributes.slug as attribute_slug'
            )
            ->orderBy('product_attributes.sort_order', 'asc')
            ->orderBy('product_attribute_values.sort_order', 'asc')
            ->get();
    }

    public function getAllActive()
    {
        return $this->table()
            ->join('product_attributes', 'product_attribute_values.product_attribute_id', '=', 'product_attributes.id')
            ->where('product_attribute_values.is_active', true)
            ->where('product_attributes.is_active', true)
            ->select(
                'product_attribute_values.*',
                'product_attributes.name as attribute_name',
                'product_attributes.slug as attribute_slug'
            )
            ->orderBy('product_attributes.sort_order', 'asc')
            ->orderBy('product_attribute_values.sort_order', 'asc')
            ->get();
    }

    public function findById($id)
    {
        return $this->table()
            ->where('id', $id)
            ->first();
    }

    public function findBySlug($attributeId, $slug)
    {
        return $this->table()
            ->where('product_attribute_id', $attributeId)
            ->where('slug', $slug)
            ->first();
    }

    public function getByAttributeId($attributeId)
    {
        return $this->table()
            ->where('product_attribute_id', $attributeId)
            ->orderBy('sort_order', 'asc')
            ->orderBy('value', 'asc')
            ->get();
    }

    public function create(array $data)
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['value']);
        }

        // Parse meta if it's array
        if (isset($data['meta']) && is_array($data['meta'])) {
            $data['meta'] = json_encode($data['meta']);
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = $this->table()->insertGetId($data);

        return $this->findById($id);
    }

    public function update($id, array $data)
    {
        // Auto-update slug if value changed
        if (isset($data['value']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['value']);
        }

        // Parse meta if it's array
        if (isset($data['meta']) && is_array($data['meta'])) {
            $data['meta'] = json_encode($data['meta']);
        }

        $data['updated_at'] = now();

        $this->table()->where('id', $id)->update($data);

        return $this->findById($id);
    }

    public function delete($id)
    {
        // Check if value is being used in variants
        $isUsed = DB::table('product_variant_attributes')
            ->where('product_attribute_value_id', $id)
            ->exists();

        if ($isUsed) {
            throw new \Exception('Cannot delete attribute value that is being used by product variants.');
        }

        return $this->table()->where('id', $id)->delete();
    }

    public function bulkCreate($attributeId, array $values)
    {
        $created = [];

        foreach ($values as $index => $valueData) {
            if (is_string($valueData)) {
                $valueData = ['value' => $valueData];
            }

            $valueData['product_attribute_id'] = $attributeId;

            if (!isset($valueData['sort_order'])) {
                $valueData['sort_order'] = $index + 1;
            }

            $created[] = $this->create($valueData);
        }

        return $created;
    }
}
