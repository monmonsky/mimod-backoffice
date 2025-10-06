# Mimod Backoffice - Comprehensive Documentation

**Version:** 1.0.0
**Last Updated:** October 6, 2025
**Laravel Version:** 11.x

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Architecture & Patterns](#2-architecture--patterns)
3. [Database Schema](#3-database-schema)
4. [Module Implementation Guide](#4-module-implementation-guide)
5. [Development Guidelines](#5-development-guidelines)

---

## 1. Project Overview

### 1.1 About The Project

**Project Name:** Mimod Backoffice
**Purpose:** E-commerce backoffice system for baby products marketplace

### 1.2 Tech Stack

**Backend:**
- Laravel 11
- MySQL/PostgreSQL
- Laravel Sanctum (Token-based Authentication)
- Repository Pattern Architecture

**Frontend:**
- Blade Templates + Vite + jQuery (Current)
- Planned Migration: Inertia.js + Vue 3 (Backoffice) | Nuxt 3 (Marketplace)

**UI/UX:**
- Tailwind CSS + DaisyUI
- Iconify Icons

### 1.3 Key Features

- Role-Based Access Control (RBAC)
- Product & Catalog Management
- Order Management
- User Activity Tracking
- Dynamic Module & Permission System
- Multi-category Product Support
- Product Variants & Stock Management
- Shipping Integration (RajaOngkir)
- Payment Gateway (Midtrans)

---

## 2. Architecture & Patterns

### 2.1 Repository Pattern

The project follows the Repository Pattern for separation of concerns and maintainability.

```
Controller → Repository Interface → Repository Implementation → Database
```

**Benefits:**
- Clean separation of concerns
- Easy to test and mock
- Cacheable implementations
- Swappable data sources

### 2.2 Folder Structure

```
app/
├── Http/
│   ├── Controllers/          # Per module controllers
│   │   ├── AccessControl/    # Users, Roles, Permissions
│   │   ├── Catalog/          # Products, Categories, Brands
│   │   ├── Reports/          # Sales, Revenue, Inventory
│   │   └── Settings/         # General Settings
│   └── Middleware/
│       └── PermissionMiddleware.php
│
├── Models/                   # Eloquent Models
│   ├── User.php
│   └── UserActivity.php
│
├── Repositories/
│   ├── Contracts/            # Repository Interfaces
│   │   ├── AccessControl/
│   │   │   ├── RoleRepositoryInterface.php
│   │   │   └── ModuleRepositoryInterface.php
│   │   └── Catalog/
│   │       └── ProductRepositoryInterface.php
│   │
│   ├── AccessControl/        # Repository Implementations
│   │   ├── RoleRepository.php
│   │   └── ModuleRepository.php
│   │
│   └── Catalog/
│       └── ProductRepository.php
│
└── Providers/
    ├── AppServiceProvider.php
    └── RepositoryServiceProvider.php

resources/
├── views/                    # Blade templates
│   ├── layouts/
│   │   └── app.blade.php
│   └── pages/               # Per module views
│       ├── access-control/
│       ├── catalog/
│       └── reports/
│
└── js/                      # JavaScript modules
    ├── utils/
    │   └── ajax.js
    └── modules/
        ├── access-control/
        └── catalog/
```

### 2.3 Naming Conventions

| Component | Format | Example |
|-----------|--------|---------|
| **Controller** | `{Feature}Controller` | `RoleController.php` |
| **Repository Interface** | `{Feature}RepositoryInterface` | `RoleRepositoryInterface.php` |
| **Repository Implementation** | `{Feature}Repository` | `RoleRepository.php` |
| **Model** | `{SingularName}` | `User`, `Product` |
| **View Files** | `{action}.blade.php` | `index.blade.php`, `create.blade.php` |
| **JS Files** | `{action}.js` | `index.js`, `create.js` |
| **Route Names** | `{feature}.{action}` | `role.index`, `role.create` |
| **Permissions** | `{module}.{feature}.{action}` | `access-control.roles.view` |
| **Database Tables** | `{plural_lowercase}` | `roles`, `users`, `products` |

### 2.4 Controller Pattern

```php
<?php

namespace App\Http\Controllers\{Module};

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\{Module}\{Name}RepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class {Name}Controller extends Controller
{
    protected ${name}Repo;

    public function __construct({Name}RepositoryInterface ${name}Repository)
    {
        $this->{name}Repo = ${name}Repository;
    }

    // Index - List all
    public function index()
    {
        $data = $this->{name}Repo->getAll();
        return view('pages.{module}.{feature}.index', compact('data'));
    }

    // Store - Create new
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            DB::beginTransaction();
            $item = $this->{name}Repo->create($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Created successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            DB::beginTransaction();
            $item = $this->{name}Repo->update($id, $validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Destroy - Delete
    public function destroy($id)
    {
        try {
            $this->{name}Repo->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

### 2.5 Repository Pattern

**Interface:**
```php
<?php

namespace App\Repositories\Contracts\{Module};

interface {Name}RepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
```

**Implementation:**
```php
<?php

namespace App\Repositories\{Module};

use App\Repositories\Contracts\{Module}\{Name}RepositoryInterface;
use Illuminate\Support\Facades\DB;

class {Name}Repository implements {Name}RepositoryInterface
{
    protected $tableName = '{table_name}';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function getAll()
    {
        return $this->table()->get();
    }

    public function findById($id)
    {
        $item = $this->table()->where('id', $id)->first();
        if (!$item) {
            throw new \Exception("{Name} not found");
        }
        return $item;
    }

    public function create(array $data)
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $id = $this->table()->insertGetId($data);
        return $this->table()->where('id', $id)->first();
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = now();
        $this->table()->where('id', $id)->update($data);
        return $this->table()->where('id', $id)->first();
    }

    public function delete($id)
    {
        return $this->table()->where('id', $id)->delete();
    }
}
```

**Registration in `RepositoryServiceProvider`:**
```php
public function register(): void
{
    $this->app->bind(
        {Name}RepositoryInterface::class,
        {Name}Repository::class
    );
}
```

### 2.6 Route Pattern

```php
Route::middleware('auth.token')->group(function () {
    Route::group(['prefix' => '{module}'], function () {
        Route::group(['prefix' => '{feature}'], function () {
            Route::get('/', 'Controller@index')
                ->name('{feature}.index')
                ->middleware('permission:{module}.{feature}.view');

            Route::get('/create', 'Controller@create')
                ->name('{feature}.create')
                ->middleware('permission:{module}.{feature}.create');

            Route::post('/store', 'Controller@store')
                ->name('{feature}.store')
                ->middleware('permission:{module}.{feature}.create');

            Route::get('/{id}/edit', 'Controller@edit')
                ->name('{feature}.edit')
                ->middleware('permission:{module}.{feature}.update');

            Route::put('/{id}', 'Controller@update')
                ->name('{feature}.update')
                ->middleware('permission:{module}.{feature}.update');

            Route::delete('/{id}', 'Controller@destroy')
                ->name('{feature}.destroy')
                ->middleware('permission:{module}.{feature}.delete');
        });
    });
});
```

---

## 3. Database Schema

### 3.1 Core Tables

#### users
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(255)
email               VARCHAR(255) UNIQUE
password            VARCHAR(255)
role_id             BIGINT UNSIGNED (FK → roles)
email_verified_at   TIMESTAMP NULL
status              ENUM('active', 'inactive', 'suspended')
last_login_at       TIMESTAMP NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Relationships:**
- `belongsTo(Role)`
- `hasMany(UserActivity)`

#### roles
```sql
id              BIGINT UNSIGNED PRIMARY KEY
name            VARCHAR(100) UNIQUE
display_name    VARCHAR(100)
description     TEXT NULL
priority        INT DEFAULT 10
is_active       BOOLEAN DEFAULT true
is_system       BOOLEAN DEFAULT false
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**System Roles:**
- `super-admin` (Full access, cannot be deleted)
- `admin` (Management access)
- `staff` (Limited access)

**Relationships:**
- `hasMany(User)`
- `belongsToMany(Permission)` via `role_permissions`
- `belongsToMany(Module)` via `role_modules`

#### permissions
```sql
id              BIGINT UNSIGNED PRIMARY KEY
name            VARCHAR(255) UNIQUE
display_name    VARCHAR(255)
description     TEXT NULL
module          VARCHAR(100)
action          VARCHAR(50)
is_active       BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Permission Naming Convention:**
```
{module}.{feature}.{action}

Examples:
- dashboard.view
- access-control.users.view
- access-control.users.create
- catalog.products.update
- reports.sales.export
```

#### modules
```sql
id              BIGINT UNSIGNED PRIMARY KEY
name            VARCHAR(255) UNIQUE
display_name    VARCHAR(255)
description     TEXT NULL
icon            VARCHAR(255) NULL
parent_id       BIGINT UNSIGNED NULL (FK → modules)
group_name      VARCHAR(100) NULL
route           VARCHAR(255) NULL
component       VARCHAR(255) NULL
sort_order      INT DEFAULT 0
is_active       BOOLEAN DEFAULT true
is_visible      BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Module Groups:**
- `overview` - Dashboard
- `access_control` - Users, Roles, Permissions
- `catalog` - Products, Categories, Brands
- `reports` - Sales, Revenue, Inventory
- `settings` - General Settings

**Hierarchy Example:**
```
Reports (parent)
├── Sales Report
├── Revenue Report
├── Product Performance
└── Inventory Report
```

### 3.2 Product Tables

#### categories
```sql
id              BIGINT UNSIGNED PRIMARY KEY
name            VARCHAR(255)
slug            VARCHAR(255) UNIQUE
parent_id       BIGINT UNSIGNED NULL (FK → categories)
image           VARCHAR(500) NULL
description     TEXT NULL
sort_order      INT DEFAULT 0
is_active       BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### brands
```sql
id              BIGINT UNSIGNED PRIMARY KEY
name            VARCHAR(255)
slug            VARCHAR(255) UNIQUE
logo            VARCHAR(500) NULL
description     TEXT NULL
is_active       BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### products
```sql
id              BIGINT UNSIGNED PRIMARY KEY
name            VARCHAR(255)
slug            VARCHAR(255) UNIQUE
description     TEXT NULL
brand_id        BIGINT UNSIGNED NULL (FK → brands)
age_min         INT NULL (Minimum age in months)
age_max         INT NULL (Maximum age in months)
tags            JSON NULL
status          ENUM('active', 'inactive', 'draft')
seo_meta        JSON NULL
view_count      INT DEFAULT 0
is_featured     BOOLEAN DEFAULT false
created_by      BIGINT UNSIGNED NULL (FK → users)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Relationships:**
- `belongsTo(Brand)`
- `belongsToMany(Category)` via `product_categories`
- `hasMany(ProductVariant)`
- `hasMany(ProductImage)`

#### product_variants
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
product_id          BIGINT UNSIGNED (FK → products)
sku                 VARCHAR(100) UNIQUE
size                VARCHAR(50)
color               VARCHAR(50) NULL
weight_gram         INT
price               DECIMAL(12, 2)
compare_at_price    DECIMAL(12, 2) NULL
stock_quantity      INT DEFAULT 0
reserved_quantity   INT DEFAULT 0
barcode             VARCHAR(100) NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Stock Calculation:**
```
available_stock = stock_quantity - reserved_quantity
```

#### product_images
```sql
id              BIGINT UNSIGNED PRIMARY KEY
product_id      BIGINT UNSIGNED (FK → products)
url             VARCHAR(500)
alt_text        VARCHAR(255) NULL
is_primary      BOOLEAN DEFAULT false
sort_order      INT DEFAULT 0
created_at      TIMESTAMP
```

### 3.3 Key Relationships

```
User (1) ──→ (M) UserActivity
User (N) ──→ (M) Role via role_permissions
Role (N) ──→ (M) Permission via role_permissions
Role (N) ──→ (M) Module via role_modules

Product (1) ──→ (M) ProductVariant
Product (1) ──→ (M) ProductImage
Product (N) ──→ (M) Category via product_categories
Product (N) ──→ (1) Brand

Module (1) ──→ (M) Module (self-referencing parent_id)
Category (1) ──→ (M) Category (self-referencing parent_id)
```

---

## 4. Module Implementation Guide

### 4.1 Product Module Example

The Product module demonstrates best practices for implementing a complete CRUD module with relationships.

#### Repository Methods

```php
// ProductRepository.php
public function getAllWithRelations()
{
    return DB::table('products as p')
        ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
        ->select([
            'p.*',
            'b.name as brand_name',
            DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_id = p.id) as min_price'),
            DB::raw('(SELECT MAX(price) FROM product_variants WHERE product_id = p.id) as max_price'),
            DB::raw('(SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id) as total_stock')
        ])
        ->get();
}

public function syncCategories($productId, array $categoryIds)
{
    // Delete existing categories
    DB::table('product_categories')
        ->where('product_id', $productId)
        ->delete();

    // Insert new categories
    if (!empty($categoryIds)) {
        $data = array_map(function($categoryId) use ($productId) {
            return [
                'product_id' => $productId,
                'category_id' => $categoryId
            ];
        }, $categoryIds);

        DB::table('product_categories')->insert($data);
    }
}
```

#### Controller Implementation

```php
// AllProductsController.php
public function destroy($id)
{
    try {
        // Check if product has variants
        $variantCount = DB::table('product_variants')
            ->where('product_id', $id)
            ->count();

        if ($variantCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with existing variants'
            ], 400);
        }

        $this->productRepo->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
```

#### Image Management

```php
// AddProductsController.php
public function uploadImages(Request $request, $id)
{
    $request->validate([
        'images' => 'required|array|max:10',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
    ]);

    try {
        $uploadedImages = [];

        // Check if product has no primary image
        $hasPrimary = DB::table('product_images')
            ->where('product_id', $id)
            ->where('is_primary', true)
            ->exists();

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');

            $imageData = [
                'product_id' => $id,
                'url' => '/storage/' . $path,
                'is_primary' => !$hasPrimary && $index === 0,
                'sort_order' => $index,
                'created_at' => now()
            ];

            $imageId = DB::table('product_images')->insertGetId($imageData);
            $uploadedImages[] = DB::table('product_images')
                ->where('id', $imageId)
                ->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'data' => $uploadedImages
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
```

### 4.2 Module Creation Checklist

When creating a new module, follow this checklist:

**Backend:**
- [ ] Create Controller in `app/Http/Controllers/{Module}/`
- [ ] Create Repository Interface in `app/Repositories/Contracts/{Module}/`
- [ ] Create Repository Implementation in `app/Repositories/{Module}/`
- [ ] Register Repository in `RepositoryServiceProvider`
- [ ] Create Migration in `database/migrations/`
- [ ] Create Seeder in `database/seeders/`
- [ ] Add Routes in `routes/web.php`
- [ ] Create Permissions in seeder

**Frontend:**
- [ ] Create View folder in `resources/views/pages/{module}/`
- [ ] Create `index.blade.php`, `create.blade.php`, `edit.blade.php`
- [ ] Create JS folder in `resources/js/modules/{module}/`
- [ ] Create `index.js`, `create.js`, `edit.js`
- [ ] Add to Vite config if needed

**Database:**
- [ ] Run migration: `php artisan migrate`
- [ ] Run seeder: `php artisan db:seed --class={Name}Seeder`

---

## 5. Development Guidelines

### 5.1 Authentication & Authorization

#### Authentication Flow (Sanctum)

**Login:**
```
POST /api/login
Body: { email, password }

Response:
{
  "success": true,
  "token": "1|abcdef123456...",
  "user": { ... }
}
```

**Authenticated Requests:**
```
Headers:
Authorization: Bearer {token}
Accept: application/json
```

#### Permission System

**Middleware Usage:**
```php
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:access-control.users.view');
```

**Blade Helper:**
```php
@if(hasPermission('access-control.users.create'))
    <button>Create User</button>
@endif
```

**PHP Helper:**
```php
if (hasPermission('access-control.users.delete')) {
    // Allow delete
}
```

### 5.2 Response Format

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error detail"]
  }
}
```

### 5.3 Validation Rules

```php
// Common validations
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users,email',
'password' => 'required|min:8|confirmed',
'status' => 'required|in:active,inactive,suspended',
'is_active' => 'boolean',
'parent_id' => 'nullable|exists:categories,id',
'image' => 'nullable|image|max:2048',
'price' => 'required|numeric|min:0',
'stock_quantity' => 'required|integer|min:0',
```

### 5.4 Activity Logging

```php
/**
 * Log user activity
 *
 * @param string $action (create, update, delete, view, export)
 * @param string $description
 * @param string $module
 * @param int|null $recordId
 */
logActivity('create', 'Created new product: Baby Formula', 'Product', $productId);
logActivity('update', 'Updated role permissions', 'Role', $roleId);
logActivity('delete', 'Deleted user: john@example.com', 'User', $userId);
logActivity('export', 'Exported sales report', 'Report');
```

### 5.5 Environment Setup

**Installation Steps:**
```bash
# Clone repository
git clone <repository-url>
cd mimod-backoffice

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage link
php artisan storage:link

# Run development server
php artisan serve
npm run dev
```

**Default Admin Account:**
```
Email: admin@mimod.com
Password: password
Role: Super Admin
```

### 5.6 Common Helper Functions

```php
// Permission check
hasPermission($permission)

// Get current user
currentUser()

// Log activity
logActivity($action, $description, $module, $recordId)
```

### 5.7 Frontend Patterns

**Ajax Helper Usage:**
```javascript
import Ajax from '../../../utils/ajax.js';

// POST request
await Ajax.post(url, data, {
    loadingMessage: 'Processing...',
    successMessage: 'Success!',
    onSuccess: (response) => {
        window.location.reload();
    }
});

// DELETE request
await Ajax.delete(url, {
    loadingMessage: 'Deleting...',
    successMessage: 'Deleted successfully',
    onSuccess: () => {
        window.location.reload();
    }
});
```

**Search Implementation:**
```javascript
$('#searchInput').on('keyup', function() {
    const value = $(this).val().toLowerCase();
    $('#dataTable tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});
```

---

## Appendix A: Quick Reference

### API Endpoints

| Module | Method | Endpoint | Permission |
|--------|--------|----------|------------|
| **Users** | GET | `/api/users` | `access-control.users.view` |
| | POST | `/api/users` | `access-control.users.create` |
| | PUT | `/api/users/{id}` | `access-control.users.update` |
| | DELETE | `/api/users/{id}` | `access-control.users.delete` |
| **Products** | GET | `/api/products` | `catalog.products.all-products.view` |
| | POST | `/api/products` | `catalog.products.add-products.view` |
| | PUT | `/api/products/{id}` | `catalog.products.add-products.view` |
| | DELETE | `/api/products/{id}` | `catalog.products.all-products.view` |

### Common Commands

```bash
# Database
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Storage
php artisan storage:link

# Development
php artisan serve
npm run dev
npm run build
```

---

**End of Documentation**

For questions or support, contact the development team.
