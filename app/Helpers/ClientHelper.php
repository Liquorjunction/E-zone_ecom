<?php

namespace App\Helpers;

use App\Models\Brand;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ClientHelper
{
    public static function getActiveBrands()
    {
        return Cache::remember('active_brands', 60, function () {
            return Brand::where('status', 'active')->get();
        });
    }

    public static function getBrandBySlug($slug)
    {
        return self::getActiveBrands()->where('slug', $slug)->first();
    }
}
