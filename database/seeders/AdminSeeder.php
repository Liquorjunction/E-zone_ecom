<?php

namespace Database\Seeders;

use App\Models\admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        $existingAdmin = admin::where('username', 'superadmin')->first();

        if (!$existingAdmin) {
            $admin = admin::create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);

            $admin->assignRole('super-admin');
        } else {
            $admin = $existingAdmin;
        }

        // Assign all permissions to super-admin role
        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super-admin')->where('guard_name', 'admin')->first();
        if ($superAdminRole) {
            $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get();
            $superAdminRole->syncPermissions($permissions);
        }
    }
}
