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
            if (isset($data['description']['en'])) {
                if (empty($data['description']['fr'])) {
                    $data['description']['fr'] = gtrans($data['description']['en'], 'fr');
                }
                if (empty($data['description']['ar'])) {
                    $data['description']['ar'] = gtrans($data['description']['en'], 'ar');
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
            if (isset($data['description']['en'])) {
                if (empty($data['description']['fr'])) {
                    $data['description']['fr'] = gtrans($data['description']['en'], 'fr');
                }
                if (empty($data['description']['ar'])) {
                    $data['description']['ar'] = gtrans($data['description']['en'], 'ar');
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

            return redirect()->back()->with(['success' => true, 'message' => 'Privacy Policy deleted successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function data(ReadPrivacyRequest $request, PrivacyPolicyRepo $privacyPolicyRepo)
    {
        $privacyPolicies = $privacyPolicyRepo->getDataForDataTable($request->all());

        return DataTables::of($privacyPolicies)
            ->addIndexColumn()
            ->addColumn('action', function ($privacyPolicy) {
                $data['edit_url'] = route('admin.privacy-policy.edit', ['id' => $privacyPolicy->id]);
                $data['id'] = $privacyPolicy->id;

                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
