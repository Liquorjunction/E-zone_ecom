<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\BrandRepositoryInterface;
use App\Models\Brand;

class BrandRepository implements BrandRepositoryInterface
{
    public function getActiveBrandsData()
    {
        return Brand::where('status', 'active')->get();
    }
}
