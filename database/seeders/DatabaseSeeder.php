<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
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
        // Check if the user exists before creating it
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Seed all data
        $this->call([
            AdminSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ContentSeeder::class,
            ShowroomSeeder::class,
            StoreAndWarehouseSeeder::class,
            SubCategorySeeder::class,
        ]);

        // Assign super-admin role to the first admin user
        $admin = Admin::first();
        if ($admin) {
            $admin->assignRole('super-admin');
        }
    }
}
