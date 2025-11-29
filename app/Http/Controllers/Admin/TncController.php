<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tnc\DestroyTncRequest;
use App\Http\Requests\Admin\Tnc\EditTncRequest;
use App\Http\Requests\Admin\Tnc\ReadTncRequest;
use App\Http\Requests\Admin\Tnc\StoreTncRequest;
use App\Http\Requests\Admin\Tnc\UpdateTncRequest;
use App\Models\TermsAndCondition;
use App\Repositories\Admin\Interfaces\TncRepositoryInterface as TncRepo;
use App\Models\Tnc;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TncController extends Controller
{
    public function index()
    {
        return view('admin.pages.tnc.index');
    }

    public function create()
    {
        return view('admin.pages.tnc.create');
    }

    public function store(StoreTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'content'] as $field) {
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

            $tncRepo->createTnc($request, $data);

            return redirect()->route('admin.tncs.index')->with('success', 'Terms and Conditions created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditTncRequest $request)
    {
        $id = $request->input('id');
        $tnc = TermsAndCondition::findOrFail($id);

        return view('admin.pages.tnc.edit', compact('tnc'));
    }

    public function update(UpdateTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'content'] as $field) {
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

            $tncRepo->updateTnc($request, $id, $data);

            return redirect()->route('admin.tncs.index')->with('success', 'Terms and Conditions updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $id = $request->input('id');
            $tncRepo->deleteTnc($id);

            return redirect()->route('admin.tncs.index')->with('success', 'Terms and Conditions deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function toggleStatus(ReadTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');

            $tncRepo->updateTncStatus($id, $status);

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function data(ReadTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $tncs = $tncRepo->getDataForDataTable($request);

            return DataTables::of($tncs)
                ->addIndexColumn()
                ->addColumn('title', function ($tnc) {
                    // Helper to show English title in the list
                    return $tnc->getTranslation('title', 'en');
                })
                ->addColumn('status', function ($tnc) use ($request) {
                    $data['toggle_url'] = $request->user()->can('tncs.update') ? route('admin.tncs.toggleStatus', ['id' => $tnc->id]) : '';
                    $data['status'] = $tnc->status;

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($tnc) use ($request) {
                    $data['edit_url'] = $request->user()->can('tncs.update') ? route('admin.tncs.edit', ['id' => $tnc->id]) : '';
                    $data['delete_url'] = $request->user()->can('tncs.delete') ? route('admin.tncs.destroy', ['id' => $tnc->id]) : '';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['title', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
