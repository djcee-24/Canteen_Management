<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Menu management
            'view menu categories',
            'create menu categories',
            'edit menu categories',
            'delete menu categories',
            'view menu items',
            'create menu items',
            'edit menu items',
            'delete menu items',
            'edit own menu items',
            
            // Order management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'process orders',
            'view own orders',
            
            // Financial management
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'view tenant rentals',
            'create tenant rentals',
            'edit tenant rentals',
            'delete tenant rentals',
            
            // Staff management
            'view staff attendance',
            'create staff attendance',
            'edit staff attendance',
            'delete staff attendance',
            'view own attendance',
            
            // Dashboard and reports
            'view admin dashboard',
            'view tenant dashboard',
            'view reports',
            'export reports',
            
            // System settings
            'manage settings',
            'manage system',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin Role (Concessionaire)
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Tenant Role
        $tenantRole = Role::create(['name' => 'tenant']);
        $tenantRole->givePermissionTo([
            'view menu categories',
            'view menu items',
            'create menu items',
            'edit own menu items',
            'view orders',
            'create orders',
            'process orders',
            'view expenses',
            'create expenses',
            'edit expenses',
            'view tenant dashboard',
            'view reports',
        ]);

        // Staff Role
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view menu categories',
            'view menu items',
            'view orders',
            'create orders',
            'process orders',
            'view own attendance',
        ]);

        // Customer Role
        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view menu categories',
            'view menu items',
            'create orders',
            'view own orders',
        ]);

        // Guest Role
        $guestRole = Role::create(['name' => 'guest']);
        $guestRole->givePermissionTo([
            'view menu categories',
            'view menu items',
            'create orders',
        ]);

        // Create default admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@canteen.com',
            'password' => bcrypt('password'),
            'user_type' => 'admin',
            'phone' => '+1234567890',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create sample tenant user
        $tenant = User::create([
            'name' => 'Tenant User',
            'email' => 'tenant@canteen.com',
            'password' => bcrypt('password'),
            'user_type' => 'tenant',
            'phone' => '+1234567891',
            'monthly_rental' => 500.00,
            'rental_start_date' => now()->startOfMonth(),
            'business_description' => 'Sample food vendor specializing in local cuisine',
            'is_active' => true,
        ]);
        $tenant->assignRole('tenant');

        // Create sample staff user
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@canteen.com',
            'password' => bcrypt('password'),
            'user_type' => 'staff',
            'phone' => '+1234567892',
            'hourly_rate' => 15.00,
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // Create sample customer user
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@canteen.com',
            'password' => bcrypt('password'),
            'user_type' => 'customer',
            'phone' => '+1234567893',
            'is_active' => true,
        ]);
        $customer->assignRole('customer');
    }
}