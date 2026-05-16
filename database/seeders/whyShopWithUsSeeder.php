<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WhyShopWithUs;

class WhyShopWithUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $whyShopWithUsEntries = [
            [
                'icon' => 'icons/quality.png',
                'title' => [
                    'en' => 'Quality Products',
                    'ar' => 'منتجات ذات جودة',
                    'fr' => 'Produits de qualité',
                ],
                'description' => [
                    'en' => 'We offer only the best quality products sourced from trusted manufacturers.',
                    'ar' => 'نحن نقدم فقط أفضل المنتجات ذات الجودة العالية من الشركات المصنعة الموثوقة.',
                    'fr' => 'Nous n\'offrons que les meilleurs produits de qualité provenant de fabricants de confiance.',
                ],
                'order' => 1,
                'status' => 1,
            ],
            [
                'icon' => 'icons/support.png',
                'title' => [
                    'en' => '24/7 Customer Support',
                    'ar' => 'دعم العملاء على مدار الساعة',
                    'fr' => 'Support client 24/7',
                ],
                'description' => [
                    'en' => 'Our dedicated support team is available around the clock to assist you with any inquiries.',
                    'ar' => 'فريق الدعم المخصص لدينا متاح على مدار الساعة لمساعدتك في أي استفسارات.',
                    'fr' => 'Notre équipe de support dédiée est disponible 24h/24 pour vous aider avec toutes vos questions.',
                ],
                'order' => 2,
                'status' => 1,
            ],
            [
                'icon' => 'icons/shipping.png',
                'title' => [
                    'en' => 'Fast & Free Shipping',
                    'ar' => 'شحن سريع ومجاني',
                    'fr' => 'Livraison rapide et gratuite',
                ],
                'description' => [
                    'en' => 'Fast delivery options and free shipping on eligible orders.',
                    'ar' => 'خيارات توصيل سريعة وشحن مجاني على الطلبات المؤهلة.',
                    'fr' => 'Options de livraison rapides et livraison gratuite sur les commandes éligibles.',
                ],
                'order' => 3,
                'status' => 1,
            ],
            [
                'icon' => 'icons/prices.png',
                'title' => [
                    'en' => 'Best Prices',
                    'ar' => 'أفضل الأسعار',
                    'fr' => 'Meilleurs prix',
                ],
                'description' => [
                    'en' => 'Competitive pricing and regular promotions across our catalog.',
                    'ar' => 'أسعار تنافسية وعروض ترويجية منتظمة عبر الكتالوج الخاص بنا.',
                    'fr' => 'Des prix compétitifs et des promotions régulières dans notre catalogue.',
                ],
                'order' => 4,
                'status' => 1,
            ],
        ];

        foreach ($whyShopWithUsEntries as $entry) {
            WhyShopWithUs::create($entry);
        }
    }
}
