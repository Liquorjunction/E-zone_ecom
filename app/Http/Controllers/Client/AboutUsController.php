<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index()
    {
        try {
            $aboutus = AboutUs::all();
            return view('client.pages.about', compact('aboutus'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch About Us.']);
        }
    }
}
