<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ContactUs\StoreContactUsRequest;
use App\Services\ContactUsService;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{

    public function index()
    {
        return view('client.pages.contact');
    }

    public function store(StoreContactUsRequest $request, ContactUsService $service)
    {
        try {
            Log::info('ContactUsController@store called', [
                'request' => $request->all(),
                'files' => $request->allFiles(),
            ]);
                $service->submitContactForm($request);
            return response()->json(['status' => true, 'message' => 'Your message has been submitted successfully.An Confirmation email will be sent to you.']);
        } catch (\Exception $e) {
            Log::error('Contact form error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
