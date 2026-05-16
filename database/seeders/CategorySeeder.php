<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category-name' => ['en' => 'Electronics', 'ar' => 'الإلكترونيات'],
                'category-icon' => 'icon/electronics.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Computers & Laptops', 'ar' => 'أجهزة الكمبيوتر والمحمول'],
                'category-icon' => 'icon/computers.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Mobile Phones & Tablets', 'ar' => 'الهواتف المحمولة والأجهزة اللوحية'],
                'category-icon' => 'icon/mobile.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Home Appliances', 'ar' => 'الأجهزة المنزلية'],
                'category-icon' => 'icon/appliances.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'TV & Audio', 'ar' => 'التلفزيون والصوتيات'],
                'category-icon' => 'icon/tv-audio.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Cameras & Photography', 'ar' => 'الكاميرات والتصوير'],
                'category-icon' => 'icon/camera.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Gaming', 'ar' => 'الألعاب'],
                'category-icon' => 'icon/gaming.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Wearables', 'ar' => 'الأجهزة القابلة للارتداء'],
                'category-icon' => 'icon/wearables.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Accessories', 'ar' => 'الإكسسوارات'],
                'category-icon' => 'icon/accessories.png',
                'status' => 1,
            ],
            [
                'category-name' => ['en' => 'Office Equipment', 'ar' => 'معدات المكتب'],
                'category-icon' => 'icon/office.png',
                'status' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Categories::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($category['category-name']['en'])],
                $category
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
