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
            'why_choose_us.create',
            'why_choose_us.read',
            'why_choose_us.update',
            'why_choose_us.delete',
        ];
        // 1. Create Permissions for the Why Choose Us module
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
            'why_choose_us.create',
            'why_choose_us.read',
            'why_choose_us.update',
            'why_choose_us.delete',
        ];

        // Remove permissions from the 'super-admin' Role
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $superAdminRole->revokePermissionTo($permissions);
        }

        // Delete the permissions
        Permission::whereIn('name', $permissions)->where('guard_name', $guardName)->delete();
    }
};
