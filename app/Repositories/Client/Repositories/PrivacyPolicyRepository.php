<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\PrivacyPolicyRepositoryInterface;
use App\Models\PrivacyPolicy;

class PrivacyPolicyRepository implements PrivacyPolicyRepositoryInterface
{
    public function getActivePrivacyPolicyData()
    {
        $privacyPolicy = PrivacyPolicy::where('status', 'active')->get();
        return $privacyPolicy;
    }
}
