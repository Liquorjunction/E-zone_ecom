<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AboutUs\ReadAboutUsRequest;
use App\Repositories\Client\Interfaces\AboutUsRepositoryInterface as AboutUsRepo;
use Carbon\Carbon;

class AboutUsController extends Controller
{
    public function index(ReadAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $aboutUs = $aboutUsRepo->getActiveAboutUsData();

            // determine latest updated timestamp from the returned collection
            $lastUpdated = null;
            if ($aboutUs && method_exists($aboutUs, 'isNotEmpty') && $aboutUs->isNotEmpty()) {
                $last = $aboutUs->sortByDesc('updated_at')->first();
                $lastUpdated = $last->updated_at instanceof Carbon ? $last->updated_at : Carbon::parse($last->updated_at);
            }

            return view('client.pages.about', compact('aboutUs', 'lastUpdated'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch About Us.']);
        }
    }
}
