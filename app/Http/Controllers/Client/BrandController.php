<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Brand\ReadBrandRequest;
use App\Repositories\Client\Interfaces\BrandRepositoryInterface as BrandRepo;

class BrandController extends Controller
{
    public function index(ReadBrandRequest $request, BrandRepo $brandRepo)
    {
        $brands = $brandRepo->getActiveBrandsData();
        return view('client.pages.allbrands', compact('brands'));
    }
}
