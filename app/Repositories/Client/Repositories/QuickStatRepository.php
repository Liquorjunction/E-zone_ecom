<?php

namespace App\Repositories\Client\Repositories;

use App\Models\QuickStat;
use App\Repositories\Client\Interfaces\QuickStatRepositoryInterface;

class QuickStatRepository implements QuickStatRepositoryInterface
{
    public function getActiveQuickStats()
    {
        return QuickStat::where('status', 'active')->get();
    }
}

