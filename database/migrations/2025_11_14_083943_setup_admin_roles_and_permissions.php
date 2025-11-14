<?php
// new_migration_file.php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define the guard name
        $guardName = 'admin';

        // Create Permissions
        Permission::create(['name' => 'create roles', 'guard_name' => $guardName]);
        Permission::create(['name' => 'assign permissions', 'guard_name' => $guardName]);
        Permission::create(['name' => 'create admins', 'guard_name' => $guardName]);
        Permission::create(['name' => 'edit any admin', 'guard_name' => $guardName]);
        Permission::create(['name' => 'toggle admin status', 'guard_name' => $guardName]);
        Permission::create(['name' => 'update own profile', 'guard_name' => $guardName]);
        Permission::create(['name' => 'reset own password', 'guard_name' => $guardName]);

        // Create Super Admin Role and assign all permissions
        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => $guardName]);
        // Give all permissions created for the admin guard
        $superAdminRole->givePermissionTo(Permission::where('guard_name', $guardName)->get());

        // Create Admin Role and assign specific permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => $guardName]);
        $adminRole->givePermissionTo([
            'update own profile',
            'reset own password',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define the guard name
        $guardName = 'admin';

        // Delete roles and permissions
        Permission::where('guard_name', $guardName)->delete();
        Role::where('guard_name', $guardName)->delete();
    }
};
