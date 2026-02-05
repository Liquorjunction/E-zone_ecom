<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Newsletter\DestroyNewsletterRequest;
use App\Http\Requests\Client\Newsletter\StoreNewsletterRequest;
use App\Repositories\Client\Interfaces\NewsletterRepositoryInterface as NewsletterRepo;


class NewsletterController extends Controller
{
    public function subscribe(StoreNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        $data = $request->validated();
        $newsletterRepo->subscribe($data);
        return response()->json(['message' => 'Subscribed to newsletter successfully.']);
    }

    public function unsubscribe(DestroyNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        $data = $request->validated();
        if ($newsletterRepo->unsubscribe($data)) {
            return response()->json(['message' => 'Unsubscribed from newsletter successfully.']);
        }
        return response()->json(['message' => 'Email not found in newsletter subscriptions.'], 404);
    }
}
