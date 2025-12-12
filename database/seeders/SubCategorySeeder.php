<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define subcategories with their parent category names
        $subCategoriesData = [
            // Electronics subcategories
            'Electronics' => [
                ['sub-category-name' => ['en' => 'Audio Systems', 'ar' => 'أنظمة صوتية'], 'sub-category-icon' => 'icon/audio.png'],
                ['sub-category-name' => ['en' => 'Speakers', 'ar' => 'سماعات'], 'sub-category-icon' => 'icon/speakers.png'],
                ['sub-category-name' => ['en' => 'Headphones', 'ar' => 'سماعات رأس'], 'sub-category-icon' => 'icon/headphones.png'],
            ],
            // Computers & Laptops subcategories
            'Computers & Laptops' => [
                ['sub-category-name' => ['en' => 'Laptops', 'ar' => 'أجهزة لابتوب'], 'sub-category-icon' => 'icon/laptop.png'],
                ['sub-category-name' => ['en' => 'Desktop Computers', 'ar' => 'أجهزة كمبيوتر مكتبية'], 'sub-category-icon' => 'icon/desktop.png'],
                ['sub-category-name' => ['en' => 'All-in-One PCs', 'ar' => 'أجهزة الكل في واحد'], 'sub-category-icon' => 'icon/aio.png'],
                ['sub-category-name' => ['en' => 'Gaming Laptops', 'ar' => 'أجهزة لابتوب للألعاب'], 'sub-category-icon' => 'icon/gaming-laptop.png'],
                ['sub-category-name' => ['en' => 'Computer Components', 'ar' => 'مكونات الكمبيوتر'], 'sub-category-icon' => 'icon/components.png'],
            ],
            // Mobile Phones & Tablets subcategories
            'Mobile Phones & Tablets' => [
                ['sub-category-name' => ['en' => 'Smartphones', 'ar' => 'الهواتف الذكية'], 'sub-category-icon' => 'icon/smartphone.png'],
                ['sub-category-name' => ['en' => 'Tablets', 'ar' => 'الأجهزة اللوحية'], 'sub-category-icon' => 'icon/tablet.png'],
                ['sub-category-name' => ['en' => 'Feature Phones', 'ar' => 'الهواتف العادية'], 'sub-category-icon' => 'icon/feature-phone.png'],
                ['sub-category-name' => ['en' => 'Mobile Accessories', 'ar' => 'إكسسوارات الهاتف'], 'sub-category-icon' => 'icon/mobile-accessories.png'],
            ],
            // Home Appliances subcategories
            'Home Appliances' => [
                ['sub-category-name' => ['en' => 'Refrigerators', 'ar' => 'الثلاجات'], 'sub-category-icon' => 'icon/refrigerator.png'],
                ['sub-category-name' => ['en' => 'Washing Machines', 'ar' => 'الغسالات'], 'sub-category-icon' => 'icon/washing-machine.png'],
                ['sub-category-name' => ['en' => 'Air Conditioners', 'ar' => 'مكيفات الهواء'], 'sub-category-icon' => 'icon/ac.png'],
                ['sub-category-name' => ['en' => 'Vacuum Cleaners', 'ar' => 'المكانس الكهربائية'], 'sub-category-icon' => 'icon/vacuum.png'],
                ['sub-category-name' => ['en' => 'Kitchen Appliances', 'ar' => 'أجهزة المطبخ'], 'sub-category-icon' => 'icon/kitchen.png'],
            ],
            // TV & Audio subcategories
            'TV & Audio' => [
                ['sub-category-name' => ['en' => 'Smart TVs', 'ar' => 'التلفزيونات الذكية'], 'sub-category-icon' => 'icon/smart-tv.png'],
                ['sub-category-name' => ['en' => 'LED TVs', 'ar' => 'تلفزيونات إل إي دي'], 'sub-category-icon' => 'icon/led-tv.png'],
                ['sub-category-name' => ['en' => 'OLED TVs', 'ar' => 'تلفزيونات أو إل إي دي'], 'sub-category-icon' => 'icon/oled-tv.png'],
                ['sub-category-name' => ['en' => 'Home Theater Systems', 'ar' => 'أنظمة السينما المنزلية'], 'sub-category-icon' => 'icon/home-theater.png'],
                ['sub-category-name' => ['en' => 'Soundbars', 'ar' => 'مكبرات الصوت'], 'sub-category-icon' => 'icon/soundbar.png'],
            ],
            // Cameras & Photography subcategories
            'Cameras & Photography' => [
                ['sub-category-name' => ['en' => 'DSLR Cameras', 'ar' => 'كاميرات DSLR'], 'sub-category-icon' => 'icon/dslr.png'],
                ['sub-category-name' => ['en' => 'Mirrorless Cameras', 'ar' => 'الكاميرات بدون مرآة'], 'sub-category-icon' => 'icon/mirrorless.png'],
                ['sub-category-name' => ['en' => 'Action Cameras', 'ar' => 'كاميرات الحركة'], 'sub-category-icon' => 'icon/action-camera.png'],
                ['sub-category-name' => ['en' => 'Camera Lenses', 'ar' => 'عدسات الكاميرا'], 'sub-category-icon' => 'icon/lenses.png'],
                ['sub-category-name' => ['en' => 'Camera Accessories', 'ar' => 'إكسسوارات الكاميرا'], 'sub-category-icon' => 'icon/camera-accessories.png'],
            ],
            // Gaming subcategories
            'Gaming' => [
                ['sub-category-name' => ['en' => 'Gaming Consoles', 'ar' => 'أجهزة الألعاب'], 'sub-category-icon' => 'icon/console.png'],
                ['sub-category-name' => ['en' => 'Gaming Controllers', 'ar' => 'أذرع التحكم'], 'sub-category-icon' => 'icon/controller.png'],
                ['sub-category-name' => ['en' => 'Gaming Accessories', 'ar' => 'إكسسوارات الألعاب'], 'sub-category-icon' => 'icon/gaming-accessories.png'],
                ['sub-category-name' => ['en' => 'VR Headsets', 'ar' => 'نظارات الواقع الافتراضي'], 'sub-category-icon' => 'icon/vr.png'],
            ],
            // Wearables subcategories
            'Wearables' => [
                ['sub-category-name' => ['en' => 'Smart Watches', 'ar' => 'الساعات الذكية'], 'sub-category-icon' => 'icon/smartwatch.png'],
                ['sub-category-name' => ['en' => 'Fitness Trackers', 'ar' => 'أجهزة تتبع اللياقة'], 'sub-category-icon' => 'icon/fitness-tracker.png'],
                ['sub-category-name' => ['en' => 'Smart Glasses', 'ar' => 'النظارات الذكية'], 'sub-category-icon' => 'icon/smart-glasses.png'],
            ],
            // Accessories subcategories
            'Accessories' => [
                ['sub-category-name' => ['en' => 'Cables & Chargers', 'ar' => 'الكابلات والشواحن'], 'sub-category-icon' => 'icon/cables.png'],
                ['sub-category-name' => ['en' => 'Power Banks', 'ar' => 'بطاريات محمولة'], 'sub-category-icon' => 'icon/powerbank.png'],
                ['sub-category-name' => ['en' => 'Cases & Covers', 'ar' => 'الحافظات والأغطية'], 'sub-category-icon' => 'icon/cases.png'],
                ['sub-category-name' => ['en' => 'Screen Protectors', 'ar' => 'واقيات الشاشة'], 'sub-category-icon' => 'icon/screen-protector.png'],
                ['sub-category-name' => ['en' => 'Memory Cards', 'ar' => 'بطاقات الذاكرة'], 'sub-category-icon' => 'icon/memory-card.png'],
            ],
            // Office Equipment subcategories
            'Office Equipment' => [
                ['sub-category-name' => ['en' => 'Printers', 'ar' => 'الطابعات'], 'sub-category-icon' => 'icon/printer.png'],
                ['sub-category-name' => ['en' => 'Scanners', 'ar' => 'الماسحات الضوئية'], 'sub-category-icon' => 'icon/scanner.png'],
                ['sub-category-name' => ['en' => 'Projectors', 'ar' => 'أجهزة العرض'], 'sub-category-icon' => 'icon/projector.png'],
                ['sub-category-name' => ['en' => 'Office Supplies', 'ar' => 'مستلزمات المكتب'], 'sub-category-icon' => 'icon/supplies.png'],
            ],
        ];

        // Create subcategories
        foreach ($subCategoriesData as $categoryName => $subCategories) {
            $category = Categories::where('category-name->en', $categoryName)->first();

            if ($category) {
                foreach ($subCategories as $subCategory) {
                    SubCategories::firstOrCreate(
                        [
                            'category_id' => $category->id,
                            'slug' => \Illuminate\Support\Str::slug($subCategory['sub-category-name']['en'])
                        ],
                        array_merge($subCategory, [
                            'category_id' => $category->id,
                            'status' => 1,
                        ])
                    );
                }
            }
        }

        $this->command->info('Sub-categories seeded successfully!');
    }
}
