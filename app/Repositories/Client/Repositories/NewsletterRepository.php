<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Newsletter;
use App\Repositories\Client\Interfaces\NewsletterRepositoryInterface;

class NewsletterRepository implements NewsletterRepositoryInterface
{
    public function subscribe($data)
    {
        return Newsletter::create($data);
    }

    public function unsubscribe($data)
    {
        $newsletter = Newsletter::where('email', $data['email'])->first();
        if ($newsletter) {
            $newsletter->delete();
            return true;
        }
        return false;
    }

    public function isSubscribed($email)
    {
        return Newsletter::where('email', $email)->exists();
    }
}
