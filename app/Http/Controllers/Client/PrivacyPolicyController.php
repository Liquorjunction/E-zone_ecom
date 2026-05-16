<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PrivacyPolicy\ReadPrivacyPolicyRequest;
use App\Repositories\Client\Interfaces\PrivacyPolicyRepositoryInterface as PrivacyPolicyRepo;
use Carbon\Carbon;

class PrivacyPolicyController extends Controller
{
    public function index(ReadPrivacyPolicyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        $privacyPolicy = $privacyPolicyRepo->getActivePrivacyPolicyData();

        $lastUpdated = null;
        if ($privacyPolicy && method_exists($privacyPolicy, 'isNotEmpty') && $privacyPolicy->isNotEmpty()) {
            $last = $privacyPolicy->sortByDesc('updated_at')->first();
            $lastUpdated = $last->updated_at instanceof Carbon ? $last->updated_at : Carbon::parse($last->updated_at);
        }

        return view('client.pages.privacy', compact('privacyPolicy', 'lastUpdated'));
    }
}
