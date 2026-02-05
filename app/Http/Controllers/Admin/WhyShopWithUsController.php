<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WhyShopWithUs\ReadWhyShopWithUs;
use App\Http\Requests\Admin\WhyShopWithUs\EditWhyShopWithUs;
use App\Http\Requests\Admin\WhyShopWithUs\StoreWhyShopWithUs;
use App\Http\Requests\Admin\WhyShopWithUs\UpdateWhyShopWithUs;
use App\Http\Requests\Admin\WhyShopWithUs\DestroyWhyShopWithUs;
use App\Repositories\Admin\Interfaces\WhyShopWithUsRepositoryInterface as WhyShopWithUsRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhyShopWithUsController extends Controller
{
    public function index()
    {
        return view('admin.pages.whyshopwithus.index');
    }

    public function create()
    {
        return view('admin.pages.whyshopwithus.create');
    }

    public function store(StoreWhyShopWithUs $request, WhyShopWithUsRepository $whyShopWithUsRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            $attributes = ['title', 'description'];
            foreach ($attributes as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $whyShopWithUsRepo->createWhyShopWithUs($data);

            return redirect()->route('admin.whyshopwithus.index')->with('success', 'Entry created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create Why Shop With Us entry', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    public function edit(EditWhyShopWithUs $request, WhyShopWithUsRepository $whyShopWithUsRepo)
    {
        try {
            $id = $request->validated()['id'];
            $entry = $whyShopWithUsRepo->getWhyShopWithUsById($id);

            if (!$entry) {
                return redirect()->route('admin.whyshopwithus.index')->with('error', 'Entry not found.');
            }

            return view('admin.pages.whyshopwithus.edit', compact('entry'));
        } catch (\Exception $e) {
            Log::error('Failed to load edit form for Why Shop With Us entry', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to load edit form: ' . $e->getMessage());
        }
    }

    public function update(UpdateWhyShopWithUs $request, WhyShopWithUsRepository $whyShopWithUsRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            $attributes = ['title', 'description'];
            foreach ($attributes as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $updated = $whyShopWithUsRepo->updateWhyShopWithUs($request, $data, $id);

            if (!$updated) {
                return redirect()->back()->with('error', 'Failed to update entry.');
            }

            return redirect()->route('admin.whyshopwithus.index')->with('success', 'Entry updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update Why Shop With Us entry', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function destroy(DestroyWhyShopWithUs $request, WhyShopWithUsRepository $whyShopWithUsRepo)
    {
        try {
            $id = $request->validated()['id'];
            $deleted = $whyShopWithUsRepo->deleteWhyShopWithUs($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Entry deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete Why Shop With Us entry', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete',
            ], 500);
        }
    }

    public function dataTable(ReadWhyShopWithUs $request, WhyShopWithUsRepository $whyShopWithUsRepo)
    {
        $entries = $whyShopWithUsRepo->getDataForDataTable();

        return DataTables::of($entries)
            ->addIndexColumn()
            ->addColumn('title', function ($entry) {
                return $entry->getTranslation('title', 'en');
            })
            ->addColumn('description', function ($entry) {
                $description = Str::limit($entry->getTranslation('description', 'en'), 50, '...');
                return $description;
            })
            ->addColumn('status', function ($entry) use ($request) {
                if ($request->user()->can('why_choose_us.update')) {
                    $data['id'] = $entry->id;
                    $data['toggle_url'] = $request->user()->can('why_choose_us.update') ? route('admin.whyshopwithus.toggleStatus', ['id' => $entry->id]) : '';
                    $data['status'] = $entry->status;
                    return view('admin.partials.datatable-status', $data)->render();
                } else {
                    return view('components.datatable-status-badge', ['status' => $entry->status])->render();
                }
            })
            ->addColumn('actions', function ($entry) use ($request) {
                $data['id'] = $entry->id;
                $data['edit_url'] = $request->user()->can('why_choose_us.update') ? route('admin.whyshopwithus.edit', ['id' => $entry->id]) : '';
                // $data['delete_url'] = $request->user()->can('why_choose_us.delete') ? route('admin.whyshopwithus.destroy', ['id' => $entry->id]) : '';
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['title', 'description', 'status', 'actions'])
            ->make(true);
    }
}
