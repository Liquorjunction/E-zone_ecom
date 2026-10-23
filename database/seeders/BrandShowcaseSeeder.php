<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandBanner;
use App\Models\BrandCategoryShowcase;
use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Database\Seeder;

class BrandShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Backfill Sharp Brand
        $sharp = Brand::where('slug', 'sharp')->first();
        if ($sharp) {
            $sharp->setTranslation('tagline', 'en', 'In step with your future');
            $sharp->setTranslation('tagline', 'fr', 'Au rythme de votre avenir');
            $sharp->setTranslation('tagline', 'ar', 'في تناغم مع مستقبلك');

            $sharpDescEn = 'Since 1915, an attitude of giving sincere thought to the people who use product, harnessing originality and creativity in the pursuit of convenience and quality has been the foundation of Sharp. Being a world-wide developer of innovative products, a range of consumer electronic products are here for your ease and convenience every time.';
            $sharpDescFr = 'Depuis 1915, une attitude consistant à accorder une attention sincère à ceux qui utilisent nos produits, en associant originalité et créativité au service de la praticité et de la qualité, constitue le fondement de Sharp. Développeur mondial de produits innovants, une gamme d\'appareils électroniques grand public est à votre disposition pour vous offrir confort et simplicité à chaque instant.';
            $sharpDescAr = 'منذ عام 1915، كان الموقف المتمثل في إعطاء اهتمام مخلص للأشخاص الذين يستخدمون المنتجات، وتسخير الأصالة والإبداع سعياً وراء الراحة والجودة، هو الأساس لشركة شارب. وبصفتها مطوراً عالمياً للمنتجات المبتكرة، تتوفر مجموعة من الأجهزة الإلكترونية الاستهلاكية لراحتك وتسهيل حياتك في كل وقت.';

            $sharp->setTranslation('description', 'en', $sharpDescEn);
            $sharp->setTranslation('description', 'fr', $sharpDescFr);
            $sharp->setTranslation('description', 'ar', $sharpDescAr);
            $sharp->save();

            // Seed Banners for Sharp
            BrandBanner::where('brand_id', $sharp->id)->delete();
            $banners = [
                [
                    'image' => 'brands/banners/sharp-banner.jpg',
                    'alt' => ['en' => 'Sharp Home Appliances Range', 'fr' => 'Gamme d\'appareils électroménagers Sharp', 'ar' => 'تشكيلة أجهزة شارب المنزلية'],
                    'sort_order' => 1,
                ],
                [
                    'image' => 'brands/banners/sharp-banner-2.jpg',
                    'alt' => ['en' => 'Sharp Smart Entertainment & Living', 'fr' => 'Divertissement intelligent et salon Sharp', 'ar' => 'الترفيه الذكي وغرفة المعيشة من شارب'],
                    'sort_order' => 2,
                ],
                [
                    'image' => 'brands/banners/sharp-banner-3.jpg',
                    'alt' => ['en' => 'Sharp Modern Kitchen Appliances', 'fr' => 'Appareils de cuisine modernes Sharp', 'ar' => 'أجهزة المطبخ العصرية من شارب'],
                    'sort_order' => 3,
                ],
            ];

            foreach ($banners as $b) {
                $banner = new BrandBanner();
                $banner->brand_id = $sharp->id;
                $banner->image = $b['image'];
                $banner->sort_order = $b['sort_order'];
                $banner->status = 'active';
                foreach ($b['alt'] as $loc => $val) {
                    $banner->setTranslation('alt_text', $loc, $val);
                }
                $banner->save();
            }

            // Find categories
            $catWashing = Categories::where('slug', 'washing-machines')->first();
            $catKitchen = Categories::where('slug', 'kitchen-home-appliances')->first();
            $catFridge = Categories::where('slug', 'refrigerator')->first();
            $catTv = Categories::where('slug', 'television-and-audio')->first();
            $catAc = Categories::where('slug', 'air-conditioner')->first();
            $subDishwasher = SubCategories::where('slug', 'dishwasher')->first();
            $subMicrowave = SubCategories::where('slug', 'like', '%microwave%')->first();

            // Seed Showcases for Sharp
            BrandCategoryShowcase::where('brand_id', $sharp->id)->delete();
            $showcases = [
                [
                    'slug' => 'laundry',
                    'category_id' => $catWashing ? $catWashing->id : null,
                    'sub_category_id' => null,
                    'title' => ['en' => 'Laundry', 'fr' => 'Lavage', 'ar' => 'الغسيل'],
                    'description' => [
                        'en' => 'With Sharp Washing Machines, enjoy clothes that are ultra-clean, super-dry & smelling wonderfully fresh every time with intuitive sensor cycles and energy-saving inverter motors.',
                        'fr' => 'Avec les lave-linge Sharp, profitez de vêtements parfaitement propres, ultra-secs et au parfum frais à chaque lavage grâce aux cycles intelligents et moteurs inverter.',
                        'ar' => 'مع غسالات شارب، استمتع بملابس فائقة النظافة وجافة تماماً ومنعشة في كل مرة بفضل دورات الاستشعار الذكية والمحركات الموفرة للطاقة.',
                    ],
                    'image' => 'brands/showcases/sharp-laundry.jpg',
                    'sort_order' => 1,
                ],
                [
                    'slug' => 'dishwashing',
                    'category_id' => $catKitchen ? $catKitchen->id : null,
                    'sub_category_id' => $subDishwasher ? $subDishwasher->id : null,
                    'title' => ['en' => 'Dishwashing', 'fr' => 'Lavage de vaisselle', 'ar' => 'غسيل الأطباق'],
                    'description' => [
                        'en' => 'Let Sharp take the hard work out of doing the dishes with these innovative, energy efficient dishwashers that pride themselves on using less water and ensuring the cleanest kitchenware.',
                        'fr' => 'Laissez Sharp s\'occuper de la vaisselle grâce à ces lave-vaisselle innovants et économes en énergie, conçus pour consommer moins d\'eau et garantir une propreté irréprochable.',
                        'ar' => 'دع شارب تتولى مهمة غسيل الأطباق بفضل غسالات الأطباق المبتكرة والموفرة للطاقة التي تستهلك مياهاً أقل وتضمن نظافة فائقة لأواني مطبخك.',
                    ],
                    'image' => 'brands/showcases/sharp-dishwashing.jpg',
                    'sort_order' => 2,
                ],
                [
                    'slug' => 'refrigerator',
                    'category_id' => $catFridge ? $catFridge->id : null,
                    'sub_category_id' => null,
                    'title' => ['en' => 'Refrigerator', 'fr' => 'Réfrigérateur', 'ar' => 'الثلاجات'],
                    'description' => [
                        'en' => 'Home is having a fridge full of fresh food. Sharp brings a host of innovative ideas to keep vegetables crisp, beverages chilled and energy costs down to keep your family well fed.',
                        'fr' => 'Un foyer chaleureux, c\'est un réfrigérateur plein d\'aliments frais. Sharp innove pour garder vos légumes croquants, vos boissons fraîches et vos factures d\'énergie au plus bas.',
                        'ar' => 'المنزل السعيد هو ثلاجة مليئة بالأطعمة الطازجة. تقدم شارب أفكاراً مبتكرة للحفاظ على نضارة الخضار وبرودة المشروبات مع خفض استهلاك الطاقة لعائلتك.',
                    ],
                    'image' => 'brands/showcases/sharp-refrigerator.jpg',
                    'sort_order' => 3,
                ],
                [
                    'slug' => 'small-home-appliances',
                    'category_id' => $catKitchen ? $catKitchen->id : null,
                    'sub_category_id' => null,
                    'title' => ['en' => 'Small Home Appliances', 'fr' => 'Petits appareils ménagers', 'ar' => 'الأجهزة المنزلية الصغيرة'],
                    'description' => [
                        'en' => 'Sharp home appliances make everyday life easier. From beautifully designed kitchen gadgets and cooking aids to powerful, versatile appliances with premium Japanese engineering.',
                        'fr' => 'Les petits appareils ménagers Sharp facilitent votre quotidien. Des ustensiles de cuisine élégants aux aides culinaires performantes, profitez de l\'ingénierie japonaise de précision.',
                        'ar' => 'تجعل الأجهزة المنزلية الصغيرة من شارب حياتك اليومية أكثر سهولة، بدءاً من أدوات المطبخ الأنيقة وحتى أجهزة الطهي المتطورة بجودة هندسية يابانية متميزة.',
                    ],
                    'image' => 'brands/showcases/sharp-small-home-appliances.jpg',
                    'sort_order' => 4,
                ],
                [
                    'slug' => 'microwave-oven',
                    'category_id' => $catKitchen ? $catKitchen->id : null,
                    'sub_category_id' => $subMicrowave ? $subMicrowave->id : null,
                    'title' => ['en' => 'Microwave Oven', 'fr' => 'Four à micro-ondes', 'ar' => 'أفران الميكروويف'],
                    'description' => [
                        'en' => 'The kitchen is the heart of a home. Give it a strong pulse with the right cooking appliances that let you enjoy delicious, perfectly warmed and prepared meals in minutes.',
                        'fr' => 'La cuisine est le cœur de la maison. Donnez-lui vie avec des appareils de cuisson adaptés qui vous permettent de savourer de délicieux repas en quelques minutes.',
                        'ar' => 'المطبخ هو قلب المنزل. امنحه نبضاً حيوياً مع أجهزة الطهي المناسبة التي تتيح لك إعداد وتسخين وجبات شهية في دقائق معدودة.',
                    ],
                    'image' => 'brands/showcases/sharp-microwave-oven.jpg',
                    'sort_order' => 5,
                ],
                [
                    'slug' => 'televisions',
                    'category_id' => $catTv ? $catTv->id : null,
                    'sub_category_id' => null,
                    'title' => ['en' => 'Televisions', 'fr' => 'Téléviseurs', 'ar' => 'أجهزة التلفزيون'],
                    'description' => [
                        'en' => 'Immerse yourself in stunning 4K and QLED visuals with Sharp Aquos display engineering, intelligent Android TV operating system, and high-fidelity cinematic audio.',
                        'fr' => 'Plongez au cœur d\'images 4K et QLED éblouissantes grâce à la technologie Sharp Aquos, le système Android TV intelligent et une qualité audio cinéma haute fidélité.',
                        'ar' => 'انغمس في صور مذهلة بدقة 4K وQLED بفضل تقنية شاشات شارب أكووس ونظام Android TV الذكي والصوت السينمائي فائق الدقة.',
                    ],
                    'image' => 'brands/showcases/sharp-televisions.jpg',
                    'sort_order' => 6,
                ],
                [
                    'slug' => 'air-conditioners',
                    'category_id' => $catAc ? $catAc->id : null,
                    'sub_category_id' => null,
                    'title' => ['en' => 'Air Conditioners', 'fr' => 'Climatiseurs', 'ar' => 'مكيفات الهواء'],
                    'description' => [
                        'en' => 'Protect your loved ones and keep them comfortable with SHARP Air Conditioners. With Sharp J-Tech inverter technology, save up to 65% energy consumption with rapid cooling.',
                        'fr' => 'Offrez fraîcheur et confort à votre foyer avec les climatiseurs SHARP. Grâce à la technologie Inverter J-Tech, économisez jusqu\'à 65% d\'énergie tout en profitant d\'un rafraîchissement rapide.',
                        'ar' => 'حافظ على راحة وانتعاش أحبائك مع مكيفات شارب. مع تقنية J-Tech Inverter من شارب، وفر حتى 65% من استهلاك الطاقة مع تبريد فائق السرعة.',
                    ],
                    'image' => 'brands/showcases/sharp-air-conditioners.jpg',
                    'sort_order' => 7,
                ],
                [
                    'slug' => 'vacuum-cleaner',
                    'category_id' => $catKitchen ? $catKitchen->id : null,
                    'sub_category_id' => null,
                    'title' => ['en' => 'Vacuum Cleaner', 'fr' => 'Aspirateurs', 'ar' => 'المكانس الكهربائية'],
                    'description' => [
                        'en' => 'Keep your living spaces pristine and allergy-free with Sharp high-power canister and drum vacuum cleaners equipped with advanced multi-filtration and HEPA hygiene technology.',
                        'fr' => 'Gardez votre intérieur propre et sain avec les aspirateurs haute puissance Sharp équipés d\'une filtration avancée et de la technologie hygiénique HEPA.',
                        'ar' => 'حافظ على نظافة منزلك وصحته مع مكانس شارب الكهربائية عالية القوة المزودة بنظام ترشيح متطور وتقنية HEPA الصحية.',
                    ],
                    'image' => 'brands/showcases/sharp-vacuum-cleaner.jpg',
                    'sort_order' => 8,
                ],
            ];

            foreach ($showcases as $sc) {
                $item = new BrandCategoryShowcase();
                $item->brand_id = $sharp->id;
                $item->category_id = $sc['category_id'];
                $item->sub_category_id = $sc['sub_category_id'];
                $item->slug = $sc['slug'];
                $item->image = $sc['image'];
                $item->sort_order = $sc['sort_order'];
                $item->status = 'active';
                foreach ($sc['title'] as $loc => $val) {
                    $item->setTranslation('title', $loc, $val);
                }
                foreach ($sc['description'] as $loc => $val) {
                    $item->setTranslation('description', $loc, $val);
                }
                $item->save();
            }
        }

        // 2. Backfill Roch Brand
        $roch = Brand::where('slug', 'roch')->first();
        if ($roch) {
            $roch->setTranslation('tagline', 'en', 'Reliable & Smart Living Solutions');
            $roch->setTranslation('tagline', 'fr', 'Des solutions fiables et intelligentes pour le quotidien');
            $roch->setTranslation('tagline', 'ar', 'حلول معيشية ذكية وموثوقة');

            $rochDescEn = 'Discover Roch innovative and dependable home appliances, engineered with modern efficiency, exceptional build quality and superior performance designed to elevate everyday life.';
            $rochDescFr = 'Découvrez les appareils électroménagers innovants et fiables de Roch, conçus avec une efficacité moderne, une qualité de fabrication exceptionnelle et des performances supérieures pour sublimer votre quotidien.';
            $rochDescAr = 'اكتشف أجهزة روتش المنزلية المبتكرة والموثوقة، المصممة بكفاءة حديثة وجودة تصنيع استثنائية وأداء متفوق للارتقاء بحياتك اليومية.';

            $roch->setTranslation('description', 'en', $rochDescEn);
            $roch->setTranslation('description', 'fr', $rochDescFr);
            $roch->setTranslation('description', 'ar', $rochDescAr);
            $roch->save();

            // Seed Banners for Roch
            BrandBanner::where('brand_id', $roch->id)->delete();
            $rochBanner = new BrandBanner();
            $rochBanner->brand_id = $roch->id;
            $rochBanner->image = 'sliders/MpBdZfE8kNL3Ei7TgIY0DAYNZeioPd4iWi2McPgn.png';
            $rochBanner->sort_order = 1;
            $rochBanner->status = 'active';
            $rochBanner->setTranslation('alt_text', 'en', 'Roch Air Conditioners');
            $rochBanner->setTranslation('alt_text', 'fr', 'Climatiseurs Roch');
            $rochBanner->setTranslation('alt_text', 'ar', 'مكيفات روتش');
            $rochBanner->save();
        }
    }
}
