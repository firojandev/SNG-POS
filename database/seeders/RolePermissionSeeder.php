<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all static permissions with display names
        // Format: ['name' => 'display_name']
        $permissions = [
            // Dashboard
            'view_dashboard' => 'View Dashboard',

            // Product Management
            'manage_category' => 'Manage Categories',
            'manage_unit' => 'Manage Units',
            'manage_tax' => 'Manage Taxes',
            'manage_vat' => 'Manage VAT',
            'manage_product' => 'Manage Products',

            // Supplier & Customer Management
            'manage_supplier' => 'Manage Suppliers',
            'manage_customer' => 'Manage Customers',

            // Purchase Management
            'manage_purchase' => 'Manage Purchases',

            // Sales/Invoice Management
            'manage_sale' => 'Manage Sales',

            // Finance Management
            'manage_income' => 'Manage Incomes',
            'manage_expense' => 'Manage Expenses',
            'manage_debt' => 'Manage Debts',
            'manage_asset' => 'Manage Assets',
            'manage_lend' => 'Manage Lend',
            'manage_security_money' => 'Manage Security Money',
            'manage_payment' => 'Manage Payments',

            // Reports
            'view_purchase_reports' => 'View Purchase Reports',
            'view_sale_reports' => 'View Sales Reports',
            'view_stock_reports' => 'View Stock Reports',
            'view_revenue_reports' => 'View Revenue Reports',
            'view_balance_sheet' => 'View Balance Sheet',

            // Settings & Configuration
            'admin_permission' => 'Admin (Manage Store, Currencies, General Setting, Roles & Permissions) ',
            'manage_staff' => 'Manage Staffs'
        ];

        // Create all permissions with display names
        foreach ($permissions as $name => $displayName) {
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['display_name' => $displayName]
            );
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->command->info('Roles and Permissions created successfully!');
        $this->command->info('Total Permissions: ' . count($permissions));
        $this->command->info('Admin role created with all permissions.');
    }
}
