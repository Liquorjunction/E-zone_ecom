<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuickStat\DestroyQuickStatRequest;
use App\Http\Requests\Admin\QuickStat\EditQuickStatRequest;
use App\Http\Requests\Admin\QuickStat\ReadQuickStatRequest;
use App\Http\Requests\Admin\QuickStat\StoreQuickStatRequest;
use App\Http\Requests\Admin\QuickStat\UpdateQuickStatRequest;
use App\Models\QuickStat;
use App\Repositories\Admin\Interfaces\QuickStatRepositoryInterface as QuickStatRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class QuickStatController extends Controller
{
    public function index()
    {
        return view('admin.pages.quick-stats.index');
    }

    public function create()
    {
        return view('admin.pages.quick-stats.create');
    }

    public function store(StoreQuickStatRequest $request, QuickStatRepo $quickStatRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['text1', 'text2'] as $field) {
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

            $quickStatRepo->createQuickStat($request, $data);

            return redirect()->route('admin.quick-stats.index')->with('success', 'Quick Stat created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating Quick Stat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the Quick Stat.');
        }
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');
        $quickStat = QuickStat::find($id);
        return view('admin.pages.quick-stats.edit', compact('quickStat'));
    }

    public function update(UpdateQuickStatRequest $request, QuickStatRepo $quickStatRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['text1', 'text2'] as $field) {
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

            $quickStatRepo->updateQuickStat($request, $id, $data);

            return redirect()->route('admin.quick-stats.index')->with('success', 'Quick Stat updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating Quick Stat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the Quick Stat.');
        }
    }

    public function destroy(DestroyQuickStatRequest $request, QuickStatRepo $quickStatRepo)
    {
        try {
            $id = $request->input('id');
            $quickStatRepo->deleteQuickStat($id);

            return response()->json([
                'success' => true,
                'message' => 'Quick Stat deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting Quick Stat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the Quick Stat.',
            ], 500);
        }
    }

    public function updateStatus(EditQuickStatRequest $request, QuickStatRepo $quickStatRepo)
    {
        try {
            $id = $request->input('id');
            $quickStatRepo->updateQuickStatStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Quick Stat status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating Quick Stat status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadQuickStatRequest $request, QuickStatRepo $quickStatRepo)
    {
        $quickStats = $quickStatRepo->getAllQuickStats();

        return DataTables::of($quickStats)
            ->addIndexColumn()
            ->addColumn('icon', function ($quickStat) {
                if ($quickStat->icon && Storage::disk('public')->exists($quickStat->icon)) {
                    return asset('storage/' . $quickStat->icon);
                }
                return null;
            })
            ->addColumn('text', function ($quickStat) {
                try {
                    $text1 = is_array($quickStat->text1) ? ($quickStat->text1['en'] ?? '') : $quickStat->text1;
                    $text2 = is_array($quickStat->text2) ? ($quickStat->text2['en'] ?? '') : $quickStat->text2;
                    return $text1 . ' ' . $text2;
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('status', function ($quickStat) use ($request) {
                if ($request->user()->can('quick_stats.update')) {
                    $data['toggle_url'] = $request->user()->can('quick_stats.update') ? route('admin.quick-stats.updateStatus', ['id' => $quickStat->id]) : null;
                    $data['status'] = $quickStat->status;
                    $data['id'] = $quickStat->id;

                    return view('admin.partials.datatable-status', $data)->render();
                } else {
                    return view('components.datatable-status-badge', ['status' => $quickStat->status])->render();
                }
            })
            ->addColumn('actions', function ($quickStat) use ($request) {
                $data['id'] = $quickStat->id;
                $data['edit_url'] = $request->user()->can('quick_stats.update') ? route('admin.quick-stats.edit', ['id' => $quickStat->id]) : null;
                $data['delete_url'] = $request->user()->can('quick_stats.delete') ? route('admin.quick-stats.destroy', ['id' => $quickStat->id]) : null;
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }
}
