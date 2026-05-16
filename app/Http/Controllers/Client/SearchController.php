<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handles the AJAX request from the frontend universal search bar.
     */
    public function ajaxSearch(Request $request, SearchService $searchService)
    {
        $query = $request->input('q');

        // Let the Service handle logic, formatting, and DB calls
        $searchPayload = $searchService->performUniversalSearch($query);

        // Return the JSON response
        return response()->json([
            'status' => 'success',
            'query'  => $query,
            'result' => $searchPayload
        ]);
    }
}
