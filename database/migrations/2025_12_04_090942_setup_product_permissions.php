<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'admin';
        $permissions = [
            'products.create',
            'products.read',
            'products.update',
            'products.delete',
            'products.import',
        ];

        // 1. Create Permissions for the Products module
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }
        // 2. Assign these permissions to the existing 'super-admin' Role
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            // Sync all permissions to ensure super-admin has all, including newly added ones
            $allPermissions = Permission::where('guard_name', $guardName)->get();
            $superAdminRole->syncPermissions($allPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'admin';
        $permissions = [
            'products.create',
            'products.read',
            'products.update',
            'products.delete',
            'products.import',
        ];
        // 1. Revoke permissions from Super Admin
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $superAdminRole->revokePermissionTo($permissions);
            // Sync all permissions to ensure super-admin has all remaining ones
            $allPermissions = Permission::where('guard_name', $guardName)->get();
            $superAdminRole->syncPermissions($allPermissions);
        }
        // 2. Delete the Products permissions
        Permission::whereIn('name', $permissions)->where('guard_name', $guardName)->delete();
    }
};
