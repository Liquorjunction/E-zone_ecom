<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndCondition;
use App\Models\ShippingAndReturns;
use App\Models\FaqCategory;
use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // About Us
        AboutUs::firstOrCreate(
            ['id' => 1],
            [
                'title' => ['en' => 'About E-Zone', 'ar' => 'حول إي زون'],
                'description' => ['en' => '<h2>Welcome to E-Zone</h2><p>Your trusted destination for all electronics and technology needs. We offer a wide range of products from leading brands with competitive prices and excellent customer service.</p><h3>Our Mission</h3><p>To provide the best electronics and technology products to our customers.</p><h3>Our Vision</h3><p>To be the leading electronics retailer in the region.</p>', 'ar' => '<h2>مرحبا بكم في إي زون</h2><p>وجهتك الموثوقة لجميع احتياجات الإلكترونيات والتكنولوجيا. نحن نقدم مجموعة واسعة من المنتجات من العلامات التجارية الرائدة بأسعار تنافسية وخدمة عملاء ممتازة.</p>'],
                'status' => 1,
            ]
        );

        // Privacy Policy
        PrivacyPolicy::firstOrCreate(
            ['id' => 1],
            [
                'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
                'description' => ['en' => '<h2>Privacy Policy</h2><p>This privacy policy outlines how we collect, use, and protect your personal information. We are committed to ensuring that your privacy is protected.</p><h3>Information We Collect</h3><p>We may collect personal information including name, email address, phone number, and shipping address when you make a purchase or register on our website.</p><h3>How We Use Your Information</h3><p>We use your information to process orders, improve our services, and communicate with you about your purchases and our products.</p>', 'ar' => '<h2>سياسة الخصوصية</h2><p>توضح سياسة الخصوصية هذه كيفية جمع معلوماتك الشخصية واستخدامها وحمايتها.</p>'],
            ]
        );

        // Terms and Conditions
        TermsAndCondition::firstOrCreate(
            ['id' => 1],
            [
                'title' => ['en' => 'Terms and Conditions', 'ar' => 'الشروط والأحكام'],
                'content' => ['en' => '<h2>Terms and Conditions</h2><p>By accessing and using this website, you agree to comply with these terms and conditions. Please read them carefully.</p><h3>Use of Website</h3><p>You may use our website for lawful purposes only. You must not use our website in any way that causes damage to the website or impairs its availability.</p><h3>Product Information</h3><p>We strive to ensure that all product information is accurate, but we do not warrant that descriptions or other content is error-free.</p>', 'ar' => '<h2>الشروط والأحكام</h2><p>باستخدام هذا الموقع، فإنك توافق على الالتزام بهذه الشروط والأحكام.</p>'],
                'status' => 1,
            ]
        );

        // Shipping and Returns
        ShippingAndReturns::firstOrCreate(
            ['id' => 1],
            [
                'title' => ['en' => 'Shipping and Returns Policy', 'ar' => 'سياسة الشحن والإرجاع'],
                'content' => ['en' => '<h2>Shipping and Returns</h2><h3>Shipping Policy</h3><p>We offer free shipping on orders above 500 SAR. Standard delivery takes 2-5 business days depending on your location.</p><h3>Returns Policy</h3><p>Returns are accepted within 14 days of delivery for unused products in original packaging. Please contact our customer service to initiate a return.</p><h3>Refunds</h3><p>Refunds will be processed within 7-10 business days after we receive the returned item.</p>', 'ar' => '<h2>الشحن والإرجاع</h2><p>نقدم شحن مجاني للطلبات التي تزيد عن 500 ريال سعودي. يتم قبول المرتجعات خلال 14 يومًا من التسليم.</p>'],
                'status' => 1,
            ]
        );

        // FAQ Categories
        $faqCategories = [
            ['name' => ['en' => 'General', 'ar' => 'عام'], 'status' => 1],
            ['name' => ['en' => 'Orders & Payments', 'ar' => 'الطلبات والدفع'], 'status' => 1],
            ['name' => ['en' => 'Shipping & Delivery', 'ar' => 'الشحن والتوصيل'], 'status' => 1],
            ['name' => ['en' => 'Returns & Refunds', 'ar' => 'المرتجعات والاسترداد'], 'status' => 1],
            ['name' => ['en' => 'Technical Support', 'ar' => 'الدعم الفني'], 'status' => 1],
        ];

        foreach ($faqCategories as $category) {
            FaqCategory::firstOrCreate(
                ['name->en' => $category['name']['en']],
                $category
            );
        }

        // FAQs
        $faqs = [
            [
                'category_name_en' => 'General',
                'question' => ['en' => 'What is E-Zone?', 'ar' => 'ما هو إي زون؟'],
                'answer' => ['en' => 'E-Zone is a leading electronics retailer offering a wide range of products from top brands.', 'ar' => 'إي زون هو بائع تجزئة رائد للإلكترونيات يقدم مجموعة واسعة من المنتجات من أفضل العلامات التجارية.'],
            ],
            [
                'category_name_en' => 'General',
                'question' => ['en' => 'Where are you located?', 'ar' => 'أين تقع متاجركم؟'],
                'answer' => ['en' => 'We have stores in Riyadh, Jeddah, and Dammam, Saudi Arabia.', 'ar' => 'لدينا متاجر في الرياض وجدة والدمام بالمملكة العربية السعودية.'],
            ],
            [
                'category_name_en' => 'Orders & Payments',
                'question' => ['en' => 'What payment methods do you accept?', 'ar' => 'ما هي طرق الدفع التي تقبلونها؟'],
                'answer' => ['en' => 'We accept credit cards, debit cards, and cash on delivery.', 'ar' => 'نقبل بطاقات الائتمان والخصم والدفع عند الاستلام.'],
            ],
            [
                'category_name_en' => 'Orders & Payments',
                'question' => ['en' => 'How can I track my order?', 'ar' => 'كيف يمكنني تتبع طلبي؟'],
                'answer' => ['en' => 'You can track your order using the tracking number sent to your email.', 'ar' => 'يمكنك تتبع طلبك باستخدام رقم التتبع المرسل إلى بريدك الإلكتروني.'],
            ],
            [
                'category_name_en' => 'Shipping & Delivery',
                'question' => ['en' => 'Do you offer free shipping?', 'ar' => 'هل تقدمون شحن مجاني؟'],
                'answer' => ['en' => 'Yes, we offer free shipping on orders above 500 SAR.', 'ar' => 'نعم، نقدم شحن مجاني للطلبات التي تزيد عن 500 ريال سعودي.'],
            ],
            [
                'category_name_en' => 'Shipping & Delivery',
                'question' => ['en' => 'How long does delivery take?', 'ar' => 'كم من الوقت يستغرق التوصيل؟'],
                'answer' => ['en' => 'Delivery typically takes 2-5 business days depending on your location.', 'ar' => 'يستغرق التوصيل عادة من 2 إلى 5 أيام عمل حسب موقعك.'],
            ],
            [
                'category_name_en' => 'Returns & Refunds',
                'question' => ['en' => 'What is your return policy?', 'ar' => 'ما هي سياسة الإرجاع الخاصة بكم؟'],
                'answer' => ['en' => 'We accept returns within 14 days of delivery for unused products in original packaging.', 'ar' => 'نقبل المرتجعات خلال 14 يومًا من التسليم للمنتجات غير المستخدمة في العبوة الأصلية.'],
            ],
            [
                'category_name_en' => 'Returns & Refunds',
                'question' => ['en' => 'How long does it take to process a refund?', 'ar' => 'كم من الوقت يستغرق معالجة الاسترداد؟'],
                'answer' => ['en' => 'Refunds are typically processed within 7-10 business days.', 'ar' => 'عادة ما تتم معالجة المبالغ المستردة خلال 7-10 أيام عمل.'],
            ],
            [
                'category_name_en' => 'Technical Support',
                'question' => ['en' => 'Do you provide warranty on products?', 'ar' => 'هل تقدمون ضمان على المنتجات؟'],
                'answer' => ['en' => 'Yes, all our products come with manufacturer warranty.', 'ar' => 'نعم، جميع منتجاتنا تأتي مع ضمان الشركة المصنعة.'],
            ],
            [
                'category_name_en' => 'Technical Support',
                'question' => ['en' => 'How can I contact technical support?', 'ar' => 'كيف يمكنني الاتصال بالدعم الفني؟'],
                'answer' => ['en' => 'You can reach our technical support team via email or phone during business hours.', 'ar' => 'يمكنك الوصول إلى فريق الدعم الفني عبر البريد الإلكتروني أو الهاتف خلال ساعات العمل.'],
            ],
        ];

        foreach ($faqs as $faqData) {
            $category = FaqCategory::where('name->en', $faqData['category_name_en'])->first();
            if ($category) {
                Faq::firstOrCreate(
                    [
                        'faq_category_id' => $category->id,
                        'question->en' => $faqData['question']['en']
                    ],
                    [
                        'faq_category_id' => $category->id,
                        'question' => $faqData['question'],
                        'answer' => $faqData['answer'],
                        'status' => 1,
                    ]
                );
            }
        }

        $this->command->info('Content seeded successfully!');
    }
}
