<?php

namespace App\Helpers;

use App\Models\Brand;
use Illuminate\Support\Facades\Cache;

class ClientHelper
{
    /**
     * Get all active brands with caching
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveBrands()
    {
        return Cache::remember('active_brands', 60, function () {
            return Brand::where('status', 'active')->get();
        });
    }

    /**
     * Get a brand by slug
     *
     * @param string $slug
     * @return \App\Models\Brand|null
     */
    public static function getBrandBySlug($slug)
    {
        return self::getActiveBrands()->where('slug', $slug)->first();
    }
}
