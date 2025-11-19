<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'admin';

        $permissions = [
            'slider.create',
            'slider.read',
            'slider.update',
            'slider.delete',
        ];

        // 1. Create Permissions for the Slider module
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName
            ]);
        }

        // 2. Assign these permissions to the existing 'super-admin' Role
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $guardName = 'admin';
        $permissions = [
            'slider.create',
            'slider.read',
            'slider.update',
            'slider.delete',
        ];

        // 1. Revoke permissions from Super Admin
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', $guardName)->first();
        if ($superAdminRole) {
            $superAdminRole->revokePermissionTo($permissions);
        }

        // 2. Delete the permissions
        Permission::whereIn('name', $permissions)->where('guard_name', $guardName)->delete();

        // Clear cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
