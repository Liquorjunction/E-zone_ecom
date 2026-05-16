<?php

namespace App\Repositories\Client\Repositories;

use App\Models\ContactUs;
use App\Repositories\Client\Interfaces\ContactUsRepositoryInterface;

class ContactUsRepository implements ContactUsRepositoryInterface
{
    public function createContactUs(array $data): ContactUs
    {
        return ContactUs::create($data);
    }

    public function ticketNumberExists(string $ticketNumber): bool
    {
        return ContactUs::withTrashed()->where('ticket_number', $ticketNumber)->exists();
    }
}
