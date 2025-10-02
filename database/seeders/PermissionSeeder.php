<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->truncate();

        $permissions = [
            // Dashboard
            [
                'name' => 'dashboard.view',
                'display_name' => 'View Dashboard',
                'description' => 'Permission to view dashboard',
                'module' => 'dashboard',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Access Control > Users
            [
                'name' => 'access-control.users.view',
                'display_name' => 'View Users',
                'description' => 'Permission to view users',
                'module' => 'users',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.users.create',
                'display_name' => 'Create User',
                'description' => 'Permission to create new user',
                'module' => 'users',
                'action' => 'create',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.users.update',
                'display_name' => 'Update User',
                'description' => 'Permission to update user',
                'module' => 'users',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.users.delete',
                'display_name' => 'Delete User',
                'description' => 'Permission to delete user',
                'module' => 'users',
                'action' => 'delete',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Access Control > Roles
            [
                'name' => 'access-control.roles.view',
                'display_name' => 'View Roles',
                'description' => 'Permission to view roles',
                'module' => 'roles',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.roles.create',
                'display_name' => 'Create Role',
                'description' => 'Permission to create new role',
                'module' => 'roles',
                'action' => 'create',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.roles.update',
                'display_name' => 'Update Role',
                'description' => 'Permission to update role',
                'module' => 'roles',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.roles.delete',
                'display_name' => 'Delete Role',
                'description' => 'Permission to delete role',
                'module' => 'roles',
                'action' => 'delete',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Access Control > Permissions
            [
                'name' => 'access-control.permissions.view',
                'display_name' => 'View Permissions',
                'description' => 'Permission to view permissions',
                'module' => 'permissions',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.permissions.create',
                'display_name' => 'Create Permission',
                'description' => 'Permission to create new permission',
                'module' => 'permissions',
                'action' => 'create',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.permissions.update',
                'display_name' => 'Update Permission',
                'description' => 'Permission to update permission',
                'module' => 'permissions',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.permissions.delete',
                'display_name' => 'Delete Permission',
                'description' => 'Permission to delete permission',
                'module' => 'permissions',
                'action' => 'delete',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Access Control > Modules
            [
                'name' => 'access-control.modules.view',
                'display_name' => 'View Modules',
                'description' => 'Permission to view modules',
                'module' => 'modules',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.modules.create',
                'display_name' => 'Create Module',
                'description' => 'Permission to create new module',
                'module' => 'modules',
                'action' => 'create',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.modules.update',
                'display_name' => 'Update Module',
                'description' => 'Permission to update module',
                'module' => 'modules',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.modules.delete',
                'display_name' => 'Delete Module',
                'description' => 'Permission to delete module',
                'module' => 'modules',
                'action' => 'delete',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Access Control > User Activities
            [
                'name' => 'access-control.user-activities.view',
                'display_name' => 'View User Activities',
                'description' => 'Permission to view user activities',
                'module' => 'user-activities',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.user-activities.export',
                'display_name' => 'Export User Activities',
                'description' => 'Permission to export user activities',
                'module' => 'user-activities',
                'action' => 'export',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'access-control.user-activities.clear',
                'display_name' => 'Clear User Activities',
                'description' => 'Permission to clear all user activities',
                'module' => 'user-activities',
                'action' => 'clear',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Generals > Store Info
            [
                'name' => 'settings.generals.store.view',
                'display_name' => 'View Store Info',
                'description' => 'Permission to view store information settings',
                'module' => 'store',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.generals.store.update',
                'display_name' => 'Update Store Info',
                'description' => 'Permission to update store information settings',
                'module' => 'store',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Generals > Email Settings
            [
                'name' => 'settings.generals.email.view',
                'display_name' => 'View Email Settings',
                'description' => 'Permission to view email settings',
                'module' => 'email',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.generals.email.update',
                'display_name' => 'Update Email Settings',
                'description' => 'Permission to update email settings',
                'module' => 'email',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Generals > SEO & Meta
            [
                'name' => 'settings.generals.seo.view',
                'display_name' => 'View SEO Settings',
                'description' => 'Permission to view SEO and meta settings',
                'module' => 'seo',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.generals.seo.update',
                'display_name' => 'Update SEO Settings',
                'description' => 'Permission to update SEO and meta settings',
                'module' => 'seo',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Generals > System Config
            [
                'name' => 'settings.generals.system.view',
                'display_name' => 'View System Config',
                'description' => 'Permission to view system configuration',
                'module' => 'system',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.generals.system.update',
                'display_name' => 'Update System Config',
                'description' => 'Permission to update system configuration',
                'module' => 'system',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Generals > API Tokens
            [
                'name' => 'settings.generals.api-tokens.view',
                'display_name' => 'View API Tokens',
                'description' => 'Permission to view API tokens',
                'module' => 'api-tokens',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.generals.api-tokens.generate',
                'display_name' => 'Generate API Tokens',
                'description' => 'Permission to generate API tokens',
                'module' => 'api-tokens',
                'action' => 'generate',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.generals.api-tokens.revoke',
                'display_name' => 'Revoke API Tokens',
                'description' => 'Permission to revoke API tokens',
                'module' => 'api-tokens',
                'action' => 'revoke',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Payments > Payment Methods
            [
                'name' => 'settings.payments.methods.view',
                'display_name' => 'View Payment Methods',
                'description' => 'Permission to view payment methods',
                'module' => 'payment-methods',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.payments.methods.update',
                'display_name' => 'Update Payment Methods',
                'description' => 'Permission to update payment methods',
                'module' => 'payment-methods',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Payments > Midtrans Config
            [
                'name' => 'settings.payments.midtrans.view',
                'display_name' => 'View Midtrans Config',
                'description' => 'Permission to view Midtrans configuration',
                'module' => 'midtrans-config',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.payments.midtrans.update',
                'display_name' => 'Update Midtrans Config',
                'description' => 'Permission to update Midtrans configuration',
                'module' => 'midtrans-config',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Payments > Tax Settings
            [
                'name' => 'settings.payments.tax.view',
                'display_name' => 'View Tax Settings',
                'description' => 'Permission to view tax settings',
                'module' => 'tax-settings',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.payments.tax.update',
                'display_name' => 'Update Tax Settings',
                'description' => 'Permission to update tax settings',
                'module' => 'tax-settings',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Shippings > Shipping Methods
            [
                'name' => 'settings.shippings.methods.view',
                'display_name' => 'View Shipping Methods',
                'description' => 'Permission to view shipping methods',
                'module' => 'shipping-methods',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.shippings.methods.update',
                'display_name' => 'Update Shipping Methods',
                'description' => 'Permission to update shipping methods',
                'module' => 'shipping-methods',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Shippings > RajaOngkir Config
            [
                'name' => 'settings.shippings.rajaongkir.view',
                'display_name' => 'View RajaOngkir Config',
                'description' => 'Permission to view RajaOngkir configuration',
                'module' => 'rajaongkir-config',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.shippings.rajaongkir.update',
                'display_name' => 'Update RajaOngkir Config',
                'description' => 'Permission to update RajaOngkir configuration',
                'module' => 'rajaongkir-config',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Settings > Shippings > Origin Address
            [
                'name' => 'settings.shippings.origin.view',
                'display_name' => 'View Origin Address',
                'description' => 'Permission to view origin address',
                'module' => 'origin-address',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'settings.shippings.origin.update',
                'display_name' => 'Update Origin Address',
                'description' => 'Permission to update origin address',
                'module' => 'origin-address',
                'action' => 'update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Catalog > Products > All Product
            [
                'name' => 'catalog.products.all-products.view',
                'display_name' => 'View All Products',
                'description' => 'Permission to view all products',
                'module' => 'all-products',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Catalog > Products > Add Product
            [
                'name' => 'catalog.products.add-products.view',
                'display_name' => 'View Add Products',
                'description' => 'Permission to view add products',
                'module' => 'add-products',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Catalog > Products > Categories
            [
                'name' => 'catalog.products.categories.view',
                'display_name' => 'View Categories Products',
                'description' => 'Permission to view categories products',
                'module' => 'categories',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Catalog > Products > Brands
            [
                'name' => 'catalog.products.brands.view',
                'display_name' => 'View Brands Products',
                'description' => 'Permission to view brands products',
                'module' => 'brands',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Catalog > Products > Variants
            [
                'name' => 'catalog.products.variants.view',
                'display_name' => 'View variants Products',
                'description' => 'Permission to view variants products',
                'module' => 'variants',
                'action' => 'view',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permissions')->insert($permissions);

        $this->command->info('Permissions seeded successfully.');
    }
}
