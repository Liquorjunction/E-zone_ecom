<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\PrivacyPolicyRepositoryInterface;
use App\Models\PrivacyPolicy;

class PrivacyPolicyRepository implements PrivacyPolicyRepositoryInterface
{
    public function getActivePrivacyPolicyData()
    {
        // Fetch only the privicy policy that are not soft deleted
        return PrivacyPolicy::withoutTrashed()->get();


    }
}
