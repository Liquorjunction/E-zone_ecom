<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Showrooms;
use App\Repositories\Client\Interfaces\ShowroomRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ShowroomRepository implements ShowroomRepositoryInterface
{
    public function getShowroomDetails($showroomId)
    {
        return Showrooms::query()
            ->where('id', $showroomId)
            ->where('status', 'active')
            ->with('images')
            ->first();
    }

    public function getShowroomById($showroomId)
    {
        return Showrooms::query()
            ->where('id', $showroomId)
            ->where('status', 'active')
            ->first();
    }

    public function getShowroomBySlug($slug)
    {
        return Showrooms::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with('images')
            ->first();
    }

    public function getAllShowrooms(Request $request, int $perPage = 10)
    {
        $locale = app()->getLocale();
        $query = Showrooms::query()
            ->where('status', 'active');

        if ($request->filled('search')) {
            $keyword = $request->get('search');
            $like = '%' . $keyword . '%';

            $query->where(function (Builder $q) use ($like, $locale) {
                $q->where("name->$locale", 'LIKE', $like)
                    ->orWhere('name', 'LIKE', $like)
                    ->orWhere('city', 'LIKE', $like)
                    ->orWhere('state', 'LIKE', $like)
                    ->orWhere('country', 'LIKE', $like)
                    ->orWhere('address', 'LIKE', $like)
                    ->orWhere('pincode', 'LIKE', $like);
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->get('country'));
        }

        return $query
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getShowroomImages($showroomId)
    {
        $showroom = Showrooms::find($showroomId);
        return $showroom ? $showroom->cover_image : null;
    }

    public function getAvailableCountries(): Collection
    {
        return Showrooms::query()
            ->where('status', 'active')
            ->pluck('country')
            ->filter()
            ->unique()
            ->values();
    }

}
