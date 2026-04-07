<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Reviews\ApproveReviewRequest;
use App\Http\Requests\Admin\Reviews\DestroyReviewRequest;
use App\Http\Requests\Admin\Reviews\ReadReviewRequest;
use App\Repositories\Admin\Interfaces\ReviewRepositoryInterface as ReviewRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.pages.reviews.index');
    }

    public function show(ReadReviewRequest $request, ReviewRepo $reviewRepo)
    {
        try {
            $review = $reviewRepo->getReviewById($request->input('id'));
            return view('admin.pages.reviews.show', compact('review'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->route('admin.reviews.index')->with('error', 'Review not found.');
        }
    }

    public function readReview(ReadReviewRequest $request, ReviewRepo $reviewRepo)
    {
        return $reviewRepo->getAllReviews($request->input('id'));
    }

    public function approve(ApproveReviewRequest $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $reviewRepo->approveReview($id);
            return response()->json(['message' => 'Review approved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to approve review.'], 500);
        }
    }

    public function reject(ApproveReviewRequest $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $reviewRepo->rejectReview($id);
            return response()->json(['message' => 'Review rejected successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to reject review.'], 500);
        }
    }

    public function destroy(DestroyReviewRequest $request, ReviewRepo $reviewRepo)
    {
        try {
            $id = $request->input('id');
            $reviewRepo->deleteReview($id);
            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review.'
            ], 500);
        }
    }

    public function getStatusList(ReviewRepo $reviewRepo)
    {
        try {
            $statuses = $reviewRepo->getStatusList();
            return response()->json(['statuses' => $statuses]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch review statuses.'], 500);
        }
    }

    public function getRatingsList(ReviewRepo $reviewRepo)
    {
        try {
            $ratings = $reviewRepo->getRatingList();
            return response()->json(['ratings' => $ratings]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch review ratings.'], 500);
        }
    }

    public function data(ReviewRepo $reviewRepo, ReadReviewRequest $request)
    {
        try {
            $review = $reviewRepo->getDataForDataTable($request->all());

            return DataTables::of($review)
            ->addIndexColumn()
            ->filterColumn('product', function ($query, $keyword) {
                $query->whereHas('product', function ($q) use ($keyword) {
                    $q->where('name->en', 'like', "%$keyword%")
                      ->orWhere('name->ar', 'like', "%$keyword%");
                })->orWhereHas('productVariant', function ($q) use ($keyword) {
                    $q->where('variant_name->en', 'like', "%$keyword%")
                      ->orWhere('variant_name->ar', 'like', "%$keyword%");
                });
            })
            ->addColumn('product', function ($review) {
                if (!$review->product) {
                    return 'N/A';
                }
                $name = $review->product->getTranslation('name', 'en');
                if ($review->productVariant) {
                    $variant = $review->productVariant->getTranslation('variant_name', 'en');
                    return $name . ($variant ? ' - ' . $variant : '');
                }
                return $name;
            })
            ->addColumn('user', function ($review) {
                return $review->user ? $review->user->name : ($review->reviewer_name ?? 'N/A');
            })
            ->addColumn('order', function ($review) {
                return $review->order ? $review->order->order_number : 'N/A';
            })
            ->addColumn('rating', function ($review) {
                return view('admin.partials.datatable-ratings', ['rating' => $review->rating])->render();
            })
            ->addColumn('review_text', function ($review) {
                return Str::limit($review->review_text, 50);
            })
            ->addColumn('created_at', function ($review) {
                return $review->created_at->format('d M Y');
            })
            ->addColumn('approval_actions', function ($review) {
                $data['id'] = $review->id;
                $data['is_approved'] = $review->is_approved;
                $data['approve_route'] = auth('admin')->user()->can('reviews.update') ? route('admin.reviews.approve') : null;
                $data['reject_route'] = auth('admin')->user()->can('reviews.update') ? route('admin.reviews.reject') : null;
                return view('admin.partials.datatable-reviewActions', $data)->render();
            })
            ->addColumn('actions', function ($review) {
                $data['id'] = $review->id;
                $data['view_url'] = auth('admin')->user()->can('reviews.read') ? route('admin.reviews.show', ['id' => $review->id]) : '';
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['approval_actions', 'rating', 'actions'])
            ->make(true);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
