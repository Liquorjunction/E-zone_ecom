<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\AboutUsRepositoryInterface;
use App\Models\AboutUs;

class AboutUsRepository implements AboutUsRepositoryInterface
{
    public function getActiveAboutUsData()
    {
        return AboutUs::where('status', 'active')->get();
    }
}
