<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Coupon;
use App\Models\Discount;
use Carbon\Carbon;

class DeactivateExpiredCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deactivate-expired-coupons-and-discounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates expired coupons and discounts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Deactivate expired coupons
        $expiredCoupons = Coupon::where('ends_at', '<', $now)
            ->where(function($q) {
                $q->where('status', true)
                  ->orWhere('status', 'active');
            })
            ->get();

        $couponCount = 0;
        foreach ($expiredCoupons as $coupon) {
            $coupon->status = false;
            $coupon->save();
            $couponCount++;
        }

        // Deactivate expired discounts
        $expiredDiscounts = Discount::where('ends_at', '<', $now)
            ->where('status', true)
            ->get();

        $discountCount = 0;
        foreach ($expiredDiscounts as $discount) {
            $discount->status = false;
            $discount->save();
            $discountCount++;
        }

        $this->info("Deactivated {$couponCount} expired coupon(s) and {$discountCount} expired discount(s).");
    }
}
