<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Reset cached roles and permissions to ensure fresh start
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define the guard name explicitly for the separate 'admins' table
        $guardName = 'admin';

        // 3. Define Modules (Resources) that Admins will manage
        $modules = [
            'user management',        // Manage other admins
            'role',         // Manage roles
        ];

        // 4. Define Access Levels (Standard CRUD)
        $actions = ['create', 'read', 'update', 'delete'];

        // 5. Loop and Create Permissions (e.g., 'admin.create', 'user.delete')
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "$module.$action",
                    'guard_name' => $guardName
                ]);
            }
        }

        // 6. Create Special Standalone Permissions
        // 'profile' permissions for admins to manage their own account

        // 7. Create Roles specifically for the 'admin' guard
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guardName]);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guardName]);

        // 8. Assign Permissions to Roles

        // Super Admin gets ALL permissions associated with the 'admin' guard
        $allAdminPermissions = Permission::where('guard_name', $guardName)->get();
        $superAdminRole->syncPermissions($allAdminPermissions);

        // Regular Admin gets a specific subset of permissions
        // (Example: Can view dashboard, manage their profile, and read users)
        $defaultAdminPermissions = [
        ];

        foreach ($defaultAdminPermissions as $permName) {
            $perm = Permission::where('name', $permName)->where('guard_name', $guardName)->first();
            if ($perm) {
                $adminRole->givePermissionTo($perm);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'admin';

        // Delete all permissions and roles associated with this guard ONLY
        Permission::where('guard_name', $guardName)->delete();
        Role::where('guard_name', $guardName)->delete();
    }
};
