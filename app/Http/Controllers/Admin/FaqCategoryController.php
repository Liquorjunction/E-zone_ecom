<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqCategory\DestroyFaqCategoryRequest;
use App\Http\Requests\Admin\FaqCategory\EditFaqCategoryRequest;
use App\Http\Requests\Admin\FaqCategory\ReadFaqCategoryRequest;
use App\Http\Requests\Admin\FaqCategory\StoreFaqCategoryRequest;
use App\Http\Requests\Admin\FaqCategory\UpdateFaqCategoryRequest;
use App\Models\FaqCategory;
use App\Repositories\Admin\Interfaces\FaqCategoryRepositoryInterface as FaqCategoryRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class FaqCategoryController extends Controller
{
    public function index()
    {
        return view('admin.pages.faqcategory.index');
    }

    public function create()
    {
        return view('admin.pages.faqcategory.create');
    }

    public function store(StoreFaqCategoryRequest $request, FaqCategoryRepo $faqCategoryRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['name', 'description'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------
            $faqCategoryRepo->createFaqCategory($request, $data);

            return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditFaqCategoryRequest $request)
    {
        $id = $request->input('id');
        $faqCategory = FaqCategory::findOrFail($id);

        return view('admin.pages.faqcategory.edit', compact('faqCategory'));
    }

    public function update(UpdateFaqCategoryRequest $request, FaqCategoryRepo $faqCategoryRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['name', 'description'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------
            $faqCategoryRepo->updateFaqCategory($request, $id, $data);

            return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyFaqCategoryRequest $request, FaqCategoryRepo $faqCategoryRepo)
    {
        $id = $request->input('id');
        $faqCategoryRepo->deleteFaqCategory($id);

        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category deleted successfully.');
    }

    public function toggleStatus(EditFaqCategoryRequest $request, FaqCategoryRepo $faqCategoryRepo)
    {
        try {
            $id = $request->input('id'); // Get ID from the request body/query
            $faqCategoryRepo->updateFaqCategoryStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'FAQ Category status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadFaqCategoryRequest $request, FaqCategoryRepo $faqCategoryRepo)
    {
        try {
            $faqCategories = $faqCategoryRepo->getDataforDataTable($request->all());

            return DataTables::of($faqCategories)
                ->addIndexColumn()
                ->addColumn('name', function ($faqCategory) {
                    // Helper to show English title in the list
                    return $faqCategory->getTranslation('name', 'en');
                })
                ->addColumn('description', function ($faqCategory) {
                    // Helper to show English description in the list
                    return $faqCategory->getTranslation('description', 'en');
                })
                ->addColumn('status', function ($faqCategory) use ($request) {
                    $data['toggle_url'] = $request->user()->can('faqs.update') ? route('admin.faq-categories.toggleStatus', ['id' => $faqCategory->id]) : '';
                    $data['status'] = $faqCategory->status;

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($faqCategory) use ($request) {
                    $data['edit_url'] = $request->user()->can('faqs.update') ? route('admin.faq-categories.edit', ['id' => $faqCategory->id]) : '';
                    $data['delete_url'] = $request->user()->can('faqs.delete') ? route('admin.faq-categories.destroy', ['id' => $faqCategory->id]) : '';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in AdminFaqCategoryController::data '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
