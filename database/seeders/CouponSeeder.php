<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'name' => 'Welcome Discount',
                'code' => 'WELCOME10',
                'coupon_type' => 'discount',
                'value' => 10.00,
                'min_order_amount' => 50.00,
                'max_discount' => null,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(7),
                'ends_at' => Carbon::now()->addMonths(3),
                'status' => true,
            ],
            [
                'name' => 'Summer Sale 20% Off',
                'code' => 'SUMMER20',
                'coupon_type' => 'discount',
                'value' => 20.00,
                'min_order_amount' => 100.00,
                'max_discount' => 50.00,
                'usage_limit' => 500,
                'usage_limit_per_user' => 3,
                'starts_at' => Carbon::now()->subDays(30),
                'ends_at' => Carbon::now()->addMonths(2),
                'status' => true,
            ],
            [
                'name' => '$15 Off Orders Over $75',
                'code' => 'SAVE15',
                'coupon_type' => 'flat_amount',
                'value' => 15.00,
                'min_order_amount' => 75.00,
                'max_discount' => null,
                'usage_limit' => 200,
                'usage_limit_per_user' => 2,
                'starts_at' => Carbon::now()->subDays(14),
                'ends_at' => Carbon::now()->addMonth(),
                'status' => true,
            ],
            [
                'name' => 'VIP 30% Discount',
                'code' => 'VIP30',
                'coupon_type' => 'discount',
                'value' => 30.00,
                'min_order_amount' => 200.00,
                'max_discount' => 100.00,
                'usage_limit' => 100,
                'usage_limit_per_user' => 5,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addYear(),
                'status' => true,
            ],
            [
                'name' => 'Black Friday Mega Sale',
                'code' => 'BLACKFRIDAY',
                'coupon_type' => 'discount',
                'value' => 50.00,
                'min_order_amount' => 150.00,
                'max_discount' => 200.00,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'starts_at' => Carbon::now()->subDays(5),
                'ends_at' => Carbon::now()->addDays(25),
                'status' => true,
            ],
            [
                'name' => 'Free Shipping Bonus',
                'code' => 'FREESHIP',
                'coupon_type' => 'flat_amount',
                'value' => 10.00,
                'min_order_amount' => 30.00,
                'max_discount' => null,
                'usage_limit' => null, // Unlimited
                'usage_limit_per_user' => null, // Unlimited per user
                'starts_at' => Carbon::now()->subMonth(),
                'ends_at' => Carbon::now()->addMonths(6),
                'status' => true,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::firstOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }

        $this->command->info('Test coupons created successfully!');
    }
}
