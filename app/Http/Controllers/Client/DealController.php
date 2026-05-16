<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\DealRepositoryInterface as DealRepo;

class DealController extends Controller
{
    public function index(DealRepo $dealRepo)
    {
        $deals = $dealRepo->getDealVariants(request());
        return view('client.pages.deals', compact('deals'));
    }
}
