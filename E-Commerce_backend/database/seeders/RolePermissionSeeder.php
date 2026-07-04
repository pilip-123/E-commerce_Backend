<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'group' => 'Dashboard'],

            ['name' => 'products.view', 'display_name' => 'View Products', 'group' => 'Products'],
            ['name' => 'products.create', 'display_name' => 'Create Products', 'group' => 'Products'],
            ['name' => 'products.edit', 'display_name' => 'Edit Products', 'group' => 'Products'],
            ['name' => 'products.delete', 'display_name' => 'Delete Products', 'group' => 'Products'],

            ['name' => 'categories.view', 'display_name' => 'View Categories', 'group' => 'Categories'],
            ['name' => 'categories.create', 'display_name' => 'Create Categories', 'group' => 'Categories'],
            ['name' => 'categories.edit', 'display_name' => 'Edit Categories', 'group' => 'Categories'],
            ['name' => 'categories.delete', 'display_name' => 'Delete Categories', 'group' => 'Categories'],

            ['name' => 'suppliers.view', 'display_name' => 'View Suppliers', 'group' => 'Suppliers'],
            ['name' => 'suppliers.create', 'display_name' => 'Create Suppliers', 'group' => 'Suppliers'],
            ['name' => 'suppliers.edit', 'display_name' => 'Edit Suppliers', 'group' => 'Suppliers'],
            ['name' => 'suppliers.delete', 'display_name' => 'Delete Suppliers', 'group' => 'Suppliers'],

            ['name' => 'customers.view', 'display_name' => 'View Customers', 'group' => 'Customers'],
            ['name' => 'customers.create', 'display_name' => 'Create Customers', 'group' => 'Customers'],
            ['name' => 'customers.edit', 'display_name' => 'Edit Customers', 'group' => 'Customers'],
            ['name' => 'customers.delete', 'display_name' => 'Delete Customers', 'group' => 'Customers'],

            ['name' => 'sales.view', 'display_name' => 'View Sales', 'group' => 'Sales'],
            ['name' => 'sales.create', 'display_name' => 'Create Sales', 'group' => 'Sales'],
            ['name' => 'sales.edit', 'display_name' => 'Edit Sales', 'group' => 'Sales'],
            ['name' => 'sales.cancel', 'display_name' => 'Cancel Sales', 'group' => 'Sales'],

            ['name' => 'inventory.view', 'display_name' => 'View Inventory', 'group' => 'Inventory'],
            ['name' => 'inventory.update', 'display_name' => 'Update Inventory', 'group' => 'Inventory'],

            ['name' => 'reports.view', 'display_name' => 'View Reports', 'group' => 'Reports'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'group' => 'Reports'],

            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'Users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'group' => 'Users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'group' => 'Users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'group' => 'Users'],

            ['name' => 'roles.view', 'display_name' => 'View Roles', 'group' => 'Roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'group' => 'Roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'group' => 'Roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'group' => 'Roles'],

            ['name' => 'settings.view', 'display_name' => 'View Settings', 'group' => 'Settings'],
            ['name' => 'settings.edit', 'display_name' => 'Update Settings', 'group' => 'Settings'],

            ['name' => 'promotions.view', 'display_name' => 'View Promotions', 'group' => 'Promotions'],
            ['name' => 'promotions.create', 'display_name' => 'Create Promotions', 'group' => 'Promotions'],
            ['name' => 'promotions.edit', 'display_name' => 'Edit Promotions', 'group' => 'Promotions'],
            ['name' => 'promotions.delete', 'display_name' => 'Delete Promotions', 'group' => 'Promotions'],

            ['name' => 'vipcodes.view', 'display_name' => 'View VIP Codes', 'group' => 'VIP Codes'],
            ['name' => 'vipcodes.generate', 'display_name' => 'Generate VIP Codes', 'group' => 'VIP Codes'],
            ['name' => 'vipcodes.delete', 'display_name' => 'Delete VIP Codes', 'group' => 'VIP Codes'],

            ['name' => 'alerts.view', 'display_name' => 'View Alerts', 'group' => 'Alerts'],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::table('permission_role')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($permissions as $data) {
            Permission::create($data);
        }

        $roles = [
            'admin' => ['*'],
            'manager' => [
                'dashboard.view',
                'users.view', 'users.create', 'users.edit',
                'products.view', 'products.create', 'products.edit', 'products.delete',
                'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
                'customers.view', 'customers.create', 'customers.edit',
                'sales.view', 'sales.create', 'sales.edit',
                'inventory.view', 'inventory.update',
                'reports.view', 'reports.export',
                'promotions.view', 'promotions.create', 'promotions.edit', 'promotions.delete',
                'vipcodes.view', 'vipcodes.generate', 'vipcodes.delete',
                'alerts.view',
            ],
            'staff' => [
                'dashboard.view',
                'products.view',
                'customers.view', 'customers.create',
                'sales.view', 'sales.create',
                'inventory.view',
                'promotions.view',
            ],
            'customer' => [
                'products.view',
                'sales.view',
            ],
        ];

        foreach ($roles as $role => $perms) {
            foreach ($perms as $perm) {
                if ($perm === '*') {
                    continue;
                }
                $permission = Permission::where('name', $perm)->first();
                if ($permission) {
                    DB::table('permission_role')->insert([
                        'permission_id' => $permission->id,
                        'role' => $role,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
