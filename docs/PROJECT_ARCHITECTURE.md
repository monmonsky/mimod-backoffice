# Mimod Backoffice - Complete Project Architecture

**Version:** 1.0.0
**Last Updated:** October 6, 2025
**Laravel Version:** 12.x
**Database:** PostgreSQL

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Current Tech Stack](#2-current-tech-stack)
3. [Database Architecture](#3-database-architecture)
4. [Backend Architecture](#4-backend-architecture)
5. [Frontend Architecture](#5-frontend-architecture)
6. [Authentication & Authorization](#6-authentication--authorization)
7. [Module Structure](#7-module-structure)
8. [API Design](#8-api-design)
9. [File Structure](#9-file-structure)
10. [Development Workflow](#10-development-workflow)

---

## 1. Project Overview

### 1.1 About

**Mimod Backoffice** is an e-commerce management system for a kids clothing marketplace.

**Key Features:**
- Product & Catalog Management (Products, Categories, Brands, Variants)
- Order Management
- Role-Based Access Control (RBAC)
- User Activity Tracking
- Reports (Sales, Revenue, Product Performance, Inventory)
- Settings (Store, Email, SEO, Payment, Shipping)
- Shipping Integration (RajaOngkir)
- Payment Gateway (Midtrans)

### 1.2 Business Domain

**Target Users:**
- Super Admin (Full access)
- Admin (Management access)
- Staff (Limited access)
- Customer Service (View + limited actions)

**Core Workflows:**
1. Product Management: Create → Variants → Images → Categories → Publish
2. Order Processing: Receive → Process → Ship → Complete
3. Inventory: Track stock → Low stock alerts → Restock
4. Reporting: Generate reports → Export → Analysis

---

## 2. Current Tech Stack

### 2.1 Backend

| Component | Technology | Version |
|-----------|-----------|---------|
| Framework | Laravel | 12.x |
| Database | PostgreSQL | 15+ |
| Authentication | Laravel Sanctum | Token-based |
| Architecture | Repository Pattern | - |
| API | RESTful JSON API | - |

### 2.2 Frontend (Current - Blade)

| Component | Technology |
|-----------|-----------|
| Template Engine | Blade Templates |
| CSS Framework | Tailwind CSS + DaisyUI |
| JavaScript | jQuery + Vanilla JS |
| Build Tool | Vite |
| Icons | Iconify |

### 2.3 Frontend (Target - Inertia + Vue)

| Component | Technology | Version |
|-----------|-----------|---------|
| Bridge | Inertia.js | 1.x |
| Framework | Vue 3 | 3.x |
| Language | TypeScript | 5.x |
| State Management | Pinia | 2.x |
| UI Framework | Tailwind CSS + DaisyUI | - |
| Build Tool | Vite | 5.x |
| Icons | Iconify | - |
| Composition API | `<script setup>` | - |

---

## 3. Database Architecture

### 3.1 Core Tables

#### **users**
Primary user table with role-based access.

```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    role_id BIGINT REFERENCES roles(id),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP,
    phone_verified_at TIMESTAMP,
    last_login_at TIMESTAMP,
    last_login_ip INET,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'deleted')),
    two_factor_enabled BOOLEAN DEFAULT false,
    two_factor_secret VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
```

**Relationships:**
- `belongsTo(Role)` via `role_id`
- `hasMany(UserActivity)`
- `hasMany(PersonalAccessToken)` (Sanctum)

#### **roles**
System roles with priority-based hierarchy.

```sql
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    priority INTEGER DEFAULT 10,
    is_active BOOLEAN DEFAULT true,
    is_system BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

**System Roles:**
- `super_admin` (Priority: 100) - Cannot be deleted, full access
- `admin` (Priority: 90) - Management access
- `staff` (Priority: 50) - Limited access
- `customer` (Priority: 10) - Customer service

**Relationships:**
- `hasMany(User)`
- `belongsToMany(Permission)` via `role_permissions`

#### **permissions**
Granular permissions for RBAC.

```sql
CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    module VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_permissions_module ON permissions(module);
CREATE INDEX idx_permissions_module_action ON permissions(module, action);
```

**Permission Naming Convention:**
```
{module}.{feature}.{action}

Examples:
- dashboard.view
- access-control.users.view
- access-control.users.create
- access-control.users.update
- access-control.users.delete
- catalog.products.all-products.view
- catalog.products.add-products.create
- reports.sales.export
```

#### **role_permissions**
Many-to-many relationship between roles and permissions.

```sql
CREATE TABLE role_permissions (
    id BIGSERIAL PRIMARY KEY,
    role_id BIGINT REFERENCES roles(id) ON DELETE CASCADE,
    permission_id BIGINT REFERENCES permissions(id) ON DELETE CASCADE,
    granted_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    granted_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(role_id, permission_id)
);

CREATE INDEX idx_role_permissions_role ON role_permissions(role_id);
CREATE INDEX idx_role_permissions_permission ON role_permissions(permission_id);
```

#### **modules**
Dynamic sidebar navigation modules.

```sql
CREATE TABLE modules (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    parent_id BIGINT REFERENCES modules(id) ON DELETE CASCADE,
    group_name VARCHAR(50),
    route VARCHAR(255),
    permission_name VARCHAR(255),
    component VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    is_visible BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_modules_parent ON modules(parent_id);
CREATE INDEX idx_modules_active ON modules(is_active);
```

**Module Groups:**
- `overview` - Dashboard
- `access_control` - Users, Roles, Permissions, Modules, User Activities
- `catalog` - Products, Categories, Brands, Variants
- `reports` - Sales, Revenue, Product Performance, Inventory
- `settings` - Store, Email, SEO, System, Payments, Shipping

**Hierarchy Example:**
```
Catalog (parent, group: catalog)
├── Products (parent)
│   ├── All Products (leaf, route: catalog.products.all-products)
│   ├── Add Products (leaf, route: catalog.products.add-products)
│   ├── Categories (leaf, route: catalog.products.categories)
│   ├── Brands (leaf, route: catalog.products.brands)
│   └── Variants (leaf, route: catalog.products.variants)
```

### 3.2 Product Tables

#### **categories**
Hierarchical product categories.

```sql
CREATE TABLE categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    parent_id BIGINT REFERENCES categories(id) ON DELETE CASCADE,
    image VARCHAR(500),
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_categories_parent ON categories(parent_id);
CREATE INDEX idx_categories_active ON categories(is_active);
CREATE INDEX idx_categories_slug ON categories(slug);
```

**Example Categories:**
```
Pakaian Anak Laki-laki (parent)
├── Kaos Anak Laki-laki
├── Kemeja Anak Laki-laki
├── Celana Pendek Anak Laki-laki
└── Celana Panjang Anak Laki-laki

Pakaian Anak Perempuan (parent)
├── Kaos Anak Perempuan
├── Blouse & Kemeja Anak Perempuan
├── Dress & Rok
└── Celana Panjang & Legging
```

#### **brands**
Product brands.

```sql
CREATE TABLE brands (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    logo VARCHAR(500),
    description TEXT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_brands_active ON brands(is_active);
CREATE INDEX idx_brands_slug ON brands(slug);
```

**Example Brands:**
- Carter's, OshKosh B'gosh, Mothercare (International)
- Little Palmerhaus, Kids Icon, Gingersnaps (Local)
- Nike Kids, Adidas Kids (Sports)
- Disney Kids, Marvel Kids (Licensed)

#### **products**
Main product table.

```sql
CREATE TABLE products (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    brand_id BIGINT REFERENCES brands(id) ON DELETE SET NULL,
    age_min INTEGER, -- Minimum age in months
    age_max INTEGER, -- Maximum age in months
    tags JSONB,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'draft')),
    seo_meta JSONB,
    view_count INTEGER DEFAULT 0,
    is_featured BOOLEAN DEFAULT false,
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_products_brand ON products(brand_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_slug ON products(slug);
```

**Relationships:**
- `belongsTo(Brand)` via `brand_id`
- `belongsTo(User)` via `created_by`
- `belongsToMany(Category)` via `product_categories`
- `hasMany(ProductVariant)`
- `hasMany(ProductImage)`

#### **product_categories**
Many-to-many: Products can belong to multiple categories.

```sql
CREATE TABLE product_categories (
    product_id BIGINT REFERENCES products(id) ON DELETE CASCADE,
    category_id BIGINT REFERENCES categories(id) ON DELETE CASCADE,
    PRIMARY KEY (product_id, category_id)
);

CREATE INDEX idx_product_categories_category ON product_categories(category_id);
```

#### **product_variants**
Product size/color variations with stock tracking.

```sql
CREATE TABLE product_variants (
    id BIGSERIAL PRIMARY KEY,
    product_id BIGINT REFERENCES products(id) ON DELETE CASCADE,
    sku VARCHAR(100) UNIQUE NOT NULL,
    size VARCHAR(50) NOT NULL,
    color VARCHAR(50),
    weight_gram INTEGER NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    compare_at_price DECIMAL(12, 2),
    stock_quantity INTEGER DEFAULT 0,
    reserved_quantity INTEGER DEFAULT 0,
    barcode VARCHAR(100),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_variants_product ON product_variants(product_id);
CREATE UNIQUE INDEX idx_variants_sku ON product_variants(sku);
```

**Stock Calculation:**
```sql
-- Available stock for purchase
SELECT stock_quantity - reserved_quantity AS available_stock
FROM product_variants
WHERE id = ?;
```

**Example Variants:**
```
Product: "Carter's Baby Boy Cotton Bodysuit"
Variants:
- SKU: CTR-BB-0-3M-BL  | Size: 0-3M | Color: Blue   | Price: 125,000 | Stock: 50
- SKU: CTR-BB-3-6M-BL  | Size: 3-6M | Color: Blue   | Price: 135,000 | Stock: 45
- SKU: CTR-BB-0-3M-GR  | Size: 0-3M | Color: Green  | Price: 125,000 | Stock: 30
```

#### **product_images**
Product images with primary image support and sorting.

```sql
CREATE TABLE product_images (
    id BIGSERIAL PRIMARY KEY,
    product_id BIGINT REFERENCES products(id) ON DELETE CASCADE,
    url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_images_product ON product_images(product_id);
CREATE INDEX idx_images_primary ON product_images(product_id, is_primary);
```

### 3.3 Activity Tracking

#### **user_activities**
Track all user actions for audit trail.

```sql
CREATE TABLE user_activities (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(50) NOT NULL,
    subject_type VARCHAR(100),
    subject_id BIGINT,
    description TEXT,
    properties JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_activities_user ON user_activities(user_id);
CREATE INDEX idx_activities_action ON user_activities(action);
CREATE INDEX idx_activities_created ON user_activities(created_at);
```

**Common Actions:**
- `login`, `logout`
- `create`, `update`, `delete`, `view`
- `export`, `import`
- `error`

**Example Activity Logs:**
```json
{
  "user_id": 1,
  "action": "create",
  "subject_type": "Product",
  "subject_id": 123,
  "description": "Created new product: Baby Formula",
  "properties": {
    "product_name": "Baby Formula",
    "brand_id": 5,
    "status": "active"
  },
  "ip_address": "192.168.1.1",
  "user_agent": "Mozilla/5.0..."
}
```

### 3.4 Settings Tables

#### **settings**
Key-value store for system settings.

```sql
CREATE TABLE settings (
    id BIGSERIAL PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(50) DEFAULT 'string',
    group_name VARCHAR(100),
    is_public BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_settings_key ON settings(key);
CREATE INDEX idx_settings_group ON settings(group_name);
```

**Setting Groups:**
- `store` - Store info, logo, contact
- `email` - SMTP settings
- `seo` - Meta tags, analytics
- `system` - Timezone, currency, language
- `payment` - Payment gateway configs
- `shipping` - Shipping settings

### 3.5 ER Diagram Summary

```
users (1) ──→ (M) user_activities
users (N) ──→ (1) roles
roles (N) ──→ (M) permissions [via role_permissions]
users (1) ──→ (M) products (created_by)

products (1) ──→ (M) product_variants
products (1) ──→ (M) product_images
products (N) ──→ (M) categories [via product_categories]
products (N) ──→ (1) brands

categories (1) ──→ (M) categories (parent_id)
modules (1) ──→ (M) modules (parent_id)
```

---

## 4. Backend Architecture

### 4.1 Repository Pattern

**Principle:** Separate data access logic from business logic.

```
Controller → Repository Interface → Repository Implementation → Database
```

**Benefits:**
- Clean separation of concerns
- Easy to test (mockable)
- Swappable data sources
- Cacheable queries
- Consistent data access patterns

### 4.2 Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── AccessControl/
│   │   │   ├── UserController.php
│   │   │   ├── RoleController.php
│   │   │   ├── PermissionController.php
│   │   │   ├── ModuleController.php
│   │   │   └── UserActivityController.php
│   │   ├── Catalog/
│   │   │   ├── AllProductsController.php
│   │   │   ├── AddProductsController.php
│   │   │   ├── CategoriesController.php
│   │   │   ├── BrandsController.php
│   │   │   └── VariantsController.php
│   │   ├── Reports/
│   │   │   ├── SalesReportController.php
│   │   │   ├── RevenueReportController.php
│   │   │   ├── ProductPerformanceController.php
│   │   │   └── InventoryReportController.php
│   │   ├── Settings/
│   │   │   ├── GeneralController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ShippingController.php
│   │   │   └── ApiTokenController.php
│   │   └── Api/
│   │       ├── Auth/
│   │       ├── Catalog/
│   │       └── Settings/
│   ├── Middleware/
│   │   ├── AuthenticateToken.php
│   │   └── PermissionMiddleware.php
│   ├── Responses/
│   │   └── GeneralResponse/
│   │       ├── Response.php
│   │       └── ResultBuilder.php
│   └── View/
│       └── Composers/
│           └── SidebarComposer.php
│
├── Repositories/
│   ├── Contracts/
│   │   ├── UserRepositoryInterface.php
│   │   ├── RoleRepositoryInterface.php
│   │   ├── PermissionRepositoryInterface.php
│   │   ├── ModuleRepositoryInterface.php
│   │   ├── AccessControl/
│   │   │   └── PermissionGroupRepositoryInterface.php
│   │   ├── Catalog/
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   ├── CategoryRepositoryInterface.php
│   │   │   └── BrandRepositoryInterface.php
│   │   └── Settings/
│   │       └── SettingRepositoryInterface.php
│   │
│   ├── UserRepository.php
│   ├── RoleRepository.php
│   ├── PermissionRepository.php
│   ├── ModuleRepository.php
│   ├── AccessControl/
│   │   └── PermissionGroupRepository.php
│   ├── Catalog/
│   │   ├── ProductRepository.php
│   │   ├── CategoryRepository.php
│   │   └── BrandRepository.php
│   └── Settings/
│       └── SettingRepository.php
│
├── Helpers/
│   └── helpers.php
│
└── Providers/
    ├── AppServiceProvider.php
    └── RepositoryServiceProvider.php
```

### 4.3 Naming Conventions

| Component | Pattern | Example |
|-----------|---------|---------|
| Controller | `{Feature}Controller` | `UserController.php` |
| Repository Interface | `{Feature}RepositoryInterface` | `UserRepositoryInterface.php` |
| Repository | `{Feature}Repository` | `UserRepository.php` |
| Middleware | `{Purpose}Middleware` | `PermissionMiddleware.php` |
| Model | `{SingularPascalCase}` | `User.php`, `ProductVariant.php` |
| Migration | `{timestamp}_create_{table}_table` | `2025_01_01_create_users_table.php` |
| Seeder | `{Feature}Seeder` | `UserSeeder.php` |

### 4.4 Repository Pattern Implementation

**Interface Example:**
```php
<?php

namespace App\Repositories\Contracts\Catalog;

interface ProductRepositoryInterface
{
    public function getAll();
    public function query();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getAllWithRelations();
    public function syncCategories($productId, array $categoryIds);
}
```

**Implementation Example:**
```php
<?php

namespace App\Repositories\Catalog;

use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    protected $tableName = 'products';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function query()
    {
        return $this->table();
    }

    public function getAll()
    {
        return $this->table()->orderBy('id', 'desc')->get();
    }

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
            ->orderBy('p.id', 'desc')
            ->get();
    }

    public function findById($id)
    {
        return $this->table()->where('id', $id)->first();
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

    public function syncCategories($productId, array $categoryIds)
    {
        // Delete existing
        DB::table('product_categories')
            ->where('product_id', $productId)
            ->delete();

        // Insert new
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
}
```

**Service Provider Registration:**
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // User
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        // Product
        $this->app->bind(
            \App\Repositories\Contracts\Catalog\ProductRepositoryInterface::class,
            \App\Repositories\Catalog\ProductRepository::class
        );

        // ... other bindings
    }
}
```

### 4.5 Controller Pattern

```php
<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Catalog\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepo = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepo->getAllWithRelations();
        return view('pages.catalog.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = currentUser('id');
            $product = $this->productRepo->create($validated);

            // Sync categories if provided
            if ($request->has('category_ids')) {
                $this->productRepo->syncCategories(
                    $product->id,
                    $request->category_ids
                );
            }

            DB::commit();

            logActivity('create', "Created product: {$product->name}", 'Product', $product->id);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
        ]);

        try {
            DB::beginTransaction();

            $product = $this->productRepo->update($id, $validated);

            if ($request->has('category_ids')) {
                $this->productRepo->syncCategories($id, $request->category_ids);
            }

            DB::commit();

            logActivity('update', "Updated product: {$product->name}", 'Product', $product->id);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = $this->productRepo->findById($id);
            $this->productRepo->delete($id);

            logActivity('delete', "Deleted product: {$product->name}", 'Product', $id);

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
}
```

### 4.6 Helper Functions

**Location:** `app/Helpers/helpers.php`

```php
<?php

// Current user helpers
function currentUser(?string $key = null, $default = null)
{
    $user = request()->get('user');
    if (!$user) return $default;
    return $key ? ($user[$key] ?? $default) : $user;
}

function userId($default = null)
{
    return currentUser('id', $default);
}

function userName(string $default = 'Guest'): string
{
    return currentUser('name', $default);
}

function userEmail(string $default = ''): string
{
    return currentUser('email', $default);
}

function userRole(string $default = 'user'): string
{
    return currentUser('role', $default);
}

// Permission helpers
function hasPermission(string $permission): bool
{
    $user = currentUser();
    if (!$user) return false;

    // Super admin has all permissions
    if (isset($user['role']) && $user['role'] === 'super_admin') {
        return true;
    }

    $permissions = $user['permissions'] ?? [];
    if (is_array($permissions)) {
        foreach ($permissions as $perm) {
            if (is_string($perm) && $perm === $permission) {
                return true;
            }
            if (is_array($perm) && isset($perm['name']) && $perm['name'] === $permission) {
                return true;
            }
        }
    }

    return false;
}

function hasAnyPermission(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if (hasPermission($permission)) {
            return true;
        }
    }
    return false;
}

// Activity logging
function logActivity(
    string $action,
    ?string $description = null,
    ?string $subjectType = null,
    ?int $subjectId = null,
    ?array $properties = null
): bool {
    try {
        $userId = userId();
        if (!$userId) return false;

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'properties' => $properties ? json_encode($properties) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        \Illuminate\Support\Facades\DB::table('user_activities')->insert($data);
        return true;
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to log activity: ' . $e->getMessage());
        return false;
    }
}
```

**Register in `composer.json`:**
```json
{
    "autoload": {
        "files": [
            "app/Helpers/helpers.php"
        ]
    }
}
```

---

## 5. Frontend Architecture

### 5.1 Current Stack (Blade)

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php (Main layout)
│   │   └── guest.blade.php (Login/Guest layout)
│   ├── partials/
│   │   ├── sidebar-dynamic.blade.php
│   │   ├── navbar.blade.php
│   │   └── footer.blade.php
│   └── pages/
│       ├── dashboard.blade.php
│       ├── access-control/
│       │   ├── users/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   └── roles/
│       └── catalog/
│           └── products/
│
└── js/
    ├── app.js
    ├── utils/
    │   └── ajax.js
    └── modules/
        ├── access-control/
        │   ├── users/
        │   │   ├── index.js
        │   │   └── create.js
        │   └── roles/
        └── catalog/
```

### 5.2 Target Stack (Inertia + Vue)

```
resources/
├── js/
│   ├── app.ts (Inertia app entry)
│   ├── ssr.ts (SSR entry - optional)
│   │
│   ├── Pages/
│   │   ├── Dashboard.vue
│   │   ├── Auth/
│   │   │   └── Login.vue
│   │   ├── AccessControl/
│   │   │   ├── Users/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   └── Edit.vue
│   │   │   └── Roles/
│   │   │       ├── Index.vue
│   │   │       └── Create.vue
│   │   └── Catalog/
│   │       ├── Products/
│   │       │   ├── Index.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Categories/
│   │       └── Brands/
│   │
│   ├── Components/
│   │   ├── Layout/
│   │   │   ├── AppLayout.vue
│   │   │   ├── Sidebar.vue
│   │   │   ├── Navbar.vue
│   │   │   └── Footer.vue
│   │   ├── UI/
│   │   │   ├── Button.vue
│   │   │   ├── Input.vue
│   │   │   ├── Select.vue
│   │   │   ├── Modal.vue
│   │   │   ├── Table.vue
│   │   │   └── Card.vue
│   │   └── Shared/
│   │       ├── Pagination.vue
│   │       ├── SearchBar.vue
│   │       └── Breadcrumb.vue
│   │
│   ├── Composables/
│   │   ├── useAuth.ts
│   │   ├── usePermission.ts
│   │   ├── useForm.ts
│   │   └── useTable.ts
│   │
│   ├── Stores/
│   │   ├── auth.ts
│   │   ├── sidebar.ts
│   │   └── notification.ts
│   │
│   ├── Types/
│   │   ├── models.ts
│   │   ├── api.ts
│   │   └── inertia.ts
│   │
│   └── Utils/
│       ├── helpers.ts
│       ├── formatters.ts
│       └── validators.ts
│
└── views/
    └── app.blade.php (Inertia root template)
```

---

## 6. Authentication & Authorization

### 6.1 Authentication Flow (Sanctum)

**Login Request:**
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@mimod.com",
  "password": "password"
}
```

**Login Response:**
```json
{
  "success": true,
  "token": "1|abc123def456...",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@mimod.com",
    "role": "super_admin",
    "role_display": "Super Administrator",
    "permissions": [
      "dashboard.view",
      "access-control.users.view",
      "access-control.users.create",
      "..."
    ]
  }
}
```

**Authenticated Request:**
```http
GET /api/users
Authorization: Bearer 1|abc123def456...
Accept: application/json
```

### 6.2 Permission Middleware

**Route Protection:**
```php
Route::middleware(['auth.token', 'permission:access-control.users.view'])
    ->get('/users', [UserController::class, 'index']);
```

**Controller Check:**
```php
public function index()
{
    if (!hasPermission('access-control.users.view')) {
        abort(403, 'Unauthorized');
    }

    // ...
}
```

**Blade Template:**
```blade
@if(hasPermission('access-control.users.create'))
    <button>Create User</button>
@endif
```

**Vue Component (Inertia):**
```vue
<script setup lang="ts">
import { usePermission } from '@/Composables/usePermission'

const { can } = usePermission()
</script>

<template>
  <button v-if="can('access-control.users.create')">
    Create User
  </button>
</template>
```

### 6.3 User Data Flow

```
1. User logs in → Token generated
2. Token stored in cookie (web) or localStorage (mobile)
3. Every request includes token
4. AuthenticateToken middleware:
   - Validates token
   - Loads user + role + permissions
   - Attaches to request
5. PermissionMiddleware:
   - Checks required permission
   - Allow/Deny access
6. Controller:
   - Access user via currentUser()
   - Log activity
```

---

## 7. Module Structure

### 7.1 Complete Module List

#### **Access Control**
- Users Management
- Roles Management
- Permissions Management
- Modules Management
- User Activities

#### **Catalog**
- Products Management
  - All Products (list, edit, delete, toggle status/featured)
  - Add Products (create, variants, images)
  - Categories (tree structure, drag-drop sort)
  - Brands (list, create, edit, delete)
  - Variants (view all variants across products)

#### **Reports**
- Sales Report
- Revenue Report
- Product Performance
- Inventory Report

#### **Settings**
- General Settings
  - Store Info
  - Email Settings
  - SEO Meta
  - System Config
  - API Tokens
- Payment Settings
  - Payment Methods
  - Midtrans Config
  - Tax Settings
- Shipping Settings
  - Shipping Methods
  - RajaOngkir Config
  - Origin Address

### 7.2 Permission Matrix

| Module | Feature | View | Create | Update | Delete | Export | Special |
|--------|---------|------|--------|--------|--------|--------|---------|
| Dashboard | - | ✓ | - | - | - | - | - |
| Users | users | ✓ | ✓ | ✓ | ✓ | ✓ | toggle-active |
| Roles | roles | ✓ | ✓ | ✓ | ✓ | - | toggle-active |
| Permissions | permissions | ✓ | ✓ | ✓ | ✓ | - | - |
| Modules | modules | ✓ | ✓ | ✓ | ✓ | - | toggle-active, update-order |
| User Activities | user-activities | ✓ | - | - | - | ✓ | clear |
| Products | all-products | ✓ | ✓ | ✓ | ✓ | - | toggle-status, toggle-featured |
| Categories | categories | ✓ | ✓ | ✓ | ✓ | - | update-order |
| Brands | brands | ✓ | ✓ | ✓ | ✓ | - | toggle-active |
| Variants | variants | ✓ | ✓ | ✓ | ✓ | - | - |
| Sales Report | sales | ✓ | - | - | - | ✓ | - |
| Revenue Report | revenue | ✓ | - | - | - | ✓ | - |
| Product Performance | product-performance | ✓ | - | - | - | ✓ | - |
| Inventory Report | inventory | ✓ | - | - | - | ✓ | - |
| Settings | generals | ✓ | - | ✓ | - | - | test-email |
| Payment | payments | ✓ | - | ✓ | - | - | test-midtrans |
| Shipping | shippings | ✓ | - | ✓ | - | - | test-rajaongkir |

**Total Permissions:** 68

---

## 8. API Design

### 8.1 Response Pattern (ResultBuilder)

**Success Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Products retrieved successfully",
  "data": {
    "products": [...],
    "total": 50
  }
}
```

**Error Response:**
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to create product: Validation failed",
  "data": {
    "errors": {
      "name": ["The name field is required."]
    }
  }
}
```

**Implementation:**
```php
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Http\Responses\GeneralResponse\Response;

class ProductApiController extends Controller
{
    protected $productRepo;
    protected $response;

    public function __construct(ProductRepository $productRepo, Response $response)
    {
        $this->productRepo = $productRepo;
        $this->response = $response;
    }

    public function index(Request $request)
    {
        try {
            $products = $this->productRepo->getAllWithRelations();

            $result = (new ResultBuilder())
                ->setStatus(true)
                ->setStatusCode('200')
                ->setMessage('Products retrieved successfully')
                ->setData($products);

            return response()->json($this->response->generateResponse($result), 200);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage('Failed to retrieve products: ' . $e->getMessage())
                ->setData([]);

            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
```

### 8.2 API Endpoints

**Base URL:** `https://api.mimod.com/api/v1`

#### Authentication
```
POST   /login                    - Login
POST   /logout                   - Logout
GET    /me                       - Get current user
```

#### Products
```
GET    /products                 - List products (with filters)
GET    /products/{id}            - Get product detail
POST   /products                 - Create product
PUT    /products/{id}            - Update product
DELETE /products/{id}            - Delete product
POST   /products/{id}/toggle-status    - Toggle active/inactive
POST   /products/{id}/toggle-featured  - Toggle featured
```

#### Categories
```
GET    /categories               - List categories
GET    /categories/tree          - Get category tree
POST   /categories               - Create category
PUT    /categories/{id}          - Update category
DELETE /categories/{id}          - Delete category
POST   /categories/update-order  - Update sort order
```

#### Brands
```
GET    /brands                   - List brands
POST   /brands                   - Create brand
PUT    /brands/{id}              - Update brand
DELETE /brands/{id}              - Delete brand
```

---

## 9. File Structure

### 9.1 Backend Files

```
mimod-backoffice/
├── app/
│   ├── Http/
│   │   ├── Controllers/          (130+ files)
│   │   ├── Middleware/           (2 files)
│   │   ├── Responses/            (2 files)
│   │   └── View/Composers/       (1 file)
│   ├── Repositories/
│   │   ├── Contracts/            (15 interfaces)
│   │   └── Implementations/      (15 classes)
│   ├── Helpers/
│   │   └── helpers.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── RepositoryServiceProvider.php
│
├── database/
│   ├── migrations/               (9 migration files)
│   └── seeders/                  (15 seeder files)
│
├── routes/
│   ├── web.php                   (Web routes)
│   ├── api.php                   (API routes)
│   └── console.php
│
└── config/
    ├── app.php
    ├── database.php
    ├── sanctum.php
    └── cors.php
```

### 9.2 Frontend Files (Current - Blade)

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── guest.blade.php
│   ├── partials/
│   │   ├── sidebar-dynamic.blade.php
│   │   ├── navbar.blade.php
│   │   └── footer.blade.php
│   └── pages/
│       ├── dashboard.blade.php
│       ├── access-control/       (20+ blade files)
│       ├── catalog/              (15+ blade files)
│       ├── reports/              (4 blade files)
│       └── settings/             (12+ blade files)
│
├── js/
│   ├── app.js
│   ├── utils/
│   │   └── ajax.js
│   └── modules/
│       ├── access-control/       (10+ JS files)
│       ├── catalog/              (8+ JS files)
│       └── settings/             (6+ JS files)
│
└── css/
    └── app.css
```

### 9.3 Key Configuration Files

**composer.json**
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "laravel/sanctum": "^4.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        },
        "files": [
            "app/Helpers/helpers.php"
        ]
    }
}
```

**package.json (Current)**
```json
{
    "devDependencies": {
        "vite": "^5.0",
        "laravel-vite-plugin": "^1.0",
        "tailwindcss": "^3.4",
        "daisyui": "^4.0"
    },
    "dependencies": {
        "jquery": "^3.7",
        "iconify-icon": "^2.0"
    }
}
```

**vite.config.js (Current)**
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

---

## 10. Development Workflow

### 10.1 Local Development Setup

```bash
# 1. Clone repository
git clone git@github.com:monmonsky/mimod-backoffice.git
cd mimod-backoffice

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
createdb mimod_backoffice  # PostgreSQL
php artisan migrate
php artisan db:seed

# 5. Storage link
php artisan storage:link

# 6. Run development servers
php artisan serve        # Backend: http://localhost:8000
npm run dev             # Frontend assets
```

### 10.2 Default Accounts

```
Super Admin:
  Email: superadmin@mimod.com
  Password: password

Admin:
  Email: admin@mimod.com
  Password: password

Staff:
  Email: staff@mimod.com
  Password: password
```

### 10.3 Common Commands

```bash
# Database
php artisan migrate:fresh --seed    # Reset & seed database
php artisan migrate:rollback        # Rollback last migration
php artisan db:seed --class=UserSeeder

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Code
php artisan make:controller AccessControl/UserController
php artisan make:migration create_products_table
php artisan make:seeder ProductSeeder

# Development
php artisan tinker                  # Interactive console
php artisan route:list              # List all routes
php artisan optimize:clear          # Clear all caches
```

### 10.4 Git Workflow

```bash
# Current branch
git status
# Shows: dev branch

# Create feature branch
git checkout -b feature/product-management

# Work on feature...

# Commit
git add .
git commit -m "feat: add product variant management"

# Push
git push origin feature/product-management

# Create PR to dev branch
# After review, merge to dev
# After testing, merge dev to master
```

**Branch Strategy:**
- `master` - Production (stable)
- `dev` - Development (current work)
- `feature/*` - New features
- `bugfix/*` - Bug fixes
- `hotfix/*` - Urgent production fixes

---

## Summary

This architecture document provides a complete blueprint for migrating to Inertia + Vue while maintaining the existing backend structure. The repository pattern, permission system, and database schema remain unchanged—only the frontend layer will be modernized.

**Key Strengths:**
✅ Clean separation of concerns (Repository Pattern)
✅ Granular RBAC with 68 permissions
✅ Comprehensive activity logging
✅ Scalable product management with variants
✅ Well-organized module structure
✅ Ready for Inertia migration

**Next Steps:**
1. Review this architecture
2. Setup Inertia + Vue + TypeScript
3. Create base components (Layout, UI)
4. Migrate first module (Dashboard)
5. Migrate remaining modules incrementally

---

**End of Architecture Document**
