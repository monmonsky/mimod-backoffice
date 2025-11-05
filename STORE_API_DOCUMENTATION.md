# Store API Documentation - Product Endpoints

API untuk frontend e-commerce store dengan fitur filtering berdasarkan variant (size, color, price, stock, dll).

## Base URL
```
/api/store
```

## Authentication
Semua endpoint menggunakan `store.api` middleware dengan lifetime session tokens.

## API Methods
Untuk filtering, tersedia 2 method:
- **GET** - Filter menggunakan query parameters (URL)
- **POST** - Filter menggunakan request body (Recommended untuk filter kompleks)

---

## 1. Get Product by Slug

**Endpoint:**
```
GET /api/store/products/{slug}
```

**Description:**
Mengambil detail product berdasarkan slug. Endpoint `show` sudah support baik ID maupun slug.

**Parameters:**
- `{slug}` - Product slug (string)

**Example Request:**
```bash
GET /api/store/products/sepatu-anak-merah
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Sepatu Anak Merah",
    "slug": "sepatu-anak-merah",
    "description": "Sepatu anak berkualitas tinggi",
    "brand_id": 5,
    "brand_name": "Nike Kids",
    "status": "active",
    "is_featured": true,
    "categories": [
      {
        "id": 10,
        "name": "Sepatu",
        "slug": "sepatu"
      }
    ],
    "variants": [
      {
        "id": 1,
        "sku": "SEPATU-001-RED-25",
        "size": "25",
        "color": "red",
        "price": 250000,
        "stock_quantity": 10,
        "images": [...]
      }
    ],
    "images": [...]
  }
}
```

---

## 2. Get Products by Category Slug

### 2.1. GET Method

**Endpoint:**
```
GET /api/store/products/category/{categorySlug}
```

**Description:**
Mengambil semua product dalam kategori tertentu berdasarkan slug kategori, dengan dukungan filtering berdasarkan variant.

**Parameters:**
- `{categorySlug}` - Category slug (string)

**Query Parameters (Optional):**
- `per_page` - Items per page (default: 20)
- `page` - Page number (default: 1)
- `search` - Search by product name/description
- `brand_id` - Filter by brand ID
- `min_price` - Minimum price
- `max_price` - Maximum price
- `size` - Filter by size (comma-separated: "25,26,27")
- `color` - Filter by color (comma-separated: "red,blue,green")
- `in_stock` - Filter products with stock (true/false)
- `stock_status` - Filter by stock status (in_stock/low_stock/out_of_stock)
- `is_featured` - Filter featured products (true/false)
- `sort_by` - Sort field (price/name/created_at, default: created_at)
- `sort_order` - Sort order (asc/desc, default: desc)

**Example Request:**
```bash
# Basic request
GET /api/store/products/category/sepatu

# With filters
GET /api/store/products/category/sepatu?size=25,26&color=red&min_price=100000&max_price=500000&in_stock=true&sort_by=price&sort_order=asc
```

### 2.2. POST Method (Recommended)

**Endpoint:**
```
POST /api/store/products/category/filter
```

**Description:**
Filter products berdasarkan category slug menggunakan POST method. Lebih baik untuk filter kompleks.

**Request Body:**
```json
{
  "category_slug": "sepatu",
  "size": ["25", "26", "27"],
  "color": ["red", "blue"],
  "min_price": 100000,
  "max_price": 500000,
  "in_stock": true,
  "sort_by": "price",
  "sort_order": "asc",
  "per_page": 20,
  "page": 1
}
```

**Example Request:**
```bash
curl -X POST /api/store/products/category/filter \
  -H "Content-Type: application/json" \
  -d '{
    "category_slug": "sepatu",
    "size": ["25", "26"],
    "color": ["red"],
    "min_price": 100000,
    "max_price": 500000,
    "in_stock": true,
    "sort_by": "price",
    "sort_order": "asc"
  }'
```

**Response (Both Methods):**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Products by category retrieved successfully",
  "data": {
    "category": {
      "id": 10,
      "name": "Sepatu",
      "slug": "sepatu",
      "description": "Kategori sepatu anak",
      "product_count": 45
    },
    "products": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "name": "Sepatu Anak Merah",
          "slug": "sepatu-anak-merah",
          "brand_name": "Nike Kids",
          "categories": [...],
          "variants": [...],
          "images": [...]
        }
      ],
      "total": 45,
      "per_page": 20,
      "last_page": 3
    }
  }
}
```

---

## 3. Get All Products with Advanced Filters

### 3.1. GET Method

**Endpoint:**
```
GET /api/store/products
```

**Description:**
Mengambil semua product dengan dukungan filtering lanjutan berdasarkan variant attributes.

**Query Parameters (Optional):**
- `per_page` - Items per page (default: 20)
- `page` - Page number (default: 1)
- `search` - Search by product name/description
- `status` - Filter by status (active/inactive/draft)
- `brand_id` - Filter by brand ID
- `category_id` - Filter by category ID (support multiple: "1,2,3")
- `min_price` - Minimum price
- `max_price` - Maximum price
- `size` - Filter by size (comma-separated: "25,26,27")
- `color` - Filter by color (comma-separated: "red,blue,green")
- `in_stock` - Filter products with stock (true/false)
- `stock_status` - Filter by stock status:
  - `in_stock` - Stock > 10
  - `low_stock` - Stock between 1-10
  - `out_of_stock` - Stock = 0
- `is_featured` - Filter featured products (true/false)
- `sort_by` - Sort field (price/name/created_at, default: created_at)
- `sort_order` - Sort order (asc/desc, default: desc)

**Example Requests:**

```bash
# Filter by size and color
GET /api/store/products?size=25,26&color=red,blue

# Filter by price range
GET /api/store/products?min_price=100000&max_price=500000

# Filter by stock status
GET /api/store/products?stock_status=in_stock

# Filter by category and brand
GET /api/store/products?category_id=10&brand_id=5

# Multiple filters with sorting
GET /api/store/products?category_id=10&size=25&color=red&min_price=100000&max_price=300000&in_stock=true&sort_by=price&sort_order=asc

# Search with filters
GET /api/store/products?search=sepatu&size=25&in_stock=true
```

### 3.2. POST Method (Recommended)

**Endpoint:**
```
POST /api/store/products/filter
```

**Description:**
Filter products menggunakan POST method. Recommended untuk filter kompleks dan menghindari URL yang terlalu panjang.

**Request Body:**
```json
{
  "search": "sepatu",
  "status": "active",
  "brand_id": 5,
  "category_id": "10,11,12",
  "min_price": 100000,
  "max_price": 500000,
  "size": ["25", "26", "27"],
  "color": ["red", "blue"],
  "in_stock": true,
  "stock_status": "in_stock",
  "is_featured": false,
  "sort_by": "price",
  "sort_order": "asc",
  "per_page": 20,
  "page": 1
}
```

**Example Requests:**

```bash
# Filter by size and color (POST)
curl -X POST /api/store/products/filter \
  -H "Content-Type: application/json" \
  -d '{
    "size": ["25", "26"],
    "color": ["red", "blue"]
  }'

# Multiple filters with sorting (POST)
curl -X POST /api/store/products/filter \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": "10",
    "size": ["25"],
    "color": ["red"],
    "min_price": 100000,
    "max_price": 300000,
    "in_stock": true,
    "sort_by": "price",
    "sort_order": "asc"
  }'
```

**Response (Both Methods):**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Products retrieved successfully",
  "data": {
    "products": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "name": "Sepatu Anak Merah",
          "slug": "sepatu-anak-merah",
          "brand_name": "Nike Kids",
          "categories": [
            {
              "id": 10,
              "name": "Sepatu",
              "slug": "sepatu"
            }
          ],
          "variants": [
            {
              "id": 1,
              "sku": "SEPATU-001-RED-25",
              "size": "25",
              "color": "red",
              "price": 250000,
              "compare_at_price": 300000,
              "stock_quantity": 10,
              "weight_gram": 300,
              "images": [...]
            }
          ],
          "images": [
            {
              "id": 1,
              "url": "https://cdn.example.com/products/sepatu-001.jpg",
              "is_primary": true,
              "media_type": "image",
              "alt_text": "Sepatu Anak Merah"
            }
          ]
        }
      ],
      "total": 100,
      "per_page": 20,
      "last_page": 5
    },
    "statistics": {
      "total": 500,
      "active": 450,
      "inactive": 30,
      "draft": 20,
      "featured": 50,
      "total_variants": 1500,
      "total_stock": 5000,
      "low_stock": 45
    }
  }
}
```

---

## Use Cases

### 1. Product Listing Page dengan Filter (GET Method)
```javascript
// Fetch products with multiple filters using GET
const filters = {
  category_id: '10',
  size: '25,26,27',
  color: 'red,blue',
  min_price: 100000,
  max_price: 500000,
  in_stock: true,
  sort_by: 'price',
  sort_order: 'asc',
  per_page: 20,
  page: 1
};

const queryString = new URLSearchParams(filters).toString();
const response = await fetch(`/api/store/products?${queryString}`);
const data = await response.json();
```

### 2. Product Listing Page dengan Filter (POST Method - Recommended)
```javascript
// Fetch products with multiple filters using POST
const filters = {
  category_id: '10',
  size: ['25', '26', '27'],
  color: ['red', 'blue'],
  min_price: 100000,
  max_price: 500000,
  in_stock: true,
  sort_by: 'price',
  sort_order: 'asc',
  per_page: 20,
  page: 1
};

const response = await fetch('/api/store/products/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(filters)
});
const data = await response.json();
```

### 3. Category Page (GET Method)
```javascript
// Fetch products by category slug with filters
const categorySlug = 'sepatu';
const filters = {
  size: '25',
  in_stock: true,
  sort_by: 'price'
};

const queryString = new URLSearchParams(filters).toString();
const response = await fetch(`/api/store/products/category/${categorySlug}?${queryString}`);
const data = await response.json();
```

### 4. Category Page (POST Method - Recommended)
```javascript
// Fetch products by category slug using POST
const filters = {
  category_slug: 'sepatu',
  size: ['25', '26'],
  color: ['red'],
  min_price: 100000,
  max_price: 500000,
  in_stock: true,
  sort_by: 'price',
  sort_order: 'asc'
};

const response = await fetch('/api/store/products/category/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(filters)
});
const data = await response.json();
```

### 5. Product Detail Page
```javascript
// Fetch product by slug
const productSlug = 'sepatu-anak-merah';
const response = await fetch(`/api/store/products/${productSlug}`);
const data = await response.json();
```

### 6. Filter by Multiple Sizes (POST)
```javascript
// Get products available in sizes 25, 26, 27
const response = await fetch('/api/store/products/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    size: ['25', '26', '27'],
    in_stock: true
  })
});
```

### 7. Filter by Price Range and Color (POST)
```javascript
// Get red and blue products between 100k-500k
const response = await fetch('/api/store/products/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    color: ['red', 'blue'],
    min_price: 100000,
    max_price: 500000
  })
});
```

---

## Filter Combinations

Semua filter dapat dikombinasikan untuk hasil yang lebih spesifik:

### GET Method Example:
```bash
GET /api/store/products/category/sepatu-anak?size=25,26&color=red,blue&min_price=100000&max_price=300000&in_stock=true&brand_id=5&sort_by=price&sort_order=asc
```

### POST Method Example (Recommended):
```bash
curl -X POST /api/store/products/category/filter \
  -H "Content-Type: application/json" \
  -d '{
    "category_slug": "sepatu-anak",
    "size": ["25", "26"],
    "color": ["red", "blue"],
    "min_price": 100000,
    "max_price": 300000,
    "in_stock": true,
    "brand_id": 5,
    "sort_by": "price",
    "sort_order": "asc"
  }'
```

Filter ini akan mengembalikan:
- Products dalam kategori "sepatu-anak"
- Yang memiliki variant dengan size 25 atau 26
- Yang memiliki variant dengan color red atau blue
- Dengan harga antara 100.000 - 300.000
- Yang masih ada stock
- Dari brand ID 5
- Diurutkan berdasarkan harga (ascending)

---

## API Endpoints Summary

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/store/products/{slug}` | Get product by slug or ID |
| GET | `/api/store/products` | Get all products with filters (query params) |
| **POST** | `/api/store/products/filter` | **Filter products (request body) - Recommended** |
| GET | `/api/store/products/category/{categorySlug}` | Get products by category slug (query params) |
| **POST** | `/api/store/products/category/filter` | **Filter by category slug (request body) - Recommended** |
| GET | `/api/store/products/sitemap` | Get products for sitemap |

---

## Notes

1. **GET vs POST**:
   - **GET Method**: Filter menggunakan query parameters di URL. Cocok untuk filter sederhana.
   - **POST Method**: Filter menggunakan request body JSON. **Recommended** untuk filter kompleks, lebih clean, dan tidak ada limit panjang URL.

2. **Product by Slug**: Endpoint `GET /api/store/products/{id}` sudah support slug, jadi tidak perlu endpoint terpisah.

3. **Category Slug Support**:
   - GET: `/api/store/products/category/{categorySlug}`
   - POST: `/api/store/products/category/filter` dengan `category_slug` di request body

4. **Multiple Values**:
   - GET Method: Comma-separated string (contoh: `size=25,26,27`)
   - POST Method: Array (contoh: `"size": ["25", "26", "27"]`)

5. **Stock Filters**:
   - `in_stock=true` - Products yang ada stocknya
   - `stock_status=in_stock` - Stock > 10
   - `stock_status=low_stock` - Stock 1-10
   - `stock_status=out_of_stock` - Stock = 0

6. **Sorting**:
   - `sort_by=price` - Sort berdasarkan minimum variant price
   - `sort_by=name` - Sort berdasarkan product name
   - `sort_by=created_at` - Sort berdasarkan tanggal dibuat

7. **Pagination**: Gunakan `page` dan `per_page` untuk navigasi halaman.

8. **Why POST for Filtering?**
   - Tidak ada batasan panjang URL
   - Lebih clean dan terstruktur
   - Mudah handle array dan nested objects
   - Lebih aman untuk data sensitif (jika ada)

---

## Error Responses

**404 - Not Found:**
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Product not found",
  "data": []
}
```

**500 - Server Error:**
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to retrieve products: [error message]",
  "data": []
}
```
