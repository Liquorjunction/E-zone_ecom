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
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TncController extends Controller
{
    public function index()
    {
        $tncCount = TermsAndCondition::count();
        return view('admin.pages.tnc.index', compact('tncCount'));
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
            // ---------------------------------

            $tncRepo->createTnc($request, $data);

            return redirect()->route('admin.tncs.index')->with('success', 'T&C created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditTncRequest $request)
    {
        // Validated ID comes from request (merged via prepareForValidation)
        $id = $request->validated()['id'];
        $tnc = TermsAndCondition::findOrFail($id);

        return view('admin.pages.tnc.edit', compact('tnc'));
    }

    public function update(UpdateTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'content'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                $translateFunc = ($field === 'content') ? 'gtrans_html' : 'gtrans';

                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = $translateFunc($data[$field]['en'], 'fr');
                }

                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = $translateFunc($data[$field]['en'], 'ar');
                }
            }
            // ---------------------------------

            $tncRepo->updateTnc($id, $request, $data);

            return redirect()->route('admin.tncs.index')->with('success', 'T&C updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $id = $request->validated()['id'];
            $tncRepo->deleteTnc($id);

            return redirect()->back()->with('success', 'T&C deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error deleting T&C');
        }
    }

    public function toggleStatus(EditTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $id = $request->validated()['id'];
            $tncRepo->updateTncStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating T&C status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadTncRequest $request, TncRepo $tncRepo)
    {
        try {
            $tncs = $tncRepo->getDataForDataTable();

            return DataTables::of($tncs)
                ->addIndexColumn()
                ->addColumn('title', function ($tnc) {
                    return $tnc->getTranslation('title', 'en');
                })
                ->addColumn('content', function ($tnc) {
                    return \Illuminate\Support\Str::limit(strip_tags($tnc->getTranslation('content', 'en')), 50, '...');
                })
                ->addColumn('updated_at', function ($tnc) {
                    return $tnc->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('status', function ($tnc) use ($request) {
                    if ($request->user()->can('tnc.update')) {
                        $data['toggle_url'] = $request->user()->can('tnc.update') ? route('admin.tncs.toggleStatus', ['id' => $tnc->id]) : '';
                        $data['status'] = $tnc->status;
                        $data['id'] = $tnc->id;

                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $tnc->status])->render();
                    }
                })
                ->addColumn('action', function ($tnc) use ($request) {
                    $data['edit_url'] = $request->user()->can('tnc.update') ? route('admin.tncs.edit', ['id' => $tnc->id]) : '';
                    $data['id'] = $tnc->id; // Ensure ID is passed for delete

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['title', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
