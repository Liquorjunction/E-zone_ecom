<?php

namespace App\Repositories\Client\Interfaces;

interface ContactUsRepositoryInterface
{
    public function createContactUs(array $data);
    // ticketNumberExists removed; ticket_number now matches id.
}
