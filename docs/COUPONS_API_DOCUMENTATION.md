# Coupons API Documentation

## Overview
API untuk mengelola kupon diskon/promosi untuk e-commerce.

**Base URL:** `http://api-local.minimoda.id/api/marketing/coupons`

**Authentication:** Required (Bearer Token)

---

## Endpoints

### 1. Get All Coupons (with filters & statistics)

**Endpoint:** `GET /api/marketing/coupons`

**Permission:** `marketing.coupons.view`

#### Query Parameters:
```typescript
{
  search?: string;           // Search by code or name
  type?: string;             // Filter: percentage, fixed, free_shipping
  status?: string;           // Filter: active, expired, upcoming, inactive
  sort_by?: string;          // Sort by: code, name, created_at (default)
  per_page?: number;         // Items per page (default: 20)
  page?: number;             // Page number
}
```

#### Request Example:
```bash
# Get all coupons
curl -X GET "http://api-local.minimoda.id/api/marketing/coupons" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Search coupon
curl -X GET "http://api-local.minimoda.id/api/marketing/coupons?search=DISC50" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by type
curl -X GET "http://api-local.minimoda.id/api/marketing/coupons?type=percentage" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by status
curl -X GET "http://api-local.minimoda.id/api/marketing/coupons?status=active" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Pagination
curl -X GET "http://api-local.minimoda.id/api/marketing/coupons?per_page=10&page=2" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Response:
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupons retrieved successfully.",
  "data": {
    "coupons": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "code": "DISC50",
          "name": "Diskon 50%",
          "description": "Diskon 50% untuk semua produk",
          "type": "percentage",
          "value": 50,
          "min_purchase": 100000,
          "max_discount": 50000,
          "usage_limit": 100,
          "usage_limit_per_customer": 1,
          "usage_count": 25,
          "start_date": "2025-01-01 00:00:00",
          "end_date": "2025-12-31 23:59:59",
          "is_active": true,
          "applicable_products": [1, 2, 3],
          "applicable_categories": [1, 2],
          "created_by": 1,
          "created_at": "2025-01-01T00:00:00.000000Z",
          "updated_at": "2025-01-01T00:00:00.000000Z"
        }
      ],
      "per_page": 20,
      "total": 50
    },
    "statistics": {
      "total": 50,
      "active": 25,
      "expired": 15,
      "upcoming": 10,
      "total_usage": 500
    }
  }
}
```

---

### 2. Get Single Coupon (with usage history)

**Endpoint:** `GET /api/marketing/coupons/{id}`

**Permission:** `marketing.coupons.view`

#### Request Example:
```bash
curl -X GET "http://api-local.minimoda.id/api/marketing/coupons/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Response:
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupon retrieved successfully.",
  "data": {
    "coupon": {
      "id": 1,
      "code": "DISC50",
      "name": "Diskon 50%",
      "description": "Diskon 50% untuk semua produk",
      "type": "percentage",
      "value": 50,
      "min_purchase": 100000,
      "max_discount": 50000,
      "usage_limit": 100,
      "usage_limit_per_customer": 1,
      "usage_count": 25,
      "start_date": "2025-01-01 00:00:00",
      "end_date": "2025-12-31 23:59:59",
      "is_active": true,
      "applicable_products": [1, 2, 3],
      "applicable_categories": [1, 2],
      "created_by": 1,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    "usage": [
      {
        "id": 1,
        "coupon_id": 1,
        "customer_id": 10,
        "order_id": 100,
        "discount_amount": 50000,
        "used_at": "2025-01-15T10:30:00.000000Z"
      }
    ]
  }
}
```

---

### 3. Create Coupon

**Endpoint:** `POST /api/marketing/coupons`

**Permission:** `marketing.coupons.create`

#### Request Body:
```typescript
{
  code: string;                      // Required, unique, max 50 chars
  name: string;                      // Required, max 255 chars
  description?: string;              // Optional
  type: string;                      // Required: percentage | fixed | free_shipping
  value: number;                     // Required, min 0
  min_purchase?: number;             // Optional, min 0
  max_discount?: number;             // Optional, min 0
  usage_limit?: number;              // Optional, min 1
  usage_limit_per_customer: number;  // Required, min 1
  start_date: string;                // Required, date format
  end_date: string;                  // Required, must be after start_date
  is_active?: boolean;               // Optional, default false
  applicable_products?: number[];    // Optional, array of product IDs
  applicable_categories?: number[];  // Optional, array of category IDs
}
```

#### Request Example:
```bash
curl -X POST "http://api-local.minimoda.id/api/marketing/coupons" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "DISC50",
    "name": "Diskon 50%",
    "description": "Diskon 50% untuk semua produk",
    "type": "percentage",
    "value": 50,
    "min_purchase": 100000,
    "max_discount": 50000,
    "usage_limit": 100,
    "usage_limit_per_customer": 1,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "is_active": true,
    "applicable_products": [1, 2, 3],
    "applicable_categories": [1, 2]
  }'
```

#### Coupon Types Explained:

**1. Percentage Discount**
```json
{
  "type": "percentage",
  "value": 50,              // 50% off
  "max_discount": 50000     // Maximum discount 50.000
}
```

**2. Fixed Amount Discount**
```json
{
  "type": "fixed",
  "value": 25000            // Rp 25.000 off
}
```

**3. Free Shipping**
```json
{
  "type": "free_shipping",
  "value": 0                // Free shipping
}
```

#### Response:
```json
{
  "status": true,
  "statusCode": "201",
  "message": "Coupon created successfully.",
  "data": {
    "coupon": {
      "id": 1,
      "code": "DISC50",
      "name": "Diskon 50%",
      ...
    }
  }
}
```

---

### 4. Update Coupon

**Endpoint:** `PUT /api/marketing/coupons/{id}`

**Permission:** `marketing.coupons.update`

#### Request Body: (Same as Create)

#### Request Example:
```bash
curl -X PUT "http://api-local.minimoda.id/api/marketing/coupons/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "DISC50",
    "name": "Diskon 50% Updated",
    "description": "Updated description",
    "type": "percentage",
    "value": 50,
    "min_purchase": 100000,
    "max_discount": 50000,
    "usage_limit": 200,
    "usage_limit_per_customer": 2,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "is_active": true
  }'
```

#### Response:
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupon updated successfully.",
  "data": {
    "coupon": { ... }
  }
}
```

---

### 5. Delete Coupon

**Endpoint:** `DELETE /api/marketing/coupons/{id}`

**Permission:** `marketing.coupons.delete`

#### Request Example:
```bash
curl -X DELETE "http://api-local.minimoda.id/api/marketing/coupons/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Response:
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupon deleted successfully.",
  "data": {}
}
```

---

### 6. Validate Coupon (Check if coupon is valid for use)

**Endpoint:** `POST /api/marketing/coupons/validate`

**Permission:** `marketing.coupons.view`

#### Request Body:
```typescript
{
  code: string;           // Required - Coupon code
  customer_id: number;    // Required - Customer ID
  cart_amount: number;    // Required - Cart total amount
}
```

#### Request Example:
```bash
curl -X POST "http://api-local.minimoda.id/api/marketing/coupons/validate" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "DISC50",
    "customer_id": 10,
    "cart_amount": 150000
  }'
```

#### Response (Valid):
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupon validation completed.",
  "data": {
    "valid": true,
    "coupon": {
      "id": 1,
      "code": "DISC50",
      "type": "percentage",
      "value": 50
    },
    "discount_amount": 50000,
    "final_amount": 100000,
    "message": "Coupon applied successfully"
  }
}
```

#### Response (Invalid):
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Coupon validation completed.",
  "data": {
    "valid": false,
    "message": "Coupon has expired",
    "discount_amount": 0
  }
}
```

#### Validation Rules:
1. ✅ Coupon must be active (`is_active = true`)
2. ✅ Current date must be between `start_date` and `end_date`
3. ✅ Cart amount must meet `min_purchase` requirement
4. ✅ Usage limit not exceeded (if set)
5. ✅ Customer usage limit not exceeded
6. ✅ Applicable to cart products/categories (if specified)

---

## Common Examples

### Example 1: Create Percentage Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/marketing/coupons" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "NEWYEAR2025",
    "name": "New Year Sale 2025",
    "description": "Diskon tahun baru 30% untuk semua produk",
    "type": "percentage",
    "value": 30,
    "min_purchase": 50000,
    "max_discount": 100000,
    "usage_limit": 500,
    "usage_limit_per_customer": 1,
    "start_date": "2025-01-01",
    "end_date": "2025-01-31",
    "is_active": true
  }'
```

### Example 2: Create Fixed Amount Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/marketing/coupons" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SAVE50K",
    "name": "Hemat 50 Ribu",
    "description": "Potongan langsung Rp 50.000",
    "type": "fixed",
    "value": 50000,
    "min_purchase": 200000,
    "usage_limit": null,
    "usage_limit_per_customer": 3,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "is_active": true
  }'
```

### Example 3: Create Free Shipping Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/marketing/coupons" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "FREESHIP",
    "name": "Gratis Ongkir",
    "description": "Gratis ongkir untuk pembelian min 100rb",
    "type": "free_shipping",
    "value": 0,
    "min_purchase": 100000,
    "usage_limit": 1000,
    "usage_limit_per_customer": 5,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "is_active": true
  }'
```

### Example 4: Create Category-Specific Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/marketing/coupons" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "TOYS20",
    "name": "Diskon Mainan 20%",
    "description": "Diskon 20% khusus kategori mainan",
    "type": "percentage",
    "value": 20,
    "min_purchase": 0,
    "max_discount": 75000,
    "usage_limit": null,
    "usage_limit_per_customer": 2,
    "start_date": "2025-01-01",
    "end_date": "2025-03-31",
    "is_active": true,
    "applicable_categories": [1, 2, 3]
  }'
```

---

## Error Responses

### 404 - Not Found
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Coupon not found",
  "data": {}
}
```

### 422 - Validation Error
```json
{
  "status": false,
  "statusCode": "422",
  "message": "The code field is required.",
  "data": {
    "errors": {
      "code": ["The code field is required."],
      "type": ["The selected type is invalid."]
    }
  }
}
```

### 500 - Server Error
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Error message here",
  "data": {}
}
```

---

## Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `code` | string | Unique coupon code (e.g., DISC50) |
| `name` | string | Display name of coupon |
| `description` | string | Detailed description |
| `type` | enum | Type: `percentage`, `fixed`, `free_shipping` |
| `value` | number | Discount value (% for percentage, amount for fixed) |
| `min_purchase` | number | Minimum purchase amount to use coupon |
| `max_discount` | number | Maximum discount amount (for percentage type) |
| `usage_limit` | number | Total usage limit (null = unlimited) |
| `usage_limit_per_customer` | number | Per-customer usage limit |
| `usage_count` | number | Current usage count |
| `start_date` | datetime | Coupon start date |
| `end_date` | datetime | Coupon end date |
| `is_active` | boolean | Active status |
| `applicable_products` | array | Product IDs this coupon applies to (null = all) |
| `applicable_categories` | array | Category IDs this coupon applies to (null = all) |

---

## Frontend Integration

### Vue 3 / Nuxt 3 Example:

```typescript
// composables/useCoupons.ts
export const useCoupons = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const getCoupons = async (params?: any) => {
    return await $fetch('/api/marketing/coupons', {
      baseURL: config.public.apiBase,
      headers: {
        Authorization: `Bearer ${authStore.token}`
      },
      params
    })
  }

  const createCoupon = async (data: any) => {
    return await $fetch('/api/marketing/coupons', {
      method: 'POST',
      baseURL: config.public.apiBase,
      headers: {
        Authorization: `Bearer ${authStore.token}`
      },
      body: data
    })
  }

  const validateCoupon = async (code: string, customerId: number, cartAmount: number) => {
    return await $fetch('/api/marketing/coupons/validate', {
      method: 'POST',
      baseURL: config.public.apiBase,
      headers: {
        Authorization: `Bearer ${authStore.token}`
      },
      body: { code, customer_id: customerId, cart_amount: cartAmount }
    })
  }

  return {
    getCoupons,
    createCoupon,
    validateCoupon
  }
}
```

---

**Last Updated:** 2025-01-13
