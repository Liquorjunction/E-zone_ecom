<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $privacyPolicy = PrivacyPolicy::all();
        return view('client.pages.privacy', compact('privacyPolicy'));
    }
}
