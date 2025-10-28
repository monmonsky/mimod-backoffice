# Store API Documentation

API untuk frontend e-commerce store. Menggunakan **lifetime session tokens** untuk akses read-only ke catalog data.

## Base URL
```
http://api-local.minimoda.id/api/store
```

## Authentication

### Lifetime Session Tokens

Store API menggunakan **Personal Access Token** dengan **lifetime session** (tidak ada expiry).

**Token Format:**
```
Authorization: Bearer {token_id}|{plain_token}
```

**Cara Generate Token:**

Melalui Backoffice Admin Panel:
1. Login ke backoffice
2. Navigasi ke **Access Control > Store Tokens**
3. Click **Generate Token**
4. Copy token yang dihasilkan
5. Token ini **tidak akan expired** (lifetime session)

**Token Abilities/Permissions:**
- `*` - Full access (all permissions)
- `products:read` - Read products
- `categories:read` - Read categories
- `brands:read` - Read brands
- `settings:read` - Read settings

---

## 📝 Endpoints

### Products

#### 1. Get All Products

**Endpoint:** `GET /store/products`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15)
- `search` - Search query
- `category_id` - Filter by category
- `brand_id` - Filter by brand
- `status` - Filter by status (active/inactive)
- `sort` - Sort field (name, price, created_at)
- `order` - Sort order (asc/desc)

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Products retrieved successfully",
    "data": {
        "products": [
            {
                "id": 1,
                "name": "Product Name",
                "slug": "product-slug",
                "description": "Product description",
                "price": "100000.00",
                "status": "active",
                "brand": {
                    "id": 1,
                    "name": "Brand Name"
                },
                "images": [
                    {
                        "url": "http://...",
                        "is_primary": true
                    }
                ]
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 100,
            "last_page": 7
        }
    }
}
```

---

#### 2. Get Product Details

**Endpoint:** `GET /store/products/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Product retrieved successfully",
    "data": {
        "id": 1,
        "name": "Product Name",
        "slug": "product-slug",
        "description": "Full product description",
        "price": "100000.00",
        "compare_at_price": "150000.00",
        "status": "active",
        "brand": {
            "id": 1,
            "name": "Brand Name",
            "logo": "http://..."
        },
        "categories": [
            {
                "id": 1,
                "name": "Category Name",
                "slug": "category-slug"
            }
        ],
        "variants": [
            {
                "id": 1,
                "sku": "SKU-001",
                "size": "M",
                "color": "Red",
                "price": "100000.00",
                "stock_quantity": 50
            }
        ],
        "images": [
            {
                "id": 1,
                "url": "http://...",
                "alt_text": "Product image",
                "is_primary": true,
                "sort_order": 1
            }
        ]
    }
}
```

---

### Categories

#### 1. Get All Categories

**Endpoint:** `GET /store/categories`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Categories retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Category Name",
            "slug": "category-slug",
            "description": "Category description",
            "parent_id": null,
            "image": "http://...",
            "status": "active"
        }
    ]
}
```

---

#### 2. Get Category Tree

**Endpoint:** `GET /store/categories/tree`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Category tree retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Parent Category",
            "slug": "parent-category",
            "children": [
                {
                    "id": 2,
                    "name": "Child Category",
                    "slug": "child-category",
                    "children": []
                }
            ]
        }
    ]
}
```

---

#### 3. Get Parent Categories

**Endpoint:** `GET /store/categories/parents`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Parent categories retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Parent Category",
            "slug": "parent-category"
        }
    ]
}
```

---

#### 4. Get Category Details

**Endpoint:** `GET /store/categories/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Category retrieved successfully",
    "data": {
        "id": 1,
        "name": "Category Name",
        "slug": "category-slug",
        "description": "Category description",
        "parent_id": null,
        "image": "http://...",
        "status": "active"
    }
}
```

---

#### 5. Get Category Children

**Endpoint:** `GET /store/categories/{parentId}/children`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Category children retrieved successfully",
    "data": [
        {
            "id": 2,
            "name": "Child Category",
            "slug": "child-category",
            "parent_id": 1
        }
    ]
}
```

---

### Brands

#### 1. Get All Brands

**Endpoint:** `GET /store/brands`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Brands retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Brand Name",
            "slug": "brand-slug",
            "logo": "http://...",
            "description": "Brand description",
            "status": "active"
        }
    ]
}
```

---

#### 2. Get Brand Details

**Endpoint:** `GET /store/brands/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Brand retrieved successfully",
    "data": {
        "id": 1,
        "name": "Brand Name",
        "slug": "brand-slug",
        "logo": "http://...",
        "description": "Full brand description",
        "website": "https://...",
        "status": "active"
    }
}
```

---

### Settings

#### 1. Get All Settings

**Endpoint:** `GET /store/settings`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Settings retrieved successfully",
    "data": {
        "general": {
            "site_name": "My Store",
            "site_description": "Best products here"
        },
        "contact": {
            "email": "store@example.com",
            "phone": "08123456789"
        }
    }
}
```

---

#### 2. Get Settings by Key

**Endpoint:** `GET /store/settings/{key}`

**Headers:**
```
Authorization: Bearer {token}
```

**Example:** `GET /store/settings/general`

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Settings retrieved successfully",
    "data": {
        "site_name": "My Store",
        "site_description": "Best products here",
        "logo": "http://..."
    }
}
```

---

### Navigation Menus

#### Get Menus by Location

**Endpoint:** `GET /store/menus?location={location}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `location` - Menu location (header, footer, sidebar)

**Response:**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Menus retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Home",
            "url": "/",
            "location": "header",
            "parent_id": null,
            "order": 1,
            "children": []
        },
        {
            "id": 2,
            "title": "Products",
            "url": "/products",
            "location": "header",
            "parent_id": null,
            "order": 2,
            "children": [
                {
                    "id": 3,
                    "title": "Men",
                    "url": "/products/men",
                    "parent_id": 2,
                    "order": 1
                }
            ]
        }
    ]
}
```

---

### Orders (Temporary - WhatsApp Checkout)

#### 1. Create Temporary Order

**Endpoint:** `POST /store/orders/temporary`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "product_variant_id": null,
      "quantity": 2
    },
    {
      "product_id": 2,
      "product_variant_id": 5,
      "quantity": 1
    }
  ],
  "customer_info": {
    "name": "John Doe",
    "phone": "081234567890",
    "email": "john@example.com",
    "address": "Jl. Example No. 123",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12345"
  },
  "coupon_code": "DISC10",
  "notes": "Tolong pack dengan bubble wrap"
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Order calculated successfully",
  "data": {
    "items": [
      {
        "type": "product",
        "product_id": 1,
        "product_variant_id": null,
        "name": "Product Name",
        "sku": "PRD-001",
        "variant_name": null,
        "price": 100000,
        "quantity": 2,
        "subtotal": 200000,
        "image": "https://..."
      }
    ],
    "customer_info": {
      "name": "John Doe",
      "phone": "081234567890",
      "email": "john@example.com",
      "address": "Jl. Example No. 123",
      "city": "Jakarta",
      "province": "DKI Jakarta",
      "postal_code": "12345"
    },
    "subtotal": 200000,
    "discount": 20000,
    "total": 180000,
    "coupon": {
      "code": "DISC10",
      "name": "10% Discount",
      "type": "percentage",
      "value": 10,
      "discount_amount": 20000
    },
    "notes": "Tolong pack dengan bubble wrap",
    "whatsapp_message": "*ORDER BARU*\n\n👤 *Customer*\nNama: John Doe\n...",
    "whatsapp_url": "https://wa.me/6281234567890?text=..."
  }
}
```

**Features:**
- Validates stock availability
- Calculates subtotal, discount, and total
- Applies coupon if valid
- Generates WhatsApp message
- Returns WhatsApp URL for direct checkout
- **Does NOT save to database** (temporary order)

---

#### 2. Validate Coupon

**Endpoint:** `POST /store/orders/validate-coupon`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "code": "DISC10",
  "subtotal": 200000
}
```

**Response Success:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupon is valid",
  "data": {
    "code": "DISC10",
    "name": "10% Discount",
    "type": "percentage",
    "value": 10,
    "discount_amount": 20000,
    "min_purchase": 100000,
    "max_discount": 50000
  }
}
```

**Response Error - Invalid Coupon:**
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Coupon not found or expired"
}
```

**Response Error - Minimum Purchase:**
```json
{
  "status": false,
  "statusCode": "422",
  "message": "Minimum purchase of Rp 100.000 required"
}
```

---

## Error Responses

### 401 Unauthorized - Token Missing

```json
{
    "status": false,
    "statusCode": "401",
    "message": "Unauthorized. API token not provided.",
    "data": [],
    "error": "TOKEN_MISSING"
}
```

### 401 Unauthorized - Invalid Token

```json
{
    "status": false,
    "statusCode": "401",
    "message": "Unauthorized. Invalid token.",
    "data": [],
    "error": "TOKEN_INVALID"
}
```

### 403 Forbidden - Insufficient Permissions

```json
{
    "status": false,
    "statusCode": "403",
    "message": "Forbidden. Token does not have required permission: products:read",
    "data": [],
    "error": "INSUFFICIENT_PERMISSIONS"
}
```

### 403 Forbidden - User Inactive

```json
{
    "status": false,
    "statusCode": "403",
    "message": "Forbidden. User account is inactive.",
    "data": [],
    "error": "USER_INACTIVE"
}
```

---

## Testing dengan cURL

### Get Products

```bash
curl -X GET http://api-local.minimoda.id/api/store/products \
  -H "Authorization: Bearer 1|abcdef123456"
```

### Get Product Details

```bash
curl -X GET http://api-local.minimoda.id/api/store/products/1 \
  -H "Authorization: Bearer 1|abcdef123456"
```

### Get Categories Tree

```bash
curl -X GET http://api-local.minimoda.id/api/store/categories/tree \
  -H "Authorization: Bearer 1|abcdef123456"
```

### Get Menus (Header)

```bash
curl -X GET "http://api-local.minimoda.id/api/store/menus?location=header" \
  -H "Authorization: Bearer 1|abcdef123456"
```

### Create Temporary Order (WhatsApp Checkout)

```bash
curl -X POST http://api-local.minimoda.id/api/store/orders/temporary \
  -H "Authorization: Bearer 1|abcdef123456" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      }
    ],
    "customer_info": {
      "name": "John Doe",
      "phone": "081234567890",
      "email": "john@example.com",
      "address": "Jl. Example No. 123",
      "city": "Jakarta",
      "province": "DKI Jakarta",
      "postal_code": "12345"
    },
    "coupon_code": "DISC10",
    "notes": "Pack with bubble wrap"
  }'
```

### Validate Coupon

```bash
curl -X POST http://api-local.minimoda.id/api/store/orders/validate-coupon \
  -H "Authorization: Bearer 1|abcdef123456" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "DISC10",
    "subtotal": 200000
  }'
```

---

## Shipping Methods

### Get All Active Shipping Methods

**Endpoint:** `GET /store/shipping-methods`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `weight` (optional): Filter by weight capacity in grams

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": [
    {
      "id": 1,
      "code": "jne_reg",
      "name": "JNE REG",
      "type": "manual",
      "provider": "jne",
      "base_cost": 10000,
      "cost_per_kg": 5000,
      "min_weight": null,
      "max_weight": 30000,
      "estimated_delivery": "2-3 hari",
      "is_active": true,
      "sort_order": 0
    },
    {
      "id": 2,
      "code": "jne_yes",
      "name": "JNE YES",
      "type": "manual",
      "provider": "jne",
      "base_cost": 15000,
      "cost_per_kg": 8000,
      "min_weight": null,
      "max_weight": 30000,
      "estimated_delivery": "1-2 hari",
      "is_active": true,
      "sort_order": 1
    }
  ]
}
```

**Example cURL:**
```bash
curl -X GET "http://api-local.minimoda.id/api/store/shipping-methods?weight=1500" \
  -H "Authorization: Bearer 1|abcdef123456"
```

---

### Calculate Shipping Cost

**Endpoint:** `POST /store/shipping-methods/{id}/calculate-cost`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "weight": 1500,
  "destination_city_id": 455,
  "destination_subdistrict_id": null
}
```

**Request Parameters:**
- `weight` (required): Weight in grams
- `destination_city_id` (optional): For RajaOngkir integration
- `destination_subdistrict_id` (optional): For RajaOngkir integration

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "cost": 17500,
    "estimated_delivery": "2-3 hari",
    "type": "manual"
  }
}
```

**Calculation Method:**
- **Manual/Custom Methods**: `cost = base_cost + (cost_per_kg × weight_in_kg)`
- **RajaOngkir Methods**: Integration with RajaOngkir API (currently not implemented)

**Error Response (Inactive Shipping Method):**
```json
{
  "status": false,
  "statusCode": "400",
  "message": "Shipping method is not active"
}
```

**Example cURL:**
```bash
curl -X POST http://api-local.minimoda.id/api/store/shipping-methods/1/calculate-cost \
  -H "Authorization: Bearer 1|abcdef123456" \
  -H "Content-Type: application/json" \
  -d '{
    "weight": 1500
  }'
```

---

## Token Management

### Generate Token (via Backoffice)

1. Login ke backoffice admin
2. Buka **Access Control > Store Tokens**
3. Click **Generate Token**
4. Isi form:
   - **Name**: Frontend Store (description)
   - **Abilities**: Pilih permissions yang diperlukan
     - `*` untuk full access
     - atau pilih specific: `products:read`, `categories:read`, dll
   - **Expires At**: Kosongkan untuk lifetime token
5. Copy token yang dihasilkan
6. **IMPORTANT**: Token hanya ditampilkan sekali!

### Token Best Practices

1. ✅ **Gunakan lifetime tokens** untuk store frontend
2. ✅ **Read-only permissions** (`products:read`, `categories:read`)
3. ✅ **Simpan token dengan aman** di environment variables
4. ✅ **Rotate tokens** secara berkala untuk keamanan
5. ❌ **Jangan hardcode** token di frontend code
6. ❌ **Jangan share** token di public repositories

---

## Notes

- **Read-Only Access**: Store API hanya untuk membaca data, tidak bisa create/update/delete
- **No Expiration**: Token bersifat lifetime (tidak expired)
- **CORS**: Pastikan CORS dikonfigurasi dengan benar untuk domain store Anda
- **Rate Limiting**: Middleware automatically update `last_used_at` timestamp
- **Caching**: Pertimbangkan caching di frontend untuk performa lebih baik
