# Backoffice API Documentation

This API is used by the frontend backoffice application for managing the e-commerce platform.

## Base URL
```
https://api-local.minimoda.id/api
```

## Authentication

### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "admin"
    },
    "token": "1|abcdef123456..."
  }
}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

### Protected Routes
All endpoints below require authentication header:
```
Authorization: Bearer {token}
```

---

## Dashboard

### Get Dashboard Statistics
```http
GET /dashboard/statistics
```

**Response:**
```json
{
  "status": true,
  "data": {
    "orders": {
      "total": 150,
      "pending": 20,
      "processing": 30,
      "completed": 90,
      "cancelled": 10
    },
    "revenue": {
      "total": 15000000,
      "today": 500000,
      "this_week": 2500000,
      "this_month": 8000000
    },
    "products": {
      "total": 500,
      "active": 450,
      "inactive": 50,
      "out_of_stock": 15
    },
    "customers": {
      "total": 1200,
      "active": 800,
      "new_this_month": 50
    }
  }
}
```

### Get Sales Chart Data
```http
GET /dashboard/sales-chart?period=week
```

**Query Parameters:**
- `period`: `week` | `month` | `year`

**Response:**
```json
{
  "status": true,
  "data": {
    "labels": ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    "datasets": [
      {
        "label": "Sales",
        "data": [150000, 200000, 180000, 250000, 300000, 400000, 350000]
      }
    ]
  }
}
```

### Get Recent Orders
```http
GET /dashboard/recent-orders?limit=10
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "order_number": "ORD-2025-001",
      "customer": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "total": 250000,
      "status": "pending",
      "created_at": "2025-10-25T10:30:00Z"
    }
  ]
}
```

### Get Top Products
```http
GET /dashboard/top-products?limit=10
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "sku": "PRD-001",
      "image": "https://example.com/products/1.jpg",
      "sales_count": 150,
      "revenue": 3750000,
      "stock": 50
    }
  ]
}
```

---

## Catalog Management

### Products

#### List Products
```http
GET /catalog/products?page=1&per_page=20&search=&category_id=&brand_id=&status=
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20)
- `search`: Search by name or SKU
- `category_id`: Filter by category
- `brand_id`: Filter by brand
- `status`: `active` | `inactive`

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Product Name",
        "slug": "product-name",
        "sku": "PRD-001",
        "price": 100000,
        "special_price": 80000,
        "stock": 50,
        "status": "active",
        "category": {
          "id": 1,
          "name": "Category Name"
        },
        "brand": {
          "id": 1,
          "name": "Brand Name"
        },
        "images": [
          {
            "id": 1,
            "url": "https://example.com/products/1.jpg",
            "is_primary": true
          }
        ],
        "created_at": "2025-10-25T10:30:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

#### Create Product
```http
POST /catalog/products
Content-Type: application/json

{
  "name": "New Product",
  "slug": "new-product",
  "sku": "PRD-002",
  "description": "Product description",
  "short_description": "Short description",
  "price": 100000,
  "special_price": 80000,
  "category_id": 1,
  "brand_id": 1,
  "stock": 50,
  "weight": 500,
  "status": "active",
  "meta_title": "SEO Title",
  "meta_description": "SEO Description",
  "meta_keywords": "keyword1, keyword2"
}
```

#### Get Product Details
```http
GET /catalog/products/{id}
```

**Response:** Same as single product object in list

#### Update Product
```http
PUT /catalog/products/{id}
Content-Type: application/json
```
Body: Same as create product

#### Delete Product
```http
DELETE /catalog/products/{id}
```

#### Update Product Status
```http
PATCH /catalog/products/{id}/status
Content-Type: application/json

{
  "status": "active"
}
```

#### Get Product Variants
```http
GET /catalog/products/{id}/variants
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "sku": "PRD-001-RED-S",
      "barcode": "1234567890",
      "price": 100000,
      "special_price": 80000,
      "stock": 20,
      "weight": 500,
      "attributes": [
        {
          "attribute": {
            "id": 1,
            "name": "Color"
          },
          "value": {
            "id": 1,
            "value": "Red"
          }
        },
        {
          "attribute": {
            "id": 2,
            "name": "Size"
          },
          "value": {
            "id": 5,
            "value": "S"
          }
        }
      ],
      "images": []
    }
  ]
}
```

### Product Variants

#### Get Variant Details
```http
GET /catalog/products/variants/{id}
```

#### Create Product Variant
```http
POST /catalog/products/variants
Content-Type: application/json

{
  "product_id": 1,
  "sku": "PRD-001-RED-S",
  "barcode": "1234567890",
  "price": 100000,
  "special_price": 80000,
  "stock": 20,
  "weight": 500
}
```

#### Update Product Variant
```http
PUT /catalog/products/variants/{id}
Content-Type: application/json
```
Body: Same as create variant

#### Delete Product Variant
```http
DELETE /catalog/products/variants/{id}
```

#### Generate SKU and Barcode
```http
POST /catalog/products/variants/{id}/generate-sku-barcode
```

**Response:**
```json
{
  "status": true,
  "data": {
    "sku": "PRD-001-001",
    "barcode": "1234567890123"
  }
}
```

### Categories

#### List Categories
```http
GET /catalog/categories?page=1&per_page=20&search=
```

#### Create Category
```http
POST /catalog/categories
Content-Type: application/json

{
  "name": "Category Name",
  "slug": "category-name",
  "description": "Description",
  "parent_id": null,
  "is_active": true,
  "meta_title": "SEO Title",
  "meta_description": "SEO Description"
}
```

#### Get Category Details
```http
GET /catalog/categories/{id}
```

#### Update Category
```http
PUT /catalog/categories/{id}
Content-Type: application/json
```
Body: Same as create category

#### Delete Category
```http
DELETE /catalog/categories/{id}
```

### Brands

#### List Brands
```http
GET /catalog/brands?page=1&per_page=20&search=
```

#### Create Brand
```http
POST /catalog/brands
Content-Type: application/json

{
  "name": "Brand Name",
  "slug": "brand-name",
  "description": "Description",
  "logo": "https://example.com/brands/logo.jpg",
  "is_active": true,
  "meta_title": "SEO Title",
  "meta_description": "SEO Description"
}
```

#### Get Brand Details
```http
GET /catalog/brands/{id}
```

#### Update Brand
```http
PUT /catalog/brands/{id}
Content-Type: application/json
```
Body: Same as create brand

#### Delete Brand
```http
DELETE /catalog/brands/{id}
```

### Product Attributes

#### List Attributes
```http
GET /catalog/attributes?page=1&per_page=20
```

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Color",
        "code": "color",
        "type": "select",
        "is_required": true,
        "is_filterable": true,
        "values": [
          {
            "id": 1,
            "value": "Red",
            "sort_order": 1
          },
          {
            "id": 2,
            "value": "Blue",
            "sort_order": 2
          }
        ]
      }
    ]
  }
}
```

#### Create Attribute
```http
POST /catalog/attributes
Content-Type: application/json

{
  "name": "Color",
  "code": "color",
  "type": "select",
  "is_required": true,
  "is_filterable": true
}
```

#### Get Attribute Details
```http
GET /catalog/attributes/{id}
```

#### Update Attribute
```http
PUT /catalog/attributes/{id}
Content-Type: application/json
```

#### Delete Attribute
```http
DELETE /catalog/attributes/{id}
```

### Product Attribute Values

#### Create Attribute Value
```http
POST /catalog/attribute-values
Content-Type: application/json

{
  "product_attribute_id": 1,
  "value": "Red",
  "sort_order": 1
}
```

#### Bulk Create Attribute Values
```http
POST /catalog/attribute-values/bulk
Content-Type: application/json

{
  "product_attribute_id": 1,
  "values": [
    {"value": "Red", "sort_order": 1},
    {"value": "Blue", "sort_order": 2},
    {"value": "Green", "sort_order": 3}
  ]
}
```

#### Update Attribute Value
```http
PUT /catalog/attribute-values/{id}
Content-Type: application/json

{
  "value": "Dark Red",
  "sort_order": 1
}
```

#### Delete Attribute Value
```http
DELETE /catalog/attribute-values/{id}
```

### Product Variant Attributes

#### Assign Attributes to Variant
```http
POST /catalog/product-variant-attributes
Content-Type: application/json

{
  "product_variant_id": 1,
  "product_attribute_id": 1,
  "product_attribute_value_id": 2
}
```

---

## Order Management

### List Orders
```http
GET /orders?page=1&per_page=20&search=&status=&payment_status=&date_from=&date_to=
```

**Query Parameters:**
- `search`: Search by order number or customer name
- `status`: `pending` | `processing` | `completed` | `cancelled`
- `payment_status`: `pending` | `paid` | `failed`
- `date_from`: Filter from date (Y-m-d)
- `date_to`: Filter to date (Y-m-d)

### Create Order
```http
POST /orders
Content-Type: application/json

{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "product_variant_id": null,
      "quantity": 2,
      "price": 100000
    }
  ],
  "shipping_address": {
    "name": "John Doe",
    "phone": "081234567890",
    "address": "Jl. Example No. 123",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12345"
  },
  "shipping_method": "jne",
  "shipping_cost": 15000,
  "coupon_code": "DISC10",
  "notes": "Please pack carefully"
}
```

### Get Order Details
```http
GET /orders/{id}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "order_number": "ORD-2025-001",
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "status": "pending",
    "payment_status": "pending",
    "subtotal": 200000,
    "discount": 20000,
    "shipping_cost": 15000,
    "total": 195000,
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Product Name",
          "sku": "PRD-001"
        },
        "variant": null,
        "quantity": 2,
        "price": 100000,
        "subtotal": 200000
      }
    ],
    "shipping_address": {
      "name": "John Doe",
      "phone": "081234567890",
      "address": "Jl. Example No. 123",
      "city": "Jakarta",
      "province": "DKI Jakarta",
      "postal_code": "12345"
    },
    "created_at": "2025-10-25T10:30:00Z"
  }
}
```

### Update Order
```http
PUT /orders/{id}
Content-Type: application/json
```
Body: Same as create order

### Delete Order
```http
DELETE /orders/{id}
```

### Update Order Status
```http
PATCH /orders/{id}/status
Content-Type: application/json

{
  "status": "processing",
  "notes": "Order is being processed"
}
```

### Update Payment Status
```http
PATCH /orders/{id}/payment
Content-Type: application/json

{
  "payment_status": "paid",
  "payment_method": "bank_transfer",
  "payment_date": "2025-10-25"
}
```

### Send Invoice Email
```http
POST /orders/{id}/send-invoice
```

### Get Orders by Customer
```http
GET /orders/customer/{customerId}?page=1&per_page=20
```

---

## Customer Management

### List Customers
```http
GET /customers?page=1&per_page=20&search=&status=
```

**Query Parameters:**
- `search`: Search by name, email, or phone
- `status`: `active` | `inactive`

### Create Customer
```http
POST /customers
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "081234567890",
  "password": "password123",
  "date_of_birth": "1990-01-01",
  "gender": "male",
  "status": "active"
}
```

### Get Customer Details
```http
GET /customers/{id}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "customer_code": "CUST000001",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "date_of_birth": "1990-01-01",
    "gender": "male",
    "status": "active",
    "email_verified_at": "2025-10-25T10:30:00Z",
    "loyalty_points": 100,
    "total_orders": 10,
    "total_spent": 5000000,
    "created_at": "2025-01-01T00:00:00Z"
  }
}
```

### Update Customer
```http
PUT /customers/{id}
Content-Type: application/json
```
Body: Same as create customer (password is optional)

### Delete Customer
```http
DELETE /customers/{id}
```

---

## Marketing

### Coupons

#### List Coupons
```http
GET /marketing/coupons?page=1&per_page=20&search=&status=
```

**Query Parameters:**
- `search`: Search by code or name
- `status`: `active` | `inactive` | `expired`

#### Create Coupon
```http
POST /marketing/coupons
Content-Type: application/json

{
  "code": "DISC10",
  "name": "10% Discount",
  "type": "percentage",
  "value": 10,
  "min_purchase": 100000,
  "max_discount": 50000,
  "usage_limit": 100,
  "usage_per_customer": 1,
  "valid_from": "2025-10-01",
  "valid_to": "2025-10-31",
  "is_active": true
}
```

**Coupon Types:**
- `percentage`: Discount by percentage (value: 10 = 10%)
- `fixed`: Fixed amount discount (value: 50000 = Rp 50.000)
- `free_shipping`: Free shipping

#### Get Coupon Details
```http
GET /marketing/coupons/{id}
```

#### Update Coupon
```http
PUT /marketing/coupons/{id}
Content-Type: application/json
```
Body: Same as create coupon

#### Delete Coupon
```http
DELETE /marketing/coupons/{id}
```

#### Validate Coupon
```http
POST /marketing/coupons/validate
Content-Type: application/json

{
  "code": "DISC10",
  "customer_id": 1,
  "subtotal": 200000
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "valid": true,
    "coupon": {
      "id": 1,
      "code": "DISC10",
      "type": "percentage",
      "value": 10
    },
    "discount_amount": 20000,
    "message": "Coupon applied successfully"
  }
}
```

---

## Access Control

### Users

#### List Users
```http
GET /access-control/users?page=1&per_page=20&search=&role_id=
```

#### Create User
```http
POST /access-control/users
Content-Type: application/json

{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password123",
  "role_id": 1,
  "is_active": true
}
```

#### Get User Details
```http
GET /access-control/users/{id}
```

#### Update User
```http
PUT /access-control/users/{id}
Content-Type: application/json
```
Body: Same as create user (password is optional)

#### Delete User
```http
DELETE /access-control/users/{id}
```

### Roles

#### List Roles
```http
GET /access-control/roles?page=1&per_page=20
```

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Super Admin",
        "description": "Full access to all features",
        "users_count": 2,
        "permissions_count": 50
      }
    ]
  }
}
```

#### Get Role Details
```http
GET /access-control/roles/{id}
```

#### Update Role
```http
PUT /access-control/roles/{id}
Content-Type: application/json

{
  "name": "Admin",
  "description": "Administrator role"
}
```

#### Get Grouped Permissions for Role
```http
GET /access-control/roles/{id}/permissions/grouped
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "module": {
        "id": 1,
        "name": "Products",
        "icon": "box"
      },
      "permissions": [
        {
          "id": 1,
          "name": "products:read",
          "display_name": "View Products",
          "assigned": true
        },
        {
          "id": 2,
          "name": "products:create",
          "display_name": "Create Products",
          "assigned": true
        }
      ]
    }
  ]
}
```

#### Sync Role Permissions
```http
POST /access-control/roles/{id}/permissions/sync
Content-Type: application/json

{
  "permission_ids": [1, 2, 3, 4, 5]
}
```

### Modules

#### List Modules
```http
GET /access-control/modules
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "name": "Products",
      "slug": "products",
      "icon": "box",
      "sort_order": 1,
      "is_active": true,
      "permissions_count": 4
    }
  ]
}
```

#### Get Module Details
```http
GET /access-control/modules/{id}
```

#### Update Module
```http
PUT /access-control/modules/{id}
Content-Type: application/json

{
  "name": "Product Management",
  "icon": "box",
  "is_active": true
}
```

#### Reorder Modules
```http
POST /access-control/modules/reorder
Content-Type: application/json

{
  "modules": [
    {"id": 1, "sort_order": 1},
    {"id": 2, "sort_order": 2},
    {"id": 3, "sort_order": 3}
  ]
}
```

### Store Tokens (API Access)

#### List Store Tokens
```http
GET /access-control/store-tokens?page=1&per_page=20
```

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Store Frontend",
        "token_preview": "1|abc***def",
        "abilities": ["products:read", "categories:read"],
        "last_used_at": "2025-10-25T10:30:00Z",
        "expires_at": null,
        "created_at": "2025-01-01T00:00:00Z"
      }
    ]
  }
}
```

#### Get Token Statistics
```http
GET /access-control/store-tokens/stats
```

**Response:**
```json
{
  "status": true,
  "data": {
    "total": 5,
    "active": 4,
    "expired": 1,
    "never_used": 2
  }
}
```

#### Get Token Details
```http
GET /access-control/store-tokens/{id}
```

#### Generate New Store Token
```http
POST /access-control/store-tokens/generate
Content-Type: application/json

{
  "name": "Store Frontend",
  "abilities": ["products:read", "categories:read", "brands:read"],
  "expires_at": null
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "name": "Store Frontend",
    "token": "1|abcdef123456...",
    "abilities": ["products:read", "categories:read"],
    "expires_at": null
  },
  "message": "Token generated successfully. Please save this token as it won't be shown again."
}
```

#### Delete Store Token
```http
DELETE /access-control/store-tokens/{id}
```

### User Activities

#### List User Activities
```http
GET /access-control/user-activities?page=1&per_page=20&user_id=&action=&date_from=&date_to=
```

**Query Parameters:**
- `user_id`: Filter by user
- `action`: Filter by action (create, update, delete, login, logout)
- `date_from`: Filter from date
- `date_to`: Filter to date

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "user": {
          "id": 1,
          "name": "Admin User"
        },
        "action": "create",
        "subject_type": "Product",
        "subject_id": 1,
        "description": "Created product: Product Name",
        "ip_address": "192.168.1.1",
        "user_agent": "Mozilla/5.0...",
        "created_at": "2025-10-25T10:30:00Z"
      }
    ]
  }
}
```

#### Get Activity Details
```http
GET /access-control/user-activities/{id}
```

---

## File Upload

### Upload Single Image
```http
POST /upload/image
Content-Type: multipart/form-data

file: [image file]
folder: products
product_id: 1
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "url": "https://example.com/products/image.jpg",
    "path": "products/image.jpg",
    "size": 102400,
    "mime_type": "image/jpeg"
  }
}
```

### Upload Multiple Images
```http
POST /upload/image/bulk
Content-Type: multipart/form-data

files[]: [image file 1]
files[]: [image file 2]
folder: products
product_id: 1
```

### Upload Media (Image or Video)
```http
POST /upload/media
Content-Type: multipart/form-data

file: [image or video file]
folder: products
```

### Upload to Temporary Storage
```http
POST /upload/temp
Content-Type: multipart/form-data

file: [file]
```

**Response:**
```json
{
  "status": true,
  "data": {
    "temp_path": "temp/abc123.jpg",
    "url": "https://example.com/temp/abc123.jpg"
  }
}
```

### Move from Temp to Permanent Storage
```http
POST /upload/move
Content-Type: application/json

{
  "temp_path": "temp/abc123.jpg",
  "folder": "products",
  "product_id": 1
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "url": "https://example.com/products/abc123.jpg",
    "path": "products/abc123.jpg"
  }
}
```

### Set Primary Product Image
```http
PATCH /upload/product-image/{id}/set-primary
```

### Delete Product Image
```http
DELETE /upload/product-image/{id}
```

---

## Appearance - Navigation

### List Menus
```http
GET /appearance/navigation/menus?page=1&per_page=20&location=
```

**Query Parameters:**
- `location`: `header` | `footer` | `sidebar`

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Home",
        "url": "/",
        "location": "header",
        "parent_id": null,
        "sort_order": 1,
        "is_active": true,
        "children": [
          {
            "id": 2,
            "title": "About",
            "url": "/about",
            "parent_id": 1,
            "sort_order": 1
          }
        ]
      }
    ]
  }
}
```

### Create Menu
```http
POST /appearance/navigation/menus
Content-Type: application/json

{
  "title": "Home",
  "url": "/",
  "location": "header",
  "parent_id": null,
  "sort_order": 1,
  "target": "_self",
  "is_active": true
}
```

### Get Parent Menus
```http
GET /appearance/navigation/menus/parents?location=header
```

### Reorder Menus
```http
POST /appearance/navigation/menus/reorder
Content-Type: application/json

{
  "menus": [
    {"id": 1, "sort_order": 1, "parent_id": null},
    {"id": 2, "sort_order": 2, "parent_id": null},
    {"id": 3, "sort_order": 1, "parent_id": 1}
  ]
}
```

### Bulk Create from Categories
```http
POST /appearance/navigation/menus/bulk-create-categories
Content-Type: application/json

{
  "location": "header",
  "parent_id": null,
  "category_ids": [1, 2, 3]
}
```

### Bulk Create from Brands
```http
POST /appearance/navigation/menus/bulk-create-brands
Content-Type: application/json

{
  "location": "header",
  "parent_id": null,
  "brand_ids": [1, 2, 3]
}
```

### Get Menu Details
```http
GET /appearance/navigation/menus/{id}
```

### Update Menu
```http
PUT /appearance/navigation/menus/{id}
Content-Type: application/json
```
Body: Same as create menu

### Delete Menu
```http
DELETE /appearance/navigation/menus/{id}
```

### Get Menu by Location (Public)
```http
GET /menus/location?location=header
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "title": "Home",
      "url": "/",
      "target": "_self",
      "children": [
        {
          "id": 2,
          "title": "About",
          "url": "/about",
          "target": "_self"
        }
      ]
    }
  ]
}
```

---

## Settings

### Get Setting
```http
GET /settings/{key}
```

**Example:**
```http
GET /settings/site_name
```

**Response:**
```json
{
  "status": true,
  "data": {
    "key": "site_name",
    "value": "Minimoda Store",
    "type": "text"
  }
}
```

### Update Setting
```http
PUT /settings/{key}
Content-Type: application/json

{
  "value": "New Store Name"
}
```

---

## Email

### Test Email Connection
```http
POST /email/test-connection
Content-Type: application/json

{
  "email": "test@example.com"
}
```

**Response:**
```json
{
  "status": true,
  "message": "Test email sent successfully"
}
```

---

## AI/SEO

### Generate SEO Content
```http
POST /ai/generate-seo
Content-Type: application/json

{
  "product_name": "Premium Cotton T-Shirt",
  "description": "High quality cotton t-shirt",
  "category": "Clothing",
  "brand": "Fashion Brand"
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "meta_title": "Premium Cotton T-Shirt - Fashion Brand | Minimoda Store",
    "meta_description": "Shop Premium Cotton T-Shirt from Fashion Brand. High quality cotton t-shirt...",
    "meta_keywords": "cotton t-shirt, premium t-shirt, fashion brand, clothing",
    "slug": "premium-cotton-t-shirt"
  }
}
```

---

## Error Responses

All error responses follow this format:

```json
{
  "status": false,
  "statusCode": "400",
  "message": "Error message here",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

**Common HTTP Status Codes:**
- `200`: Success
- `201`: Created
- `400`: Bad Request / Validation Error
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Unprocessable Entity
- `500`: Internal Server Error

---

## Payment Methods

### Get All Payment Methods
```http
GET /payment-methods
```

**Query Parameters:**
- `is_active`: Filter by active status (true/false)
- `type`: Filter by type (bank_transfer, virtual_account, e_wallet, qris, credit_card, cod)
- `provider`: Filter by provider (manual, midtrans, xendit, etc.)
- `search`: Search by name or code
- `sort_by`: Sort field (default: sort_order)
- `sort_order`: Sort direction (asc/desc, default: asc)
- `per_page`: Items per page (default: 15, or 'all' for no pagination)

**Response:**
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "code": "bank_transfer_bca",
        "name": "Bank Transfer BCA",
        "type": "bank_transfer",
        "provider": "manual",
        "logo_url": null,
        "description": "Transfer manual ke rekening BCA",
        "instructions": "Transfer ke rekening...",
        "fee_percentage": 0,
        "fee_fixed": 0,
        "min_amount": null,
        "max_amount": null,
        "expired_duration": 1440,
        "is_active": true,
        "sort_order": 0
      }
    ],
    "pagination": {...}
  }
}
```

### Get Payment Method by ID
```http
GET /payment-methods/{id}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "payment_method": {...},
    "config": {
      "provider_config": "true",
      "environment": "sandbox",
      "sandbox_server_key": "SB-Mid-...",
      "sandbox_client_key": "SB-Mid-...",
      "enable_3ds": "true"
    }
  }
}
```

### Create Payment Method
```http
POST /payment-methods
Content-Type: application/json

{
  "code": "midtrans_dana",
  "name": "DANA via Midtrans",
  "type": "e_wallet",
  "provider": "midtrans",
  "logo_url": "https://...",
  "description": "E-Wallet DANA",
  "instructions": "Scan QR code...",
  "fee_percentage": 1.5,
  "fee_fixed": 0,
  "min_amount": 10000,
  "max_amount": 10000000,
  "expired_duration": 30,
  "is_active": true,
  "sort_order": 10
}
```

### Update Payment Method
```http
PUT /payment-methods/{id}
Content-Type: application/json

{
  "name": "Updated Name",
  "is_active": false,
  ...
}
```

### Delete Payment Method
```http
DELETE /payment-methods/{id}
```

**Note:** Cannot delete if used in orders. Will return error with suggestion to deactivate instead.

### Toggle Payment Method Status
```http
POST /payment-methods/{id}/toggle-active
```

### Update Payment Method Configuration (Legacy)
```http
POST /payment-methods/{id}/config
Content-Type: application/json

{
  "configs": [
    {
      "key": "environment",
      "value": "production",
      "is_encrypted": false
    },
    {
      "key": "production_server_key",
      "value": "Mid-server-...",
      "is_encrypted": true
    }
  ]
}
```

**Note:** This endpoint still works but **it's recommended to use the dedicated Config API endpoints** for better granularity and RESTful structure. See [Dedicated Config API](#payment-method-config-api-dedicated) below.

---

### Payment Method Config API (Dedicated)

New dedicated endpoints for more granular config management:

#### Get All Configs (Merged)
```http
GET /payment-methods/{id}/configs
```
Returns all configs merged from provider-level and method-specific.

#### Get Single Config
```http
GET /payment-methods/{id}/configs/{key}
```
Example: `GET /payment-methods/4/configs/environment`

#### Update Single Config
```http
PUT /payment-methods/{id}/configs/{key}
Content-Type: application/json

{
  "value": "production",
  "is_encrypted": false
}
```

#### Bulk Update Configs
```http
POST /payment-methods/{id}/configs
Content-Type: application/json

{
  "configs": [
    { "key": "environment", "value": "production", "is_encrypted": false },
    { "key": "production_server_key", "value": "KEY", "is_encrypted": true }
  ]
}
```

#### Delete Config
```http
DELETE /payment-methods/{id}/configs/{key}
```
Removes method-specific override, falls back to provider config.

#### Get Provider-Level Configs
```http
GET /payment-methods/providers/{provider}/configs
```
Example: `GET /payment-methods/providers/midtrans/configs`

Returns the config holder method and all shared configs for that provider.

**See [CONFIG_API_ENDPOINTS.md](./CONFIG_API_ENDPOINTS.md) for detailed usage examples and use cases.**

---

### Global Payment Method Config API (NEW)

Manage global configs that can be shared across multiple payment methods:

#### List All Global Configs
```http
GET /payment-method-configs
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "name": "Midtrans Config",
      "provider": "midtrans",
      "description": "Shared configuration for all midtrans payment methods",
      "method_count": 8,
      "configs": {
        "environment": "sandbox",
        "server_key": "...",
        "client_key": "..."
      },
      "created_at": "2025-10-26...",
      "updated_at": "2025-10-26..."
    }
  ]
}
```

#### Get Single Global Config
```http
GET /payment-method-configs/{id}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 2,
    "name": "Midtrans Config",
    "provider": "midtrans",
    "description": "...",
    "configs": { /* all config items */ },
    "methods": [
      { "id": 4, "code": "midtrans_bca_va", "name": "BCA VA", "is_active": true },
      { "id": 5, "code": "midtrans_bni_va", "name": "BNI VA", "is_active": true }
    ],
    "created_at": "...",
    "updated_at": "..."
  }
}
```

#### Create Global Config
```http
POST /payment-method-configs
Content-Type: application/json

{
  "name": "Xendit Config",
  "provider": "xendit",
  "description": "Xendit payment gateway configuration",
  "configs": [
    { "key": "api_key", "value": "xnd_...", "is_encrypted": true },
    { "key": "webhook_token", "value": "whsec_...", "is_encrypted": true },
    { "key": "environment", "value": "sandbox", "is_encrypted": false }
  ]
}
```

**Response:** Returns the created global config with all details.

#### Update Global Config
```http
PUT /payment-method-configs/{id}
Content-Type: application/json

{
  "name": "Xendit Config (Updated)",
  "description": "Updated description",
  "configs": [
    { "key": "environment", "value": "production", "is_encrypted": false },
    { "key": "api_key", "value": "xnd_production_...", "is_encrypted": true }
  ]
}
```

**Note:** Config items are upserted (update if exists, insert if not).

#### Delete Global Config
```http
DELETE /payment-method-configs/{id}
```

**Response (Error if in use):**
```json
{
  "status": false,
  "statusCode": "400",
  "message": "Cannot delete global config that is being used by 8 payment method(s). Please unassign methods first."
}
```

**Response (Success):**
```json
{
  "status": true,
  "message": "Global config deleted successfully"
}
```

#### Delete Config Item
```http
DELETE /payment-method-configs/{id}/items/{key}
```

Removes a specific config key from the global config.

**Example:** `DELETE /payment-method-configs/2/items/enable_3ds`

---

**Important Notes on Configuration Architecture:**

The system uses **global config with foreign key reference** for efficient configuration management:

**New Config Structure (Database):**

The configuration system has been refactored to use a relational structure:

1. **Global Config Tables:**
   - `payment_method_configs` - Global config holder (id, name, provider, description)
   - `payment_method_config_items` - Config key-value pairs associated with global config
   - `payment_methods.payment_method_config_id` - Foreign key reference to global config

2. **Override Tables:**
   - `payment_method_config_overrides` - Method-specific config overrides
   - Allows individual methods to override specific configs from the global config

**How It Works:**

```
payment_method_configs:
  id: 1, name: "Midtrans Config", provider: "midtrans"

payment_method_config_items (global configs):
  - config_id: 1, key: "environment", value: "sandbox"
  - config_id: 1, key: "server_key", value: "xxx"
  - config_id: 1, key: "client_key", value: "yyy"

payment_methods:
  - id: 4, code: "midtrans_bca_va", payment_method_config_id: 1
  - id: 5, code: "midtrans_bni_va", payment_method_config_id: 1
  - id: 6, code: "midtrans_gopay", payment_method_config_id: 1
  (All reference the same global config)

payment_method_config_overrides (optional):
  - payment_method_id: 10, key: "acquirer", value: "gopay"
  (QRIS method has a unique override)
```

**Config Retrieval Priority:**
1. **Method-specific override** (from `payment_method_config_overrides`) - Highest priority
2. **Global config** (via `payment_method_config_id` foreign key) - Fallback
3. **Merged result** - Both combined and returned

**Benefits:**
- ✅ **Truly global config** - Config is independent from payment methods
- ✅ **Proper relational structure** - Foreign key relationships
- ✅ **Easy management** - Update one global config, affects all linked methods
- ✅ **Flexible overrides** - Individual methods can override specific settings
- ✅ **No duplication** - Credentials stored once, referenced many times
- ✅ **Easy environment switching** - Change sandbox ↔ production in one place

**Example:**
- All Midtrans methods (BCA VA, BNI VA, GoPay, QRIS, etc.) reference the same `payment_method_config_id: 2`
- Update Midtrans credentials once → automatically affects all 8 Midtrans methods
- QRIS can have a unique `acquirer` setting via override table

---

## Shipping Methods

### Get All Shipping Methods
```http
GET /shipping-methods
```

**Query Parameters:**
- `is_active`: Filter by active status (true/false)
- `type`: Filter by type (manual, rajaongkir, custom)
- `provider`: Filter by provider (jne, jnt, sicepat, pos, rajaongkir, etc.)
- `search`: Search by name or code
- `sort_by`: Sort field (default: sort_order)
- `sort_order`: Sort direction (asc/desc, default: asc)
- `per_page`: Items per page (default: 15, or 'all' for no pagination)

**Response:**
```json
{
  "status": true,
  "data": {
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
      }
    ],
    "pagination": {...}
  }
}
```

### Get Shipping Method by ID
```http
GET /shipping-methods/{id}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "shipping_method": {...},
    "config": {
      "provider_config": "true",
      "tracking_url": "https://www.jne.co.id/id/tracking/trace",
      "service_code": "REG"
    }
  }
}
```

### Create Shipping Method
```http
POST /shipping-methods
Content-Type: application/json

{
  "code": "anteraja_reg",
  "name": "AnterAja Reguler",
  "type": "manual",
  "provider": "anteraja",
  "base_cost": 8000,
  "cost_per_kg": 4000,
  "min_weight": null,
  "max_weight": 50000,
  "estimated_delivery": "3-4 hari",
  "is_active": true,
  "sort_order": 20
}
```

### Update Shipping Method
```http
PUT /shipping-methods/{id}
Content-Type: application/json

{
  "base_cost": 12000,
  "cost_per_kg": 6000,
  ...
}
```

### Delete Shipping Method
```http
DELETE /shipping-methods/{id}
```

**Note:** Cannot delete if used in orders. Will return error with suggestion to deactivate instead.

### Toggle Shipping Method Status
```http
POST /shipping-methods/{id}/toggle-active
```

### Update Shipping Method Configuration (Legacy)
```http
POST /shipping-methods/{id}/config
Content-Type: application/json

{
  "configs": [
    {
      "key": "api_key",
      "value": "your-rajaongkir-api-key",
      "is_encrypted": true
    },
    {
      "key": "origin_city_id",
      "value": "501",
      "is_encrypted": false
    }
  ]
}
```

**Note:** This endpoint still works but **it's recommended to use the dedicated Config API endpoints** for better granularity and RESTful structure. See [Dedicated Config API](#shipping-method-config-api-dedicated) below.

---

### Shipping Method Config API (Dedicated)

New dedicated endpoints for more granular config management:

#### Get All Configs (Merged)
```http
GET /shipping-methods/{id}/configs
```
Returns all configs merged from provider-level and method-specific.

#### Get Single Config
```http
GET /shipping-methods/{id}/configs/{key}
```
Example: `GET /shipping-methods/14/configs/api_key`

#### Update Single Config
```http
PUT /shipping-methods/{id}/configs/{key}
Content-Type: application/json

{
  "value": "new-rajaongkir-api-key",
  "is_encrypted": true
}
```

#### Bulk Update Configs
```http
POST /shipping-methods/{id}/configs
Content-Type: application/json

{
  "configs": [
    { "key": "api_key", "value": "KEY", "is_encrypted": true },
    { "key": "origin_city_id", "value": "501", "is_encrypted": false }
  ]
}
```

#### Delete Config
```http
DELETE /shipping-methods/{id}/configs/{key}
```
Removes method-specific override, falls back to provider config.

#### Get Provider-Level Configs
```http
GET /shipping-methods/providers/{provider}/configs
```
Examples:
- `GET /shipping-methods/providers/rajaongkir/configs`
- `GET /shipping-methods/providers/jne/configs`

Returns the config holder method and all shared configs for that provider.

**See [CONFIG_API_ENDPOINTS.md](./CONFIG_API_ENDPOINTS.md) for detailed usage examples and use cases.**

---

**Important Notes on Configuration Architecture:**

The system uses **provider-level shared configs** to reduce redundancy:

**Provider-Level Config (Shared):**
- One shipping method from each provider is designated as the config holder with `provider_config: true`
- All methods from the same provider automatically inherit these shared configs
- Examples:
  - **RajaOngkir**: All RajaOngkir methods (JNE, TIKI, POS) share `api_key`, `account_type`, `origin_city_id`, `origin_province_id`
  - **JNE**: All JNE methods (REG, YES) share `tracking_url`
  - **J&T**: All J&T methods (REG, Express) share `tracking_url`
  - **SiCepat**: All SiCepat methods (REG, BEST) share `tracking_url`
  - **GoSend/Grab**: Share `api_key`, `merchant_id`, `max_distance_km`

**Method-Specific Config (Override):**
- Individual methods can have unique configs
- Example RajaOngkir methods: Each has unique `courier_code` (jne/tiki/pos)
- Example Store Courier: `coverage_area`, `phone_number`, `notes`
- Example Free Shipping: `min_purchase`, `max_weight`

**Config Retrieval Priority:**
1. Method-specific config (if exists)
2. Provider-level config (fallback)
3. Merged result returned

**Benefits:**
- Update RajaOngkir API key once → affects all 3 RajaOngkir methods
- Update JNE tracking URL once → affects all JNE service types
- Reduced configs: 41 → 30 (27% reduction)

---

### Global Shipping Method Config API (NEW)

Manage global configs that can be shared across multiple shipping methods:

#### List All Global Configs
```http
GET /shipping-method-configs
```

#### Get Single Global Config
```http
GET /shipping-method-configs/{id}
```

#### Create Global Config
```http
POST /shipping-method-configs
Content-Type: application/json

{
  "name": "AnterAja Config",
  "provider": "anteraja",
  "description": "AnterAja shipping configuration",
  "configs": [
    { "key": "api_key", "value": "anteraja_api_key", "is_encrypted": true },
    { "key": "tracking_url", "value": "https://anteraja.id/track", "is_encrypted": false }
  ]
}
```

#### Update Global Config
```http
PUT /shipping-method-configs/{id}
Content-Type: application/json

{
  "configs": [
    { "key": "api_key", "value": "new_api_key", "is_encrypted": true }
  ]
}
```

#### Delete Global Config
```http
DELETE /shipping-method-configs/{id}
```

**Note:** Cannot delete if shipping methods are still using this config.

#### Delete Config Item
```http
DELETE /shipping-method-configs/{id}/items/{key}
```

**Same structure and behavior as Payment Method Global Configs.** See payment section above for detailed response examples.

---

### Calculate Shipping Cost
```http
POST /shipping-methods/{id}/calculate-cost
Content-Type: application/json

{
  "weight": 1500,
  "destination_city_id": 455,
  "destination_subdistrict_id": null
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "cost": 17500,
    "estimated_delivery": "2-3 hari",
    "type": "manual"
  }
}
```

**Note:**
- For manual/custom methods: Cost = base_cost + (cost_per_kg × weight_in_kg)
- For RajaOngkir methods: Will integrate with RajaOngkir API (currently not implemented)

---

## Rate Limiting

API requests are rate limited to prevent abuse:
- **Authenticated requests**: 1000 requests per minute
- **Unauthenticated requests**: 100 requests per minute

When rate limit is exceeded:
```json
{
  "status": false,
  "statusCode": "429",
  "message": "Too many requests. Please try again later."
}
```

---

## Pagination

All list endpoints support pagination with these parameters:
- `page`: Current page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)

Pagination response format:
```json
{
  "status": true,
  "data": {
    "data": [...],
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 200,
    "from": 1,
    "to": 20
  }
}
```

---

## Testing

Use tools like Postman, Insomnia, or cURL to test the API.

**Example cURL request:**
```bash
curl --location 'https://api-local.minimoda.id/api/catalog/products' \
--header 'Authorization: Bearer 1|abcdef123456...' \
--header 'Content-Type: application/json'
```
