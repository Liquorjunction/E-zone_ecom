<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $guardName = 'admin';
        $modules = ['napsLogs', 'napsStatus'];
        $permissions = [];

        // Generate CRUD permissions for each module
        foreach ($modules as $module) {
            $permissions[] = $module . '.read';
            $permissions[] = $module . '.create';
            $permissions[] = $module . '.update';
            $permissions[] = $module . '.delete';
        }

        // Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        // Assign permissions to super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $allPermissions = Permission::where('guard_name', $guardName)->get();
            $superAdminRole->syncPermissions($allPermissions);
        }
    }

    public function down(): void
    {
        $guardName = 'admin';
        $modules = ['napsLogs', 'napsStatus'];
        $permissions = [];

        // Generate CRUD permissions for each module
        foreach ($modules as $module) {
            $permissions[] = $module . '.read';
            $permissions[] = $module . '.create';
            $permissions[] = $module . '.update';
            $permissions[] = $module . '.delete';
        }

        // Revoke permissions from super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $superAdminRole->revokePermissionTo($permissions);
        }

        // Delete the permissions
        Permission::whereIn('name', $permissions)->where('guard_name', $guardName)->delete();
    }
};
