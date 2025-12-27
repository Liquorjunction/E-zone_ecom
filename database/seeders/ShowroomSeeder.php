<?php

namespace Database\Seeders;

use App\Models\Showrooms;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShowroomSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $showrooms = [
            [
                'name' => ['en' => 'Riyadh Main Showroom', 'ar' => 'صالة العرض الرئيسية - الرياض'],
                'phone' => '+966 11 234 5678',
                'email' => 'showroom.riyadh@ezone.com',
                'address' => ['en' => 'King Fahd Road', 'ar' => 'طريق الملك فهد'],
                'city' => ['en' => 'Riyadh', 'ar' => 'الرياض'],
                'state' => ['en' => 'Riyadh Region', 'ar' => 'منطقة الرياض'],
                'country' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'],
                'pincode' => '11564',
                'description' => ['en' => 'Our flagship showroom featuring the latest electronics and technology products.', 'ar' => 'صالة العرض الرئيسية لدينا تضم أحدث منتجات الإلكترونيات والتكنولوجيا.'],
                'opening_hours' => [
                    'saturday' => '09:00 AM - 10:00 PM',
                    'sunday' => '09:00 AM - 10:00 PM',
                    'monday' => '09:00 AM - 10:00 PM',
                    'tuesday' => '09:00 AM - 10:00 PM',
                    'wednesday' => '09:00 AM - 10:00 PM',
                    'thursday' => '09:00 AM - 10:00 PM',
                    'friday' => '04:00 PM - 10:00 PM',
                ],
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.8470775827583!2d46.673944215022855!3d24.71348708410714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjTCsDQyJzQ4LjYiTiA0NsKwNDAnMzIuMyJF!5e0!3m2!1sen!2ssa!4v1234567890123!5m2!1sen!2ssa" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'status' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => ['en' => 'Jeddah Electronics Showroom', 'ar' => 'صالة العرض للإلكترونيات - جدة'],
                'phone' => '+966 12 345 6789',
                'email' => 'showroom.jeddah@ezone.com',
                'address' => ['en' => 'Tahlia Street', 'ar' => 'شارع التحلية'],
                'city' => ['en' => 'Jeddah', 'ar' => 'جدة'],
                'state' => ['en' => 'Makkah Region', 'ar' => 'منطقة مكة المكرمة'],
                'country' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'],
                'pincode' => '23442',
                'description' => ['en' => 'Experience cutting-edge technology at our Jeddah showroom.', 'ar' => 'اختبر أحدث التقنيات في صالة العرض لدينا في جدة.'],
                'opening_hours' => [
                    'saturday' => '09:00 AM - 11:00 PM',
                    'sunday' => '09:00 AM - 11:00 PM',
                    'monday' => '09:00 AM - 11:00 PM',
                    'tuesday' => '09:00 AM - 11:00 PM',
                    'wednesday' => '09:00 AM - 11:00 PM',
                    'thursday' => '09:00 AM - 11:00 PM',
                    'friday' => '04:00 PM - 11:00 PM',
                ],
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3710.2821583815387!2d39.16394791501763!3d21.54268798580129!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjHCsDMyJzMzLjciTiAzOcKwMDknNTguMiJF!5e0!3m2!1sen!2ssa!4v1234567890123!5m2!1sen!2ssa" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'status' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => ['en' => 'Dammam Technology Center', 'ar' => 'مركز التكنولوجيا - الدمام'],
                'phone' => '+966 13 456 7890',
                'email' => 'showroom.dammam@ezone.com',
                'address' => ['en' => 'King Abdullah Road', 'ar' => 'طريق الملك عبدالله'],
                'city' => ['en' => 'Dammam', 'ar' => 'الدمام'],
                'state' => ['en' => 'Eastern Region', 'ar' => 'المنطقة الشرقية'],
                'country' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'],
                'pincode' => '32245',
                'description' => ['en' => 'Your destination for premium electronics in the Eastern Province.', 'ar' => 'وجهتك للإلكترونيات الفاخرة في المنطقة الشرقية.'],
                'opening_hours' => [
                    'saturday' => '09:00 AM - 10:00 PM',
                    'sunday' => '09:00 AM - 10:00 PM',
                    'monday' => '09:00 AM - 10:00 PM',
                    'tuesday' => '09:00 AM - 10:00 PM',
                    'wednesday' => '09:00 AM - 10:00 PM',
                    'thursday' => '09:00 AM - 10:00 PM',
                    'friday' => '04:00 PM - 10:00 PM',
                ],
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3574.9284635689377!2d50.20768031504932!3d26.42339898336185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjbCsDI1JzI0LjIiTiA1MMKwMTInMzUuNyJF!5e0!3m2!1sen!2ssa!4v1234567890123!5m2!1sen!2ssa" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'status' => 1,
                'sort_order' => 3,
            ],
        ];

        foreach ($showrooms as $showroom) {
            Showrooms::firstOrCreate(
                ['email' => $showroom['email']],
                $showroom
            );
        }

        $this->command->info('Showrooms seeded successfully!');
    }
}
