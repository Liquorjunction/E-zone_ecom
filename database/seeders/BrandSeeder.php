<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => ['en' => 'Samsung', 'ar' => 'سامسونج'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Apple', 'ar' => 'أبل'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Sony', 'ar' => 'سوني'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'LG', 'ar' => 'إل جي'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Dell', 'ar' => 'ديل'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'HP', 'ar' => 'إتش بي'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Lenovo', 'ar' => 'لينوفو'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Asus', 'ar' => 'أسوس'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Microsoft', 'ar' => 'مايكروسوفت'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Canon', 'ar' => 'كانون'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Nikon', 'ar' => 'نيكون'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Panasonic', 'ar' => 'باناسونيك'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Philips', 'ar' => 'فيليبس'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Bose', 'ar' => 'بوز'],
                'status' => 1,
            ],
            [
                'name' => ['en' => 'JBL', 'ar' => 'جيه بي إل'],
                'status' => 1,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($brand['name']['en'])],
                $brand
            );
        }

        $this->command->info('Brands seeded successfully!');
    }
}
