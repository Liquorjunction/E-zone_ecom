<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Faq\DestroyFaqRequest;
use App\Http\Requests\Admin\Faq\EditFaqRequest;
use App\Http\Requests\Admin\Faq\ReadFaqRequest;
use App\Http\Requests\Admin\Faq\StoreFaqRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Repositories\Admin\Interfaces\FaqRepositoryInterface as FaqRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.pages.faq.index');
    }

    public function create()
    {
        // Fetch categories for selection with ID
        $categories = FaqCategory::all();

        return view('admin.pages.faq.create', compact('categories'));
    }

    public function store(StoreFaqRequest $request, FaqRepo $faqRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['question', 'answer'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                $translateFunc = ($field === 'answer') ? 'gtrans_html' : 'gtrans';

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = $translateFunc($data[$field]['en'], 'fr');
                }

                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = $translateFunc($data[$field]['en'], 'ar');
                }
            }

            $faqRepo->createFaq($request, $data);

            return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditFaqRequest $request)
    {
        $id = $request->input('id');
        $categories = FaqCategory::all();
        $faq = Faq::findOrFail($id);

        return view('admin.pages.faq.edit', compact('faq', 'categories'));
    }

    public function update(UpdateFaqRequest $request, FaqRepo $faqRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['question', 'answer'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }

                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }

            $faqRepo->updateFaq($request, $id, $data);

            return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyFaqRequest $request, FaqRepo $faqRepo)
    {
        try {
            $id = $request->input('id');
            $faqRepo->deleteFaq($id);

            return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateStatus(ReadFaqRequest $request, FaqRepo $faqRepo)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');

            $faqRepo->updateFaqStatus($id, $status);

            return response()->json(['success' => true, 'message' => 'FAQ status updated successfully.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus(ReadFaqRequest $request, FaqRepo $faqRepo)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status'); // Optional, if repo handles automatic toggle, null is fine

            $faqRepo->updateFaqStatus($id, $status);

            return response()->json([
                'success' => true,
                'message' => 'FAQ status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadFaqRequest $request, FaqRepo $faqRepo)
    {
        try {
            $faqs = $faqRepo->getDataForDataTable($request);

            return DataTables::of($faqs)
                ->addIndexColumn()
                ->addColumn('question', function ($faq) {
                    // Helper to show English title in the list
                    return $faq->getTranslation('question', 'en');
                })
                ->addColumn('answer', function ($faq) {
                    // Helper to show English answer in the list, limited to 50 chars
                    return Str::limit(strip_tags($faq->getTranslation('answer', 'en')), 40);
                })
                ->addColumn('category', function ($faq) {
                    return $faq->category ? $faq->category->getTranslation('name', 'en') : 'Uncategorized';
                })
                ->addColumn('status', function ($faq) use ($request) {
                    if ($request->user()->can('faqs.update')) {
                        $data['id'] = $faq->id;
                        $data['toggle_url'] = $request->user()->can('faqs.update') ? route('admin.faqs.toggleStatus', ['id' => $faq->id]) : '';
                        $data['status'] = $faq->status;

                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $faq->status])->render();
                    }
                })
                ->addColumn('action', function ($faq) use ($request) {
                    $data['id'] = $faq->id;
                    $data['edit_url'] = $request->user()->can('faqs.update') ? route('admin.faqs.edit', ['id' => $faq->id]) : '';
                    $data['delete_url'] = $request->user()->can('faqs.delete') ? route('admin.faqs.destroy', ['id' => $faq->id]) : '';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
