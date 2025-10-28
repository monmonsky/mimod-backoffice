# Product Attributes System - API Documentation

## Overview

Product Attributes System memungkinkan Anda untuk membuat variant produk secara dinamis dan flexible menggunakan attributes seperti Size, Color, Material, dll.

## Architecture

```
product_attributes (Size, Color, Material)
    ↓
product_attribute_values (5-6 tahun, Bright Pink, Cotton)
    ↓
product_variant_attributes (pivot: variant + attribute + value)
    ↓
product_variants (SKU, Price, Stock, etc)
```

---

## 1. Product API with Attributes

### 1.1 Get Product with Variants & Attributes

**Endpoint:** `GET /api/catalog/products/{id}`

**Response Example:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Product retrieved successfully",
  "data": {
    "id": 9,
    "brand_id": 1,
    "name": "Minimoda Breezy Dress",
    "slug": "minimoda-breezy-dress",
    "description": "Minimoda Breezy Dress | Dress Anak Perempuan | Gamis anak",
    "status": "active",
    "brand": {
      "id": 1,
      "name": "Minimoda",
      "slug": "minimoda"
    },
    "categories": [...],
    "variants": [
      {
        "id": 284,
        "product_id": 9,
        "sku": "MM-PQJB9B-bright-pink-5-6",
        "barcode": "8990036913119",
        "size": "5-6 tahun",
        "color": "Bright Pink",
        "weight_gram": 200,
        "price": 179550,
        "compare_at_price": 315000,
        "stock_quantity": 10,
        "reserved_quantity": 0,
        "images": [
          {
            "id": 123,
            "variant_id": 284,
            "url": "https://ftp.example.com/products/breezy-dress/variants/image.jpg",
            "is_primary": true,
            "sort_order": 1
          }
        ],
        "attributes": [
          {
            "pivot_id": 45,
            "attribute_id": 1,
            "attribute_name": "Size",
            "attribute_slug": "size",
            "attribute_type": "select",
            "value_id": 1,
            "value": "5-6 tahun",
            "value_slug": "5-6",
            "meta": null
          },
          {
            "pivot_id": 46,
            "attribute_id": 2,
            "attribute_name": "Color",
            "attribute_slug": "color",
            "attribute_type": "color",
            "value_id": 5,
            "value": "Bright Pink",
            "value_slug": "bright-pink",
            "meta": {
              "hex": "#FF69B4"
            }
          }
        ]
      },
      {
        "id": 285,
        "product_id": 9,
        "sku": "MM-XYZABC-merah-muda-7-8",
        "size": "7-8 tahun",
        "color": "Merah Muda",
        "price": 179550,
        "compare_at_price": 315000,
        "stock_quantity": 10,
        "images": [...],
        "attributes": [...]
      }
      // ... 10 more variants
    ]
  }
}
```

### 1.2 Get Product Variants

**Endpoint:** `GET /api/catalog/products/{id}/variants`

Returns the same variants array structure as above with attributes included.

---

## 2. Attributes Management API

Base URL: `/api/catalog/attributes`

### 2.1 Get All Attributes

**Endpoint:** `GET /api/catalog/attributes`

**Query Parameters:**
- `active_only` (boolean, optional) - Filter hanya attributes yang aktif

**Request Example:**
```bash
GET /api/catalog/attributes?active_only=true
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Attributes retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Size",
      "slug": "size",
      "type": "select",
      "description": "Product size/ukuran",
      "is_required": true,
      "is_active": true,
      "sort_order": 1,
      "values_count": 4,
      "created_at": "2025-10-25T00:00:00.000000Z",
      "updated_at": "2025-10-25T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Color",
      "slug": "color",
      "type": "color",
      "description": "Product color/warna",
      "is_required": false,
      "is_active": true,
      "sort_order": 2,
      "values_count": 8,
      "created_at": "2025-10-25T00:00:00.000000Z",
      "updated_at": "2025-10-25T00:00:00.000000Z"
    }
  ]
}
```

### 2.2 Get Attribute by ID (with Values)

**Endpoint:** `GET /api/catalog/attributes/{id}`

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Attribute retrieved successfully",
  "data": {
    "id": 1,
    "name": "Size",
    "slug": "size",
    "type": "select",
    "description": "Product size/ukuran",
    "is_required": true,
    "is_active": true,
    "sort_order": 1,
    "values": [
      {
        "id": 1,
        "product_attribute_id": 1,
        "value": "5-6 tahun",
        "slug": "5-6",
        "meta": null,
        "is_active": true,
        "sort_order": 1
      },
      {
        "id": 2,
        "product_attribute_id": 1,
        "value": "7-8 tahun",
        "slug": "7-8",
        "meta": null,
        "is_active": true,
        "sort_order": 2
      }
    ]
  }
}
```

### 2.3 Create Attribute

**Endpoint:** `POST /api/catalog/attributes`

**Request Body:**
```json
{
  "name": "Material",
  "slug": "material",
  "type": "select",
  "description": "Product material",
  "is_required": false,
  "is_active": true,
  "sort_order": 3
}
```

**Validation Rules:**
- `name`: required, string, max 100
- `slug`: optional, string, max 100, unique
- `type`: required, enum: `select`, `color`, `radio`, `checkbox`
- `description`: optional, string
- `is_required`: optional, boolean
- `is_active`: optional, boolean
- `sort_order`: optional, integer

**Response:**
```json
{
  "status": true,
  "statusCode": "201",
  "message": "Attribute created successfully",
  "data": {
    "id": 3,
    "name": "Material",
    "slug": "material",
    ...
  }
}
```

### 2.4 Update Attribute

**Endpoint:** `PUT /api/catalog/attributes/{id}`

**Request Body:** (semua field optional)
```json
{
  "name": "Size Updated",
  "is_active": false
}
```

### 2.5 Delete Attribute

**Endpoint:** `DELETE /api/catalog/attributes/{id}`

**Note:** Attribute tidak bisa dihapus jika masih memiliki values.

---

## 3. Attribute Values Management API

Base URL: `/api/catalog/attribute-values`

### 3.1 Get All Attribute Values

**Endpoint:** `GET /api/catalog/attribute-values`

**Query Parameters:**
- `attribute_id` (integer, optional) - Filter by attribute ID
- `active_only` (boolean, optional) - Filter hanya values yang aktif

**Request Example:**
```bash
GET /api/catalog/attribute-values?attribute_id=1
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Attribute values retrieved successfully",
  "data": [
    {
      "id": 1,
      "product_attribute_id": 1,
      "value": "5-6 tahun",
      "slug": "5-6",
      "meta": null,
      "is_active": true,
      "sort_order": 1,
      "attribute_name": "Size",
      "attribute_slug": "size"
    }
  ]
}
```

### 3.2 Create Single Attribute Value

**Endpoint:** `POST /api/catalog/attribute-values`

**Request Body:**
```json
{
  "product_attribute_id": 2,
  "value": "Navy Blue",
  "slug": "navy-blue",
  "meta": {
    "hex": "#000080"
  },
  "is_active": true,
  "sort_order": 9
}
```

**Validation Rules:**
- `product_attribute_id`: required, exists in product_attributes
- `value`: required, string, max 100
- `slug`: optional, string, max 100
- `meta`: optional, object (untuk color hex, image URL, dll)
- `is_active`: optional, boolean
- `sort_order`: optional, integer

### 3.3 Bulk Create Attribute Values

**Endpoint:** `POST /api/catalog/attribute-values/bulk`

**Request Body:**
```json
{
  "product_attribute_id": 3,
  "values": [
    {
      "value": "Cotton",
      "slug": "cotton",
      "meta": null,
      "sort_order": 1
    },
    {
      "value": "Polyester",
      "slug": "polyester",
      "meta": null,
      "sort_order": 2
    },
    {
      "value": "Wool",
      "slug": "wool",
      "meta": null,
      "sort_order": 3
    }
  ]
}
```

**Atau Format Sederhana:**
```json
{
  "product_attribute_id": 3,
  "values": ["Cotton", "Polyester", "Wool"]
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "201",
  "message": "Attribute values created successfully",
  "data": [
    {
      "id": 13,
      "product_attribute_id": 3,
      "value": "Cotton",
      "slug": "cotton",
      ...
    },
    ...
  ]
}
```

### 3.4 Update Attribute Value

**Endpoint:** `PUT /api/catalog/attribute-values/{id}`

**Request Body:**
```json
{
  "value": "Navy Blue Updated",
  "meta": {
    "hex": "#000090"
  }
}
```

### 3.5 Delete Attribute Value

**Endpoint:** `DELETE /api/catalog/attribute-values/{id}`

**Note:** Value tidak bisa dihapus jika sedang digunakan oleh variant.

---

## 4. Frontend Integration Guide

### 4.1 Setup: Fetch Available Attributes

Saat user membuka form create/edit product, fetch semua attributes yang tersedia:

```javascript
// Fetch all active attributes with their values
async function fetchAttributes() {
  const response = await fetch('/api/catalog/attributes?active_only=true');
  const result = await response.json();

  // For each attribute, fetch its values
  const attributesWithValues = await Promise.all(
    result.data.map(async (attr) => {
      const valuesResponse = await fetch(`/api/catalog/attributes/${attr.id}`);
      const valuesResult = await valuesResponse.json();
      return valuesResult.data;
    })
  );

  return attributesWithValues;
}
```

**Example Response:**
```javascript
[
  {
    id: 1,
    name: "Size",
    slug: "size",
    type: "select",
    is_required: true,
    values: [
      { id: 1, value: "5-6 tahun", slug: "5-6" },
      { id: 2, value: "7-8 tahun", slug: "7-8" },
      { id: 3, value: "9-10 tahun", slug: "9-10" },
      { id: 4, value: "11-12 tahun", slug: "11-12" }
    ]
  },
  {
    id: 2,
    name: "Color",
    slug: "color",
    type: "color",
    is_required: false,
    values: [
      { id: 5, value: "Bright Pink", slug: "bright-pink", meta: { hex: "#FF69B4" } },
      { id: 6, value: "Merah Muda", slug: "merah-muda", meta: { hex: "#FFB6C1" } },
      { id: 7, value: "Putih", slug: "putih", meta: { hex: "#FFFFFF" } }
    ]
  }
]
```

### 4.2 Step 1: Select Attributes untuk Product

UI untuk memilih attributes mana yang akan digunakan untuk product ini:

```javascript
// User selects which attributes to use
const selectedAttributes = [
  {
    attributeId: 1,  // Size
    selectedValues: [1, 2, 3, 4]  // All sizes
  },
  {
    attributeId: 2,  // Color
    selectedValues: [5, 6, 7]  // Bright Pink, Merah Muda, Putih
  }
];
```

### 4.3 Step 2: Generate Variant Combinations

Generate semua kombinasi possible dari attributes yang dipilih:

```javascript
function generateVariantCombinations(selectedAttributes, attributesData) {
  // Get attribute data
  const attributes = selectedAttributes.map(sel => {
    const attr = attributesData.find(a => a.id === sel.attributeId);
    return {
      ...attr,
      selectedValues: attr.values.filter(v => sel.selectedValues.includes(v.id))
    };
  });

  // Generate cartesian product
  function cartesianProduct(arrays) {
    return arrays.reduce((acc, curr) => {
      return acc.flatMap(a => curr.map(b => [...a, b]));
    }, [[]]);
  }

  const valueCombinations = cartesianProduct(
    attributes.map(attr => attr.selectedValues)
  );

  // Create variant objects
  return valueCombinations.map((combination, index) => {
    // Generate variant name
    const variantName = combination.map(v => v.value).join(' - ');

    // Generate SKU suggestion
    const skuParts = combination.map(v => v.slug.toUpperCase().replace('-', ''));
    const sku = `MM-${skuParts.join('-')}`;

    return {
      tempId: `temp_${index}`,  // Temporary ID untuk frontend
      name: variantName,
      sku: sku,
      attributes: combination.map((val, idx) => ({
        attributeId: attributes[idx].id,
        attributeName: attributes[idx].name,
        valueId: val.id,
        value: val.value
      })),
      price: 0,  // Will be filled by user
      compare_at_price: null,
      stock_quantity: 0,  // Will be filled by user
      weight_gram: 200,  // Default
      barcode: '',  // Will be generated or filled
      images: []
    };
  });
}
```

**Example Output:**
```javascript
[
  {
    tempId: "temp_0",
    name: "5-6 tahun - Bright Pink",
    sku: "MM-56-BRIGHTPINK",
    attributes: [
      { attributeId: 1, attributeName: "Size", valueId: 1, value: "5-6 tahun" },
      { attributeId: 2, attributeName: "Color", valueId: 5, value: "Bright Pink" }
    ],
    price: 0,
    stock_quantity: 0,
    ...
  },
  {
    tempId: "temp_1",
    name: "5-6 tahun - Merah Muda",
    sku: "MM-56-MERAHMUDA",
    attributes: [
      { attributeId: 1, attributeName: "Size", valueId: 1, value: "5-6 tahun" },
      { attributeId: 2, attributeName: "Color", valueId: 6, value: "Merah Muda" }
    ],
    price: 0,
    stock_quantity: 0,
    ...
  },
  // ... 10 more combinations (4 sizes × 3 colors = 12 variants)
]
```

### 4.4 Step 3: Display Variants in Grid/Table

Tampilkan semua variants dalam table untuk bulk edit:

```javascript
function VariantsTable({ variants, onUpdate }) {
  return (
    <table>
      <thead>
        <tr>
          <th>Variant</th>
          <th>SKU</th>
          <th>Price</th>
          <th>Compare At Price</th>
          <th>Stock</th>
          <th>Weight (gram)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        {variants.map((variant, index) => (
          <tr key={variant.tempId}>
            <td>
              {variant.attributes.map(a => a.value).join(' - ')}
            </td>
            <td>
              <input
                type="text"
                value={variant.sku}
                onChange={(e) => onUpdate(index, 'sku', e.target.value)}
              />
            </td>
            <td>
              <input
                type="number"
                value={variant.price}
                onChange={(e) => onUpdate(index, 'price', e.target.value)}
              />
            </td>
            <td>
              <input
                type="number"
                value={variant.compare_at_price}
                onChange={(e) => onUpdate(index, 'compare_at_price', e.target.value)}
              />
            </td>
            <td>
              <input
                type="number"
                value={variant.stock_quantity}
                onChange={(e) => onUpdate(index, 'stock_quantity', e.target.value)}
              />
            </td>
            <td>
              <input
                type="number"
                value={variant.weight_gram}
                onChange={(e) => onUpdate(index, 'weight_gram', e.target.value)}
              />
            </td>
            <td>
              <button onClick={() => removeVariant(index)}>Delete</button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
```

### 4.5 Bulk Actions

Tambahkan fitur untuk set price/stock untuk semua variants sekaligus:

```javascript
function BulkActions({ onBulkUpdate }) {
  const [bulkPrice, setBulkPrice] = useState('');
  const [bulkStock, setBulkStock] = useState('');

  return (
    <div className="bulk-actions">
      <h3>Bulk Actions</h3>

      <div>
        <label>Set Price for All:</label>
        <input
          type="number"
          value={bulkPrice}
          onChange={(e) => setBulkPrice(e.target.value)}
        />
        <button onClick={() => onBulkUpdate('price', bulkPrice)}>
          Apply to All
        </button>
      </div>

      <div>
        <label>Set Stock for All:</label>
        <input
          type="number"
          value={bulkStock}
          onChange={(e) => setBulkStock(e.target.value)}
        />
        <button onClick={() => onBulkUpdate('stock_quantity', bulkStock)}>
          Apply to All
        </button>
      </div>
    </div>
  );
}
```

### 4.6 Step 4: Save Variants

⚠️ **IMPORTANT:** Saat ini backend belum support bulk create variants dengan attributes. Anda perlu create variants satu-per-satu menggunakan existing API.

**Current Workaround - Create One by One:**
```javascript
async function saveVariants(productId, variants) {
  const results = [];

  for (const variant of variants) {
    try {
      // Create variant via existing API
      const response = await fetch('/api/catalog/products/variants', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          product_id: productId,
          sku: variant.sku,
          barcode: variant.barcode,
          size: variant.attributes.find(a => a.attributeName === 'Size')?.value || null,
          color: variant.attributes.find(a => a.attributeName === 'Color')?.value || null,
          price: variant.price,
          compare_at_price: variant.compare_at_price,
          stock_quantity: variant.stock_quantity,
          weight_gram: variant.weight_gram,
          images: variant.images
        })
      });

      const result = await response.json();

      if (result.status) {
        // After variant created, save attribute relationships
        await saveVariantAttributes(result.data.id, variant.attributes);
        results.push(result.data);
      }
    } catch (error) {
      console.error('Failed to create variant:', error);
    }
  }

  return results;
}

// Save variant-attribute relationships
async function saveVariantAttributes(variantId, attributes) {
  for (const attr of attributes) {
    await fetch('/api/catalog/product-variant-attributes', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        product_variant_id: variantId,
        product_attribute_id: attr.attributeId,
        product_attribute_value_id: attr.valueId
      })
    });
  }
}
```

---

## 5. Complete Workflow Example

### Scenario: Create Product "Minimoda Breezy Dress" dengan 12 Variants

```javascript
// 1. Fetch attributes
const attributes = await fetchAttributes();
// Result: [Size (4 values), Color (8 values)]

// 2. User selects attributes
const selectedAttributes = [
  {
    attributeId: 1,  // Size
    selectedValues: [1, 2, 3, 4]  // All 4 sizes
  },
  {
    attributeId: 2,  // Color
    selectedValues: [5, 6, 7]  // Bright Pink, Merah Muda, Putih
  }
];

// 3. Generate combinations
const variants = generateVariantCombinations(selectedAttributes, attributes);
// Result: 12 variants (4 sizes × 3 colors)

// 4. User fills in details
variants.forEach(v => {
  v.price = 179550;
  v.compare_at_price = 315000;
  v.stock_quantity = 10;
  v.weight_gram = 200;
});

// 5. Create product first
const product = await createProduct({
  name: "Minimoda Breezy Dress",
  description: "Dress Anak Perempuan...",
  brand_id: 1,
  // ... other fields
});

// 6. Save all variants
const savedVariants = await saveVariants(product.id, variants);

console.log(`Created ${savedVariants.length} variants`);
```

---

## 6. Error Handling

### Common Error Responses

**404 - Not Found:**
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Attribute not found",
  "data": []
}
```

**422 - Validation Error:**
```json
{
  "status": false,
  "statusCode": "422",
  "message": "Validation failed",
  "data": {
    "errors": {
      "name": ["The name field is required."],
      "type": ["The selected type is invalid."]
    }
  }
}
```

**500 - Cannot Delete:**
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Cannot delete attribute with existing values. Please delete all values first.",
  "data": []
}
```

---

## 7. Tips ## 6. Tips & Best Practices Best Practices

### 7.1 Performance Optimization

- **Cache attributes data** di frontend (attributes jarang berubah)
- **Debounce bulk update operations** untuk menghindari too many requests
- **Lazy load attribute values** hanya saat dibutuhkan

### 7.2 UX Recommendations

- **Show preview** dari semua variants sebelum save
- **Validate SKU uniqueness** di frontend sebelum submit
- **Allow copy values** dari satu variant ke variant lain
- **Support import from CSV** untuk bulk variant creation
- **Auto-save drafts** saat user edit variants

### 7.3 Future Enhancements (Coming Soon)

- ✅ Bulk create variants API endpoint
- ✅ Duplicate variant functionality
- ✅ Import/Export variants CSV
- ✅ Variant templates
- ✅ Attribute groups

---

## 8. Need Help?

Jika ada pertanyaan atau butuh bantuan implementasi:
- Check file seeder: `database/seeders/ProductAttributeSeeder.php`
- Check controller: `app/Http/Controllers/Api/Catalog/ProductAttributeApiController.php`
- Check repository: `app/Repositories/Catalog/ProductAttributeRepository.php`
