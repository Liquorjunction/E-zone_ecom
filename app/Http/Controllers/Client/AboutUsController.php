<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AboutUs\ReadAboutUsRequest;
use App\Repositories\Client\Interfaces\AboutUsRepositoryInterface as AboutUsRepo;

class AboutUsController extends Controller
{
    public function index(ReadAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $aboutUs = $aboutUsRepo->getActiveAboutUsData();
            return view('client.pages.about', compact('aboutUs'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch About Us.']);
        }
    }
}
