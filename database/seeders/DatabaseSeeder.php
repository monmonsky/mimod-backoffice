<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Seed roles first (required by users)
            RoleSeeder::class,

            // 2. Seed users (requires roles)
            UserSeeder::class,

            // 3. Seed modules (required by role_modules)
            ModuleSeeder::class,

            // 4. Seed permission groups
            PermissionGroupSeeder::class,

            // 5. Seed permissions
            PermissionSeeder::class,

            // 6. Seed permission group items (requires permission_groups and permissions)
            PermissionGroupItemSeeder::class,

            // 7. Seed role permissions (requires roles and permissions)
            RolePermissionSeeder::class,

            // 8. Seed role modules (requires roles and modules)
            RoleModuleSeeder::class,

            // 9. Seed settings
            SettingsSeeder::class,

            // 10. Seed catalog data (categories, brands, products)
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,

            // 11. Seed customers (required by orders)
            CustomerSeeder::class,

            // 12. Seed orders (requires customers and products)
            OrderSeeder::class,
        ]);
    }
}
