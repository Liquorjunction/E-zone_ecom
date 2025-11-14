<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed admin data
        $this->call([
            AdminSeeder::class,
        ]);

        // Assign super-admin role to the first admin user
        $admin = admin::first();
        if ($admin) {
            $admin->assignRole('super-admin');
        }
    }
}
