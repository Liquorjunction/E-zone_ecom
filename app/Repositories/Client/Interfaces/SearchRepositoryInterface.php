<?php

namespace App\Repositories\Client\Interfaces;

interface SearchRepositoryInterface
{
    public function search($query);
    public function searchUserOrders($query, $userId);
}
