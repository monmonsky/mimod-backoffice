# Mimod Backoffice - API Documentation

> RESTful API dengan Laravel Sanctum Authentication

## 📋 Table of Contents

- [Authentication](#authentication)
- [Response Pattern](#response-pattern)
- [API Endpoints](#api-endpoints)
  - [Auth API](#auth-api)
  - [Catalog API](#catalog-api)
  - [Settings API](#settings-api)
- [Usage Examples](#usage-examples)
- [Error Handling](#error-handling)

---

## 🔐 Authentication

Semua endpoint API (kecuali login) memerlukan autentikasi menggunakan **Laravel Sanctum Bearer Token**.

### Get Token

```bash
POST /api/auth/login
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
      "name": "Admin",
      "email": "admin@example.com"
    },
    "token": "1|abc123xyz..."
  }
}
```

### Use Token

Setiap request harus menyertakan token di header:

```bash
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 📦 Response Pattern

Semua API menggunakan **ResultBuilder Pattern** untuk response yang konsisten.

### Success Response

```json
{
  "status": true,
  "statusCode": "200",
  "message": "Success message",
  "data": {
    // Response data
  }
}
```

### Error Response

```json
{
  "status": false,
  "statusCode": "404",
  "message": "Error message"
}
```

### Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `204` | No Content / Not Found |
| `404` | Resource Not Found |
| `422` | Validation Error |
| `500` | Internal Server Error |

---

## 🔌 API Endpoints

### Auth API

**Base:** `/api/auth`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/login` | Login dan dapatkan token | ❌ |
| POST | `/logout` | Logout (hapus token saat ini) | ✅ |
| POST | `/logout-all` | Logout semua device | ✅ |
| GET | `/me` | Get user info | ✅ |
| GET | `/sessions` | Get active sessions | ✅ |

---

### Catalog API

**Base:** `/api/catalog`

#### Products

| Method | Endpoint | Description | Query Params |
|--------|----------|-------------|--------------|
| GET | `/products` | Get all products | `status`, `is_featured`, `brand_id`, `search`, `per_page` |
| GET | `/products/featured` | Get featured products | `limit` (default: 10) |
| GET | `/products/{id}` | Get product by ID | - |
| GET | `/products/{slug}` | Get product by slug | - |
| GET | `/products/{id}/variants` | Get product variants | - |
| GET | `/products/{id}/images` | Get product images | - |
| GET | `/products/category/{categoryId}` | Get products by category | `per_page` |
| GET | `/products/brand/{brandId}` | Get products by brand | `per_page` |

**Example Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Products retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Carters Baby Bodysuit",
        "slug": "carters-baby-bodysuit",
        "description": "Soft cotton bodysuit",
        "brand_id": 1,
        "status": "active",
        "is_featured": 1,
        "created_at": "2025-10-06T10:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

#### Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/categories` | Get all categories |
| GET | `/categories/tree` | Get category tree (hierarchical) |
| GET | `/categories/parents` | Get parent categories only |
| GET | `/categories/{id}` | Get single category |
| GET | `/categories/{parentId}/children` | Get child categories |

**Example Response (Tree):**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Category tree retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Baby Clothing",
      "slug": "baby-clothing",
      "children": [
        {
          "id": 2,
          "name": "Bodysuits",
          "slug": "bodysuits",
          "children": []
        }
      ]
    }
  ]
}
```

#### Brands

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/brands` | Get all active brands |
| GET | `/brands/{id}` | Get single brand |

---

### Settings API

**Base:** `/api/settings`

#### General Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/general` | Get all general settings |
| GET | `/general/{key}` | Get specific setting |
| PUT | `/general/{key}` | Update setting |
| GET | `/general/store/info` | Get store info |
| GET | `/general/email/settings` | Get email settings |
| GET | `/general/seo/settings` | Get SEO settings |
| GET | `/general/system/config` | Get system config |

**Example:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting retrieved successfully",
  "data": {
    "key": "store.info",
    "value": {
      "name": "Mimod Store",
      "tagline": "Kids Clothing",
      "description": "Best baby clothes"
    }
  }
}
```

#### Payment Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/payment` | Get all payment settings |
| GET | `/payment/{key}` | Get specific payment setting |
| PUT | `/payment/{key}` | Update payment setting |
| GET | `/payment/tax/settings` | Get tax settings |
| GET | `/payment/midtrans/config` | Get Midtrans config |
| GET | `/payment/methods/list` | Get payment methods |

#### Shipping Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/shipping` | Get all shipping settings |
| GET | `/shipping/{key}` | Get specific shipping setting |
| PUT | `/shipping/{key}` | Update shipping setting |
| GET | `/shipping/origin/address` | Get origin address |
| GET | `/shipping/rajaongkir/config` | Get RajaOngkir config |
| GET | `/shipping/methods/list` | Get shipping methods |

---

## 💻 Usage Examples

### JavaScript (Fetch)

```javascript
// 1. Login
const login = async () => {
  const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      email: 'admin@example.com',
      password: 'password'
    })
  });

  const result = await response.json();
  const token = result.data.token;

  // Save token
  localStorage.setItem('api_token', token);
  return token;
};

// 2. Get Products
const getProducts = async (token) => {
  const response = await fetch('/api/catalog/products?status=active&per_page=20', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });

  const result = await response.json();
  console.log(result.data);
};

// 3. Get Product by Slug
const getProduct = async (token, slug) => {
  const response = await fetch(`/api/catalog/products/${slug}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });

  const result = await response.json();
  return result.data;
};

// 4. Update Settings
const updateSettings = async (token) => {
  const response = await fetch('/api/settings/general/store.info', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      value: {
        name: 'New Store Name',
        tagline: 'New Tagline'
      }
    })
  });

  const result = await response.json();
  console.log(result.message);
};
```

### cURL

```bash
# 1. Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'

# 2. Get Products
curl -X GET "http://localhost:8000/api/catalog/products?status=active" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 3. Get Featured Products
curl -X GET "http://localhost:8000/api/catalog/products/featured?limit=5" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 4. Get Category Tree
curl -X GET "http://localhost:8000/api/catalog/categories/tree" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 5. Get Store Info
curl -X GET "http://localhost:8000/api/settings/general/store.info" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# 6. Update Settings
curl -X PUT "http://localhost:8000/api/settings/general/store.info" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"value": {"name": "New Store Name"}}'
```

### Axios (Vue/React)

```javascript
import axios from 'axios';

// Setup axios instance
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Add token to requests
api.interceptors.request.use(config => {
  const token = localStorage.getItem('api_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Usage
const fetchProducts = async () => {
  try {
    const response = await api.get('/catalog/products', {
      params: {
        status: 'active',
        per_page: 20
      }
    });

    if (response.data.status) {
      console.log(response.data.data);
    }
  } catch (error) {
    console.error(error.response.data.message);
  }
};
```

---

## ⚠️ Error Handling

### 401 Unauthorized

Token tidak valid atau expired:

```json
{
  "message": "Unauthenticated."
}
```

**Solution:** Login ulang untuk mendapatkan token baru.

### 404 Not Found

Resource tidak ditemukan:

```json
{
  "status": false,
  "statusCode": "404",
  "message": "Product not found"
}
```

### 422 Validation Error

Data tidak valid:

```json
{
  "status": false,
  "statusCode": "422",
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 500 Internal Server Error

Server error:

```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to retrieve data: [error details]"
}
```

---

## 🎯 API Pattern Implementation

### Controller Pattern

```php
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;

class ProductApiController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->productRepo->table();

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $products = $query->paginate($request->per_page ?? 15);

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Products retrieved successfully')
                ->setData($products)
                ->build();

            return Response::success($result);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve products: ' . $e->getMessage())
                ->build();

            return Response::error($result);
        }
    }
}
```

### Route Pattern

```php
// routes/api.php

// Public routes
Route::post('auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth.sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Catalog routes
    Route::prefix('catalog')->group(function () {
        Route::get('products', [ProductApiController::class, 'index']);
        Route::get('products/{id}', [ProductApiController::class, 'show']);
    });

    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('general/{key}', [GeneralSettingsApiController::class, 'show']);
        Route::put('general/{key}', [GeneralSettingsApiController::class, 'update']);
    });
});
```

### Repository Pattern

```php
// Interface
interface ProductRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function findBySlug($slug);
}

// Implementation
class ProductRepository implements ProductRepositoryInterface
{
    protected $table = 'products';

    public function getAll()
    {
        return DB::table($this->table)
            ->where('status', 'active')
            ->get();
    }

    public function findById($id)
    {
        return DB::table($this->table)->find($id);
    }

    public function findBySlug($slug)
    {
        return DB::table($this->table)
            ->where('slug', $slug)
            ->first();
    }
}
```

### ResultBuilder Pattern

```php
// app/Http/Responses/GeneralResponse/ResultBuilder.php
class ResultBuilder
{
    private $status;
    private $statusCode;
    private $message;
    private $data;

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setStatusCode(string $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    public function build(): array
    {
        return [
            'status' => $this->status,
            'statusCode' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}

// app/Http/Responses/GeneralResponse/Response.php
class Response
{
    public static function success(array $result)
    {
        return response()->json($result, (int)$result['statusCode']);
    }

    public static function error(array $result)
    {
        return response()->json($result, (int)$result['statusCode']);
    }
}
```

---

## 📝 Notes

- Semua endpoint menggunakan **ResultBuilder Pattern** untuk konsistensi response
- Token Sanctum memiliki expiration time (default: tidak expired)
- Untuk logout, token akan dihapus dari database
- Gunakan `logout-all` untuk menghapus semua token user di semua device
- Pagination default: 15 items per page
- Filter dan search tersedia di endpoint products

---

**Built with ❤️ for Mimod Kids Clothing Marketplace**
