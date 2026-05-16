<?php

namespace App\Repositories\Client\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface ShowroomRepositoryInterface
{
    public function getShowroomDetails($showroomId);
    public function getShowroomById($showroomId);
    public function getShowroomBySlug($slug);
    public function getAllShowrooms(Request $request, int $perPage = 10);
    public function getShowroomImages($showroomId);
    public function getAvailableCountries(): Collection;
}
