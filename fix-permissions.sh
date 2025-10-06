#!/bin/bash

echo "🔧 Fixing Role Permissions Issue..."
echo ""

echo "Step 1: Re-seeding Permission Groups..."
php artisan db:seed --class=PermissionGroupSeeder

echo ""
echo "Step 2: Re-seeding Permissions..."
php artisan db:seed --class=PermissionSeeder

echo ""
echo "Step 3: Re-seeding Permission Group Items (linking permissions to groups)..."
php artisan db:seed --class=PermissionGroupItemSeeder

echo ""
echo "Step 4: Re-seeding Role Permissions (assigning permissions to roles)..."
php artisan db:seed --class=RolePermissionSeeder

echo ""
echo "Step 5: Re-seeding Role Modules..."
php artisan db:seed --class=RoleModuleSeeder

echo ""
echo "✅ Done! Summary:"
echo ""

php artisan tinker --execute="
\$roles = DB::table('roles')->get(['id', 'name']);
foreach (\$roles as \$role) {
    \$permCount = DB::table('role_permissions')->where('role_id', \$role->id)->count();
    echo \"  - {\$role->name}: {\$permCount} permissions\" . PHP_EOL;
}
"

echo ""
echo "Please refresh your browser and try editing super_admin role."
echo "All permissions should now be checked!"
