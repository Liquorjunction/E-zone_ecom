<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrivacyPolicy\DestroyPrivacyRequest;
use App\Http\Requests\Admin\PrivacyPolicy\EditPrivacyRequest;
use App\Http\Requests\Admin\PrivacyPolicy\ReadPrivacyRequest;
use App\Http\Requests\Admin\PrivacyPolicy\StorePrivacyRequest;
use App\Http\Requests\Admin\PrivacyPolicy\UpdatePrivacyRequest;
use App\Models\PrivacyPolicy;
use App\Repositories\Admin\Interfaces\PrivacyPolicyRespositoryInterface as PrivacyPolicyRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        return view('admin.pages.privacy-policy.index');
    }

    public function create()
    {
        return view('admin.pages.privacy-policy.create');
    }

    public function edit(EditPrivacyRequest $request)
    {
        $id = $request->input('id');
        $privacyPolicy = PrivacyPolicy::findOrFail($id);

        return view('admin.pages.privacy-policy.edit', compact('privacyPolicy'));
    }

    public function store(StorePrivacyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Fill for 'description' ---
            foreach (['title', 'description'] as $field) {
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }
            // --------------------------------------

            $privacyPolicyRepo->storePrivacyPolicy($request, $data);

            return redirect()->route('admin.privacy-policy.index')->with('success', 'Privacy Policy created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function update(UpdatePrivacyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Fill for Update ---
            foreach (['title', 'description'] as $field) {
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }
            // -----------------------------

            $privacyPolicyRepo->updatePrivacyPolicy($request, $id, $data);

            return redirect()->route('admin.privacy-policy.index')->with('success', 'Privacy Policy updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyPrivacyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        try {
            $id = $request->input('id');
            $privacyPolicyRepo->destroyPrivacyPolicy($id);

            return response()->json([
                'success' => true,
                'message' => 'Privacy Policy deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting Privacy Policy',
            ], 500);
        }
    }

    public function updateStatus(EditPrivacyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        try {
            $id = $request->input('id');
            $privacyPolicyRepo->updatePrivacyPolicyStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Privacy Policy status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadPrivacyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        $privacyPolicies = $privacyPolicyRepo->getDataForDataTable($request->all());

        return DataTables::of($privacyPolicies)
            ->addIndexColumn()
            ->addColumn('title', function ($privacyPolicy) {
                return $privacyPolicy->title;
            })
            ->addColumn('description', function ($privacyPolicy) {
                return Str::limit(strip_tags($privacyPolicy->description), 50, '...');
            })
            ->addColumn('updated_at', function ($privacyPolicy) {
                return $privacyPolicy->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('status', function ($privacyPolicy) use ($request) {
                if ($request->user()->can('privacy_policies.update')) {
                    $data['toggle_url'] = $request->user()->can('privacy_policies.update') ? route('admin.privacy-policy.toggleStatus', ['id' => $privacyPolicy->id]) : null;
                    $data['status'] = $privacyPolicy->status;
                    $data['id'] = $privacyPolicy->id;

                    return view('admin.partials.datatable-status', $data)->render();
                } else {
                    return view('components.datatable-status-badge', ['status' => $privacyPolicy->status])->render();
                }
            })
            ->addColumn('action', function ($privacyPolicy) use ($request) {
                $data['delete_url'] = $request->user()->can('privacy_policies.delete') ? route('admin.privacy-policy.destroy', ['id' => $privacyPolicy->id]) : '';
                $data['edit_url'] = $request->user()->can('privacy_policies.update') ? route('admin.privacy-policy.edit', ['id' => $privacyPolicy->id]) : '';
                $data['id'] = $privacyPolicy->id;

                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['description', 'updated_at', 'status', 'action'])
            ->make(true);
    }
}
