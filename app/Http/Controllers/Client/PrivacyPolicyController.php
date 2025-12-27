<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PrivacyPolicy\ReadPrivacyPolicyRequest;
use App\Repositories\Client\Interfaces\PrivacyPolicyRepositoryInterface as PrivacyPolicyRepo;

class PrivacyPolicyController extends Controller
{
    public function index(ReadPrivacyPolicyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        $privacyPolicy = $privacyPolicyRepo->getActivePrivacyPolicyData();
        return view('client.pages.privacy', compact('privacyPolicy'));
    }
}
