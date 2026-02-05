<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Newsletter\DestroyNewsletterRequest;
use App\Http\Requests\Admin\Newsletter\EditNewsletterRequest;
use App\Http\Requests\Admin\Newsletter\ReadNewsletterRequest;
use App\Repositories\Admin\Interfaces\NewsletterRepositoryInterface as NewsletterRepo;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.pages.newsletters.index');
    }

    public function toggleStatus(EditNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        try {
            $id = $request->input('id');
            $newsletterRepo->toggleStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Newsletter status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(DestroyNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        try {
            $id = $request->input('id');
            $newsletterRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Newsletter deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete newsletter.',
            ], 500);
        }
    }

    public function data(ReadNewsletterRequest $request, NewsletterRepo $newsletterRepo)
    {
        try {
            $newsletters = $newsletterRepo->getDataForDataTable($request);
            return datatables()->of($newsletters)
                ->addIndexColumn()
                ->addColumn('email', function ($newsletter) {
                    return $newsletter->email;
                })
                ->addColumn('created_at', function ($newsletter) {
                    return $newsletter->created_at;
                })
                ->addColumn('status', function ($newsletter) use ($request) {
                    if ($request->user()->can('newsletter.update')) {
                        $data['id'] = $newsletter->id;
                        $data['status'] = $newsletter->status;
                        $data['toggle_url'] = $request->user()->can('newsletter.update') ? route('admin.newsletter-subscribers.toggleStatus', ['id' => $newsletter->id]) : null;
                        Log::info($data);
                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $newsletter->status])->render();
                    }
                })
                ->addColumn('action', function ($newsletter) use ($request) {
                    $data['id'] = $newsletter->id;
                    $data['delete_url'] = $request->user()->can('newsletter.delete') ? route('admin.newsletter-subscribers.destroy', $newsletter->id) : null;
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
