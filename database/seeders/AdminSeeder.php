<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'), // Use a secure password!
                'status' => 'active',
            ]
        );

        // Because of $guard_name = 'admin' in the model,
        // this will correctly assign the 'admin'-guarded 'super-admin' role.
        $superAdmin->assignRole('super-admin');
    }
}
