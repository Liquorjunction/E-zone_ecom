<?php

namespace App\Repositories\Client\Interfaces;

interface NewsletterRepositoryInterface
{
    public function subscribe($data);
    public function unsubscribe($data);
    public function isSubscribed($email);
}
