<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Wishlist\AddWishlistRequest;
use App\Http\Requests\Client\Wishlist\RemoveWishlistRequest;
use App\Http\Requests\Client\Wishlist\GetWishlistRequest;
use App\Http\Requests\Client\Wishlist\CountWishlistRequest;
use App\Http\Requests\Client\Wishlist\ClearWishlistRequest;
use App\Models\Wishlist;
use App\Repositories\Client\Interfaces\WishlistRepositoryInterface as WishlistRepo;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        // Show wishlist page with user's wishlist items
        $user = request()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $userId = $user->id;

        // Resolve repository from container
        $wishlistRepo = app(WishlistRepo::class);
        $wishlist = $wishlistRepo->getWishlist($userId);

        return view('client.pages.dashboard.wishlist', compact('wishlist'));
    }

    public function addToWishlist(AddWishlistRequest $request, WishlistRepo $wishlistRepo)
    {
        $userId = $request->user()->id;
        $productVariantId = $request->input('product_variant_id');

        if ($wishlistRepo->checkWishlist($userId, $productVariantId)) {
            return response()->json(['message' => statictranslations('Product already in wishlist.')], 200);
        }

        $wishlistRepo->addToWishlist($userId, $productVariantId);

        $count = $wishlistRepo->countWishlist($userId);
        return response()->json(['message' => statictranslations('Product added to wishlist.'), 'count' => $count], 201);
    }

    public function removeFromWishlist(RemoveWishlistRequest $request, WishlistRepo $wishlistRepo)
    {
        $userId = $request->user()->id;
        $productVariantId = $request->input('product_variant_id');

        if (! $wishlistRepo->checkWishlist($userId, $productVariantId)) {
            return response()->json(['message' => statictranslations('Product not found in wishlist.')], 404);
        }

        $wishlistRepo->removeFromWishlist($userId, $productVariantId);
        $count = $wishlistRepo->countWishlist($userId);

        return response()->json(['message' => statictranslations('Product removed from wishlist.'), 'count' => $count], 200);
    }

    public function getWishlist(GetWishlistRequest $request, WishlistRepo $wishlistRepo)
    {
        $userId = $request->user()->id;
        $wishlist = $wishlistRepo->getWishlist($userId);

        return response()->json(['wishlist' => $wishlist], 200);
    }

    public function countWishlist(CountWishlistRequest $request, WishlistRepo $wishlistRepo)
    {
        $userId = $request->user()->id;
        $count = $wishlistRepo->countWishlist($userId);

        return response()->json(['count' => $count], 200);
    }

    public function clearWishlist(ClearWishlistRequest $request, WishlistRepo $wishlistRepo)
    {
        $userId = $request->user()->id;
        $wishlistRepo->clearWishlist($userId);

        return response()->json(['message' => statictranslations('Wishlist cleared')], 200);
    }

    public function ajaxCount()
    {
        $count = 0;
        if (Auth::check()) {
            $count = Wishlist::where('user_id', Auth::id())->count();
        }
        return response()->json(['count' => $count]);
    }
}
