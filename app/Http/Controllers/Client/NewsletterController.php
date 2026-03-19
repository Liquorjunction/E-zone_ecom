<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Newsletter\DestroyNewsletterRequest;
use App\Http\Requests\Client\Newsletter\StoreNewsletterRequest;
use App\Repositories\Client\Interfaces\NewsletterRepositoryInterface as NewsletterRepo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function subscribe(StoreNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        try {
            $data = $request->validated();
            $newsletter = $newsletterRepo->subscribe($data);

            return response()->json([
                'message' => statictranslations('Subscribed to newsletter successfully.'),
                'success' => true
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => statictranslations('Database error. Please try again.'),
                'error' => config('app.debug') ? $e->getMessage() : 'Database error'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => statictranslations('An error occurred while subscribing.'),
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    public function unsubscribe(DestroyNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        $data = $request->validated();
        if ($newsletterRepo->unsubscribe($data)) {
            return response()->json(['message' => statictranslations('Unsubscribed from newsletter successfully.')]);
        }
        return response()->json(['message' => statictranslations('Email not found in newsletter subscriptions.')], 404);
    }
}
