# Project Pattern Documentation - Mimod Backoffice

## 📋 Table of Contents
- [Architecture Overview](#architecture-overview)
- [Folder Structure](#folder-structure)
- [Pattern Examples](#pattern-examples)
- [Naming Conventions](#naming-conventions)
- [Database Structure](#database-structure)

---

## 🏗 Architecture Overview

Project ini menggunakan **Laravel 11** dengan arsitektur **Repository Pattern** dan mengikuti struktur modular untuk memudahkan development dan maintenance.

### Tech Stack:
- **Backend**: Laravel 11 (PHP)
- **Frontend**: Blade Templates + Vite + jQuery
- **Database**: MySQL/PostgreSQL
- **CSS Framework**: Tailwind CSS + DaisyUI
- **Icons**: Iconify

---

## 📁 Folder Structure

```
mimod-backoffice/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controllers (per module)
│   │   │   ├── AccessControl/    # Module Access Control
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── PermissionController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Catalog/          # Module Catalog
│   │   │   └── Settings/         # Module Settings
│   │   └── Middleware/           # Custom middleware
│   │
│   ├── Models/                   # Eloquent Models
│   │   ├── User.php
│   │   └── UserActivity.php
│   │
│   ├── Repositories/             # Repository Pattern
│   │   ├── Contracts/            # Repository Interfaces
│   │   │   ├── AccessControl/
│   │   │   │   ├── RoleRepositoryInterface.php
│   │   │   │   └── ModuleRepositoryInterface.php
│   │   │   └── UserRepositoryInterface.php
│   │   │
│   │   └── AccessControl/        # Repository Implementation
│   │       ├── RoleRepository.php
│   │       └── ModuleRepository.php
│   │
│   └── Providers/
│       └── RepositoryServiceProvider.php
│
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
│
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layouts/
│   │   │   └── app.blade.php    # Main layout
│   │   └── pages/               # Page views (per module)
│   │       ├── access-control/
│   │       │   ├── roles/
│   │       │   │   ├── index.blade.php
│   │       │   │   ├── create.blade.php
│   │       │   │   └── edit.blade.php
│   │       │   └── users/
│   │       ├── catalog/
│   │       └── settings/
│   │
│   └── js/                      # JavaScript modules
│       ├── utils/
│       │   └── ajax.js          # Ajax helper utility
│       └── modules/             # JS per module
│           ├── access-control/
│           │   ├── roles/
│           │   │   ├── index.js
│           │   │   ├── create.js
│           │   │   └── edit.js
│           │   └── users/
│           ├── catalog/
│           └── settings/
│
└── routes/
    ├── web.php                  # Web routes
    └── api.php                  # API routes
```

---

## 🎯 Pattern Examples

### 1. Controller Pattern

#### Location: `app/Http/Controllers/{Module}/{Name}Controller.php`

**Example**: `app/Http/Controllers/AccessControl/RoleController.php`

```php
<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AccessControl\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(
        RoleRepositoryInterface $roleRepository
    ) {
        $this->roleRepo = $roleRepository;
    }

    // Index - List all data
    public function index()
    {
        $roles = $this->roleRepo->getAllWithCounts();
        $statistics = $this->roleRepo->getStatistics();

        return view('pages.access-control.roles.index', compact('roles', 'statistics'));
    }

    // Create - Show create form
    public function create()
    {
        return view('pages.access-control.roles.create');
    }

    // Store - Save new data
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:roles,name|max:100',
                // ... other validations
            ]);

            DB::beginTransaction();

            $role = $this->roleRepo->create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Edit - Show edit form
    public function edit($id)
    {
        $role = $this->roleRepo->findById($id);
        return view('pages.access-control.roles.edit', compact('role'));
    }

    // Update - Update existing data
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:roles,name,' . $id,
                // ... other validations
            ]);

            DB::beginTransaction();

            $role = $this->roleRepo->update($id, $validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Destroy - Delete data
    public function destroy($id)
    {
        try {
            $this->roleRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
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

**Controller Naming Convention**:
- Format: `{Module}/{Feature}Controller.php`
- Example: `AccessControl/RoleController.php`, `Catalog/ProductController.php`

---

### 2. Repository Pattern

#### Interface Location: `app/Repositories/Contracts/{Module}/{Name}RepositoryInterface.php`

**Example**: `app/Repositories/Contracts/AccessControl/RoleRepositoryInterface.php`

```php
<?php

namespace App\Repositories\Contracts\AccessControl;

interface RoleRepositoryInterface
{
    public function getAll();
    public function getAllWithCounts();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleActive($id);
    public function getStatistics();
}
```

#### Implementation Location: `app/Repositories/{Module}/{Name}Repository.php`

**Example**: `app/Repositories/AccessControl/RoleRepository.php`

```php
<?php

namespace App\Repositories\AccessControl;

use App\Repositories\Contracts\AccessControl\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleRepositoryInterface
{
    protected $tableName = 'roles';

    private function table()
    {
        return DB::table($this->tableName);
    }

    public function getAll()
    {
        return $this->table()->orderBy('priority', 'desc')->get();
    }

    public function findById($id)
    {
        $role = $this->table()->where('id', $id)->first();

        if (!$role) {
            throw new \Exception("Role not found");
        }

        return $role;
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

**Repository Registration** in `app/Providers/RepositoryServiceProvider.php`:

```php
public function register(): void
{
    $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
}
```

---

### 3. Routes Pattern

#### Location: `routes/web.php`

```php
// Group by module with prefix
Route::middleware('auth.token')->group(function () {

    // Access Control Module
    Route::group(['prefix' => 'access-control'], function () {

        // Roles Routes
        Route::group(['prefix' => 'role'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\RoleController@index')
                ->name('role.index')
                ->middleware('permission:access-control.roles.view');

            Route::get('/create', 'App\Http\Controllers\AccessControl\RoleController@create')
                ->name('role.create')
                ->middleware('permission:access-control.roles.create');

            Route::post('/store', 'App\Http\Controllers\AccessControl\RoleController@store')
                ->name('role.store')
                ->middleware('permission:access-control.roles.create');

            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\RoleController@edit')
                ->name('role.edit')
                ->middleware('permission:access-control.roles.update');

            Route::put('/{id}', 'App\Http\Controllers\AccessControl\RoleController@update')
                ->name('role.update')
                ->middleware('permission:access-control.roles.update');

            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\RoleController@destroy')
                ->name('role.destroy')
                ->middleware('permission:access-control.roles.delete');
        });
    });
});
```

**Route Naming Convention**:
- Format: `{module}.{feature}.{action}`
- Example: `role.index`, `role.create`, `role.store`, `role.edit`, `role.update`, `role.destroy`

---

### 4. View Pattern (Blade)

#### Location: `resources/views/pages/{module}/{feature}/{action}.blade.php`

**Example Index**: `resources/views/pages/access-control/roles/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Roles')
@section('page_title', 'Role')
@section('page_subtitle', 'Role Management')

@section('content')
<!-- Breadcrumb -->
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Role Management</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Access Control</li>
            <li class="opacity-80">Roles</li>
        </ul>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Statistics cards here -->
</div>

<!-- Data Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <!-- Search & Actions -->
            <div class="flex items-center justify-between px-5 pt-5">
                <label class="input input-sm">
                    <span class="iconify lucide--search"></span>
                    <input id="searchInput" placeholder="Search roles" />
                </label>

                @if(hasPermission('access-control.roles.create'))
                <a href="{{ route('role.create') }}" class="btn btn-primary btn-sm">
                    <span class="iconify lucide--plus"></span>
                    Add Role
                </a>
                @endif
            </div>

            <!-- Table -->
            <div class="mt-4 overflow-auto">
                <table id="rolesTable" class="table">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->display_name }}</td>
                            <td>
                                <!-- Action buttons -->
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">No roles found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
@vite(['resources/js/modules/access-control/roles/index.js'])
@endsection
```

**View Naming Convention**:
- **Index**: `index.blade.php` (list all data)
- **Create**: `create.blade.php` (form tambah data)
- **Edit**: `edit.blade.php` (form edit data)

---

### 5. JavaScript Module Pattern

#### Location: `resources/js/modules/{module}/{feature}/{action}.js`

**Example**: `resources/js/modules/access-control/roles/index.js`

```javascript
import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#rolesTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Toggle active status
    $('.toggle-form').on('submit', async function(e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.post(url, null, {
                loadingMessage: 'Updating status...',
                successMessage: 'Role status updated successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });

    // Delete form handler
    $('.delete-form').on('submit', async function(e) {
        e.preventDefault();

        const confirmed = confirm('Are you sure?');
        if (!confirmed) return;

        const $form = $(this);
        const url = $form.attr('action');

        try {
            await Ajax.delete(url, {
                loadingMessage: 'Deleting role...',
                successMessage: 'Role deleted successfully',
                onSuccess: () => {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        } catch (error) {
            // Error handled by Ajax helper
        }
    });
});
```

**Ajax Helper** (`resources/js/utils/ajax.js`):
```javascript
import Toast from '../components/toast.js';

class Ajax {
    static async request(method, url, data = null, options = {}) {
        const {
            loadingMessage = 'Processing...',
            successMessage = 'Success!',
            showToast = true,
            onSuccess = null,
            onError = null
        } = options;

        if (showToast && loadingMessage) {
            Toast.loading(loadingMessage);
        }

        try {
            const response = await $.ajax({
                url: url,
                method: method,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (showToast && successMessage) {
                Toast.success(successMessage);
            }

            if (onSuccess) {
                onSuccess(response);
            }

            return response;
        } catch (error) {
            const message = error.responseJSON?.message || 'An error occurred';

            if (showToast) {
                Toast.error(message);
            }

            if (onError) {
                onError(error);
            }

            throw error;
        }
    }

    static get(url, options = {}) {
        return this.request('GET', url, null, options);
    }

    static post(url, data, options = {}) {
        return this.request('POST', url, data, options);
    }

    static put(url, data, options = {}) {
        return this.request('PUT', url, data, options);
    }

    static delete(url, options = {}) {
        return this.request('DELETE', url, null, options);
    }
}

export default Ajax;
```

---

### 6. Migration Pattern

#### Location: `database/migrations/{timestamp}_{table_name}.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->integer('priority')->default(10);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

---

### 7. Seeder Pattern

#### Location: `database/seeders/{Name}Seeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super-admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access',
                'priority' => 100,
                'is_active' => true,
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ... more roles
        ];

        DB::table('roles')->insert($roles);
    }
}
```

---

## 📝 Naming Conventions

### 1. Controller
- **Format**: `{Feature}Controller`
- **Example**: `RoleController`, `UserController`, `ProductController`
- **Location**: `app/Http/Controllers/{Module}/`

### 2. Repository Interface
- **Format**: `{Feature}RepositoryInterface`
- **Example**: `RoleRepositoryInterface`, `UserRepositoryInterface`
- **Location**: `app/Repositories/Contracts/{Module}/`

### 3. Repository Implementation
- **Format**: `{Feature}Repository`
- **Example**: `RoleRepository`, `UserRepository`
- **Location**: `app/Repositories/{Module}/`

### 4. Model
- **Format**: `{SingularName}`
- **Example**: `User`, `Role`, `Product`
- **Location**: `app/Models/`

### 5. View Files
- **Format**: `{action}.blade.php`
- **Example**: `index.blade.php`, `create.blade.php`, `edit.blade.php`
- **Location**: `resources/views/pages/{module}/{feature}/`

### 6. JavaScript Files
- **Format**: `{action}.js`
- **Example**: `index.js`, `create.js`, `edit.js`
- **Location**: `resources/js/modules/{module}/{feature}/`

### 7. Route Names
- **Format**: `{feature}.{action}`
- **Example**: `role.index`, `role.create`, `role.store`

### 8. Permission Names
- **Format**: `{module}.{feature}.{action}`
- **Example**: `access-control.roles.view`, `access-control.roles.create`

### 9. Database Tables
- **Format**: `{plural_lowercase}`
- **Example**: `roles`, `users`, `products`

### 10. Migration Files
- **Format**: `{timestamp}_create_{table}_table.php`
- **Example**: `2025_10_02_000000_create_roles_table.php`

---

## 🗄 Database Structure

### Common Table Columns:
```php
$table->id();                           // Primary key
$table->string('name')->unique();       // Unique identifier
$table->string('display_name');         // Human readable name
$table->text('description')->nullable(); // Optional description
$table->boolean('is_active')->default(true);
$table->boolean('is_system')->default(false);
$table->timestamps();                   // created_at, updated_at
```

### Relationship Tables:
```php
// Pivot table example: role_permissions
$table->id();
$table->foreignId('role_id')->constrained()->onDelete('cascade');
$table->foreignId('permission_id')->constrained()->onDelete('cascade');
$table->timestamp('granted_at')->nullable();
$table->foreignId('granted_by')->nullable()->constrained('users');
```

---

## 🎨 Frontend Components

### 1. Button Patterns
```html
<!-- Primary Action -->
<button class="btn btn-primary btn-sm">
    <span class="iconify lucide--plus"></span>
    Add New
</button>

<!-- Secondary Action -->
<button class="btn btn-outline btn-sm">
    <span class="iconify lucide--download"></span>
    Export
</button>

<!-- Delete/Danger Action -->
<button class="btn btn-error btn-outline btn-sm">
    <span class="iconify lucide--trash"></span>
    Delete
</button>
```

### 2. Badge Patterns
```html
<!-- Status Active -->
<span class="badge badge-success badge-sm">Active</span>

<!-- Status Inactive -->
<span class="badge badge-error badge-sm">Inactive</span>

<!-- System/Protected -->
<span class="badge badge-warning badge-sm">System</span>

<!-- Info/Count -->
<span class="badge badge-primary badge-sm">{{ $count }}</span>
```

### 3. Search Input Pattern
```html
<label class="input input-sm">
    <span class="iconify lucide--search text-base-content/80 size-3.5"></span>
    <input
        class="w-24 sm:w-36"
        placeholder="Search..."
        type="search"
        id="searchInput" />
</label>
```

---

## 🔐 Permission System

### Permission Middleware:
```php
Route::get('/roles', 'RoleController@index')
    ->middleware('permission:access-control.roles.view');
```

### Blade Permission Check:
```blade
@if(hasPermission('access-control.roles.create'))
    <a href="{{ route('role.create') }}" class="btn btn-primary">
        Add Role
    </a>
@endif
```

### Permission Naming:
- **Format**: `{module}.{feature}.{action}`
- **Actions**: `view`, `create`, `update`, `delete`, `export`
- **Example**:
  - `access-control.roles.view`
  - `access-control.roles.create`
  - `access-control.users.update`
  - `catalog.products.delete`

---

## 📦 Module Creation Checklist

Ketika membuat module baru, ikuti checklist ini:

### Backend:
- [ ] Create Controller di `app/Http/Controllers/{Module}/`
- [ ] Create Repository Interface di `app/Repositories/Contracts/{Module}/`
- [ ] Create Repository Implementation di `app/Repositories/{Module}/`
- [ ] Register Repository di `RepositoryServiceProvider`
- [ ] Create Migration di `database/migrations/`
- [ ] Create Seeder di `database/seeders/`
- [ ] Add Routes di `routes/web.php`
- [ ] Create Permissions di seeder

### Frontend:
- [ ] Create View folder di `resources/views/pages/{module}/`
- [ ] Create `index.blade.php`, `create.blade.php`, `edit.blade.php`
- [ ] Create JS folder di `resources/js/modules/{module}/`
- [ ] Create `index.js`, `create.js`, `edit.js`
- [ ] Add to Vite config if needed

### Database:
- [ ] Run migration: `php artisan migrate`
- [ ] Run seeder: `php artisan db:seed --class={Name}Seeder`

---

## 🚀 Quick Start Example

### Contoh: Membuat Module "Categories"

1. **Create Migration**:
```bash
php artisan make:migration create_categories_table
```

2. **Create Controller**:
```php
// app/Http/Controllers/Catalog/CategoryController.php
namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepo->getAll();
        return view('pages.catalog.categories.index', compact('categories'));
    }
}
```

3. **Create Repository**:
```php
// app/Repositories/Contracts/CategoryRepositoryInterface.php
interface CategoryRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

// app/Repositories/CategoryRepository.php
class CategoryRepository implements CategoryRepositoryInterface
{
    protected $tableName = 'categories';

    // Implementation methods...
}
```

4. **Register Repository**:
```php
// app/Providers/RepositoryServiceProvider.php
$this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
```

5. **Add Routes**:
```php
// routes/web.php
Route::group(['prefix' => 'catalog'], function () {
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'App\Http\Controllers\Catalog\CategoryController@index')
            ->name('categories.index')
            ->middleware('permission:catalog.categories.view');
        // ... other routes
    });
});
```

6. **Create Views**:
```blade
<!-- resources/views/pages/catalog/categories/index.blade.php -->
@extends('layouts.app')

@section('content')
    <!-- Your content here -->
@endsection

@section('customjs')
@vite(['resources/js/modules/catalog/categories/index.js'])
@endsection
```

7. **Create JS Module**:
```javascript
// resources/js/modules/catalog/categories/index.js
import $ from 'jquery';
import Ajax from '../../../utils/ajax.js';

$(document).ready(function() {
    // Your JavaScript logic here
});
```

---

## 📚 Common Patterns Reference

### CRUD Operations Flow:

1. **List/Index** (`GET /resource`)
   - Controller → Repository → View
   - Display all data in table format

2. **Create** (`GET /resource/create`)
   - Controller → View (form)
   - Show create form

3. **Store** (`POST /resource/store`)
   - Validate → Repository → JSON Response
   - Save new data

4. **Edit** (`GET /resource/{id}/edit`)
   - Controller → Repository (get data) → View (form with data)
   - Show edit form with existing data

5. **Update** (`PUT /resource/{id}`)
   - Validate → Repository → JSON Response
   - Update existing data

6. **Delete** (`DELETE /resource/{id}`)
   - Repository → JSON Response
   - Delete data

---

## 🔧 Helper Functions

### Available Global Helpers:
- `hasPermission($permission)` - Check user permission
- `currentUser()` - Get current logged in user
- `logActivity($action, $description, $module, $recordId)` - Log user activity

---

## 📖 Usage

Ketika membuat module baru, cukup rujuk ke file ini:

```
"Tolong buatkan module Product Management dengan fitur CRUD.
Untuk pattern dan strukturnya, ikuti yang ada di PROJECT-PATTERN.md"
```

---

**Last Updated**: 2025-10-03
**Project**: Mimod Backoffice
**Laravel Version**: 11.x
