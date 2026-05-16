<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Showrooms\ReadDetailShowroomRequest;
use App\Http\Requests\Client\Showrooms\ReadShowroomRequest;
use App\Models\Showrooms;
use App\Repositories\Client\Interfaces\ShowroomRepositoryInterface as ShowroomRepo;

class ShowroomController extends Controller
{
    public function index()
    {
        // Fetch the single active showroom directly
        $showroom = Showrooms::where('status', 'active')
            ->with('images')
            ->first();

        if (!$showroom) {
            abort(404);
        }

        return view('client.pages.showroom-detail', [
            'showroom' => $showroom,
            'images' => $showroom->images ?? collect(),
        ]);
    }

    public function show(ReadDetailShowroomRequest $request, $slug, ShowroomRepo $showroomRepo)
    {
        $showroom = $showroomRepo->getShowroomBySlug($slug);

        if (!$showroom) {
            abort(404);
        }

        return view('client.pages.showroom-detail', [
            'showroom' => $showroom,
            'images' => $showroom->images ?? collect(),
        ]);
    }

    public function details($showroomId, ShowroomRepo $showroomRepo)
    {
        return $showroomRepo->getShowroomDetails($showroomId);
    }
}
