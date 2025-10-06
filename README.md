# Mimod Backoffice

> E-commerce backoffice system for baby & kids clothing marketplace built with Laravel 12

## 📋 Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Features](#features)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Development](#development)
- [API Documentation](#api-documentation)
- [Architecture Pattern](#architecture-pattern)
- [Database Schema](#database-schema)
- [Migration Guide](#migration-guide)
- [Contributing](#contributing)

## 🎯 Overview

Mimod Backoffice is a comprehensive e-commerce management system designed for managing a baby and kids clothing marketplace. The system provides complete features for product management, inventory tracking, sales reporting, and user access control.

**Current Version:** Laravel 12
**Database:** PostgreSQL
**Authentication:** Laravel Sanctum (Token-based API)

## 🚀 Tech Stack

### Backend
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** PostgreSQL
- **Authentication:** Laravel Sanctum
- **Architecture:** Repository Pattern
- **API:** RESTful with standardized response format

### Frontend (Current)
- **Template Engine:** Blade
- **Build Tool:** Vite 7
- **CSS Framework:** Tailwind CSS 4 + DaisyUI
- **JavaScript:** jQuery 3.7
- **Additional Libraries:**
  - Select2 (Advanced select boxes)
  - SortableJS (Drag & drop)
  - FilePond (File uploads)
  - Iconify (Icons)

### Frontend (Planned Migration)
- **Backoffice:** Inertia.js + Vue 3
- **Marketplace:** Nuxt 3

## ✨ Features

### 1. Access Control
- ✅ User Management (CRUD, status management)
- ✅ Role Management (CRUD, permission assignment)
- ✅ Permission Management (grouped by modules)
- ✅ Module Management (sortable sidebar)
- ✅ Activity Logging (user actions tracking)

### 2. Catalog Management
- ✅ Product Management (CRUD with variants & images)
- ✅ Category Management (hierarchical tree structure)
- ✅ Brand Management (CRUD with logo upload)
- ✅ Product Variants (SKU, size, color, stock, pricing)
- ✅ Multiple Image Upload (with primary image selection)
- ✅ Auto-generated Slugs (from product names)

### 3. Reports
- ✅ Sales Reports
- ✅ Revenue Reports
- ✅ Inventory Reports
- ✅ Product Performance Analytics

### 4. Settings
- ✅ General Settings (store info, contact, social media)
- ✅ Payment Method Configuration
- ✅ Shipping Method Configuration
- ✅ Shipping Location Management

### 5. API Endpoints
- ✅ Catalog API (Products, Categories, Brands)
- ✅ Settings API (General, Payment, Shipping)
- ✅ Authentication API (Token management)
- ✅ Standardized Response Format (ResultBuilder pattern)

## 📁 Project Structure

```
mimod-backoffice/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AccessControl/      # Users, Roles, Permissions, Modules
│   │   │   ├── Api/                # API Controllers
│   │   │   │   ├── Catalog/        # Product, Category, Brand APIs
│   │   │   │   └── Settings/       # Settings APIs
│   │   │   ├── Catalog/            # Product, Category, Brand Management
│   │   │   ├── Reports/            # Sales, Revenue, Inventory Reports
│   │   │   └── Settings/           # Configuration Management
│   │   ├── Middleware/
│   │   │   └── PermissionMiddleware.php
│   │   └── Responses/
│   │       └── GeneralResponse/    # ResultBuilder & Response classes
│   ├── Models/
│   ├── Repositories/
│   │   ├── Contracts/              # Repository Interfaces
│   │   │   ├── AccessControl/
│   │   │   ├── Catalog/
│   │   │   └── Settings/
│   │   ├── AccessControl/          # Repository Implementations
│   │   ├── Catalog/
│   │   ├── Settings/
│   │   └── Cache/                  # Cached Repositories
│   ├── Helpers/
│   │   ├── helpers.php             # Global helper functions
│   │   └── SidebarHelper.php       # Dynamic sidebar rendering
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── RepositoryServiceProvider.php
│
├── database/
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php       # Main layout
│   │   │   └── partials/           # Reusable partials
│   │   ├── components/             # Blade components
│   │   └── pages/                  # Module pages
│   │       ├── access-control/
│   │       ├── catalog/
│   │       ├── reports/
│   │       └── settings/
│   │
│   └── js/
│       ├── components/             # Reusable JS components
│       │   ├── toast.js            # Toast notifications
│       │   └── loading.js          # Loading overlay
│       └── modules/                # Module-specific JS
│           ├── access-control/
│           ├── catalog/
│           └── settings/
│
├── routes/
│   ├── web.php                     # Web routes
│   └── api.php                     # API routes
│
├── public/
│   └── storage/                    # Symlinked storage
│       ├── products/               # Product images
│       └── brands/                 # Brand logos
│
└── docs/
    ├── api/                        # API documentation
    │   ├── catalog-api.md
    │   ├── settings-api.md
    │   └── sanctum-usage-guide.md
    └── general/                    # General documentation
        ├── BACKEND_DOCUMENTATION.md
        ├── PROJECT-PATTERN.md
        ├── db-schema.md
        └── PRODUCT_MODULE_IMPLEMENTATION.md
```

## 🛠 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- PostgreSQL 14+
- Node.js 20+ & npm
- Git

### Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd mimod-backoffice
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database**

Edit `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mimod_backoffice
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations & seeders**
```bash
php artisan migrate --seed
```

7. **Create storage symlink**
```bash
php artisan storage:link
```

8. **Build assets**
```bash
npm run build
# or for development
npm run dev
```

9. **Start the server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

### Default Credentials

After seeding, you can login with:
- **Email:** admin@example.com
- **Password:** password

## ⚙️ Configuration

### Storage Configuration

Product images and brand logos are stored in:
- `storage/app/public/products/`
- `storage/app/public/brands/`

### Cache Configuration

Module cache is enabled for performance. Clear cache:
```bash
php artisan cache:clear
```

### Permission Configuration

Permissions are grouped by modules and actions:
```
{module}.{feature}.{action}

Examples:
- catalog.products.view
- catalog.products.create
- access-control.roles.update
```

## 💻 Development

### Running in Development Mode

```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server
npm run dev
```

### Database Operations

```bash
# Fresh migration with seed
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=ProductSeeder

# Rollback last migration
php artisan migrate:rollback
```

### Code Style

```bash
# Format code with Laravel Pint
./vendor/bin/pint
```

## 📚 API Documentation

### Authentication

All API endpoints require Sanctum token authentication:

```bash
# Get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'

# Use token
curl -X GET http://localhost:8000/api/catalog/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response Format

All API responses follow standardized format:

**Success:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": { }
}
```

**Error:**
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Resource not found"
}
```

### API Endpoints

- **Catalog API:** See [docs/api/catalog-api.md](docs/api/catalog-api.md)
- **Settings API:** See [docs/api/settings-api.md](docs/api/settings-api.md)
- **Sanctum Guide:** See [docs/api/sanctum-usage-guide.md](docs/api/sanctum-usage-guide.md)

## 🏗 Architecture Pattern

### Repository Pattern

This project implements the Repository Pattern for separation of concerns:

```
Controller → Repository Interface → Repository Implementation → Database
```

**Benefits:**
- Clean code separation
- Easy to test
- Cacheable
- Swappable data sources
- Framework-agnostic business logic

### Example Implementation

**1. Define Interface:**
```php
// app/Repositories/Contracts/Catalog/ProductRepositoryInterface.php
interface ProductRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
```

**2. Implement Repository:**
```php
// app/Repositories/Catalog/ProductRepository.php
class ProductRepository implements ProductRepositoryInterface
{
    protected $table = 'products';

    public function getAll()
    {
        return DB::table($this->table)->get();
    }
    // ... other methods
}
```

**3. Bind in Service Provider:**
```php
// app/Providers/RepositoryServiceProvider.php
$this->app->bind(
    ProductRepositoryInterface::class,
    ProductRepository::class
);
```

**4. Use in Controller:**
```php
class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function index()
    {
        $products = $this->productRepo->getAll();
        return view('products.index', compact('products'));
    }
}
```

### ResultBuilder Pattern

API responses use ResultBuilder for consistency:

```php
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;

$result = (new ResultBuilder())
    ->setStatus(true)
    ->setStatusCode('200')
    ->setMessage('Products retrieved successfully')
    ->setData($products)
    ->build();

return Response::success($result);
```

## 💾 Database Schema

### Key Tables

**Access Control:**
- `users` - User accounts
- `roles` - User roles (super-admin, admin, staff)
- `permissions` - System permissions
- `permission_groups` - Permission grouping
- `modules` - Sidebar modules
- `user_activities` - Activity logging

**Catalog:**
- `products` - Product master data
- `product_variants` - Product SKUs with pricing & stock
- `product_images` - Product images
- `product_categories` - Product-category relationships
- `categories` - Hierarchical category tree
- `brands` - Product brands

**Settings:**
- `general_settings` - Store configuration
- `payment_methods` - Payment options
- `shipping_methods` - Shipping options
- `shipping_locations` - Available shipping zones

See detailed schema: [docs/general/db-schema.md](docs/general/db-schema.md)

## 🔄 Migration Guide

### Framework Migration (Laravel → Other)

This project is designed to be framework-agnostic through the Repository Pattern. To migrate to another framework:

**1. Keep Business Logic Layer:**
- Repository interfaces define contracts
- Business logic stays in repositories
- No framework dependencies in interfaces

**2. Replace Framework-Specific Code:**
- Controllers (map routes to repositories)
- Views (use new framework's template engine)
- Middleware (implement framework's middleware pattern)
- Service providers (use new framework's DI container)

**3. Migration Example (Laravel → Express.js):**

```javascript
// Repository stays the same concept
class ProductRepository {
    async getAll() {
        return await db.query('SELECT * FROM products');
    }
}

// Controller adapts to Express
app.get('/api/products', async (req, res) => {
    const products = await productRepo.getAll();

    const result = new ResultBuilder()
        .setStatus(true)
        .setStatusCode('200')
        .setMessage('Products retrieved successfully')
        .setData(products)
        .build();

    res.json(result);
});
```

### Frontend Migration (Blade → Vue/Nuxt)

**Current State:** Blade + jQuery
**Target State:** Inertia.js + Vue 3 (Backoffice) | Nuxt 3 (Marketplace)

**Migration Steps:**

1. **API-First Approach:**
   - ✅ APIs already implemented
   - ✅ Standardized response format
   - ✅ Sanctum authentication ready

2. **Install Inertia.js:**
```bash
composer require inertiajs/inertia-laravel
npm install @inertiajs/vue3
```

3. **Convert Blade to Vue Components:**
```vue
<!-- resources/js/Pages/Catalog/Products/Index.vue -->
<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    products: Object
})
</script>

<template>
    <div class="grid grid-cols-1 gap-4">
        <ProductCard
            v-for="product in products.data"
            :key="product.id"
            :product="product"
        />
    </div>
</template>
```

4. **Update Controllers:**
```php
use Inertia\Inertia;

public function index()
{
    $products = $this->productRepo->getAllWithRelations();
    return Inertia::render('Catalog/Products/Index', [
        'products' => $products
    ]);
}
```

### Third-Party Integration Guide

**Example: Integrating Payment Gateway**

1. **Create Service Interface:**
```php
// app/Services/Contracts/PaymentServiceInterface.php
interface PaymentServiceInterface
{
    public function createPayment(array $data);
    public function verifyPayment(string $transactionId);
}
```

2. **Implement Service:**
```php
// app/Services/Payment/MidtransService.php
class MidtransService implements PaymentServiceInterface
{
    public function createPayment(array $data)
    {
        // Midtrans-specific implementation
    }
}
```

3. **Bind in Provider:**
```php
$this->app->bind(
    PaymentServiceInterface::class,
    MidtransService::class
);
```

4. **Use in Controller:**
```php
public function __construct(
    protected PaymentServiceInterface $paymentService
) {}
```

This pattern allows easy switching between payment providers without changing controller code.

## 📖 Additional Documentation

- **Backend Documentation:** [docs/general/BACKEND_DOCUMENTATION.md](docs/general/BACKEND_DOCUMENTATION.md)
- **Project Patterns:** [docs/general/PROJECT-PATTERN.md](docs/general/PROJECT-PATTERN.md)
- **Product Module:** [docs/general/PRODUCT_MODULE_IMPLEMENTATION.md](docs/general/PRODUCT_MODULE_IMPLEMENTATION.md)
- **Database Schema:** [docs/general/db-schema.md](docs/general/db-schema.md)

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Coding Standards

- Follow PSR-12 coding style
- Use Repository Pattern for data access
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation when needed

### Branch Naming

- `feature/` - New features
- `fix/` - Bug fixes
- `refactor/` - Code refactoring
- `docs/` - Documentation updates

## 📄 License

This project is proprietary software. All rights reserved.

## 👥 Team

- **Backend Development:** Laravel 12 + Repository Pattern
- **Frontend Development:** Blade + Tailwind CSS + DaisyUI
- **API Development:** RESTful API with Sanctum Authentication
- **Database:** PostgreSQL

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check documentation in `/docs` folder

---

**Built with ❤️ for Mimod Kids Clothing Marketplace**
