<?php

namespace App\Http\Controllers\Client;

use App\Helpers\ClientHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = ClientHelper::getActiveBrands();
        return view('client.pages.allbrands', compact('brands'));
    }
}
