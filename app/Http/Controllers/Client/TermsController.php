<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;

class TermsController extends Controller
{
    public function index()
    {
        try {
            $terms = TermsAndCondition::where('status', 'active')->first();
            return view('client.pages.terms', compact('terms'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch Terms and Conditions.']);
        }
    }
}
