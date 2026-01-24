<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Store\CreateStoreRequest;
use App\Http\Requests\Admin\Store\UpdateStoreRequest;
use App\Http\Requests\Admin\Store\DestroyStoreRequest;
use App\Http\Requests\Admin\Store\ReadStoreRequest;
use App\Http\Requests\Admin\Store\EditStoreRequest;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface as StoreRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
{
    public function index()
    {
        try {
            return view('admin.pages.stores.index');
        } catch (\Exception $e) {
            Log::error('Failed to load stores index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to load stores: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('admin.pages.stores.create');
        } catch (\Exception $e) {
            Log::error('Failed to load store create form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.stores.index')->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    public function store(CreateStoreRequest $request, StoreRepo $storeRepo)
    {
        try {
            $data = $request->validated();

            foreach (['store_name', 'store_footer', 'location'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }

            $storeRepo->createStore($request, $data);

            return redirect()->route('admin.stores.index')->with('success', 'Store created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Failed to create store: ' . $e->getMessage());
        }
    }

    public function edit(EditStoreRequest $request, StoreRepo $storeRepo)
    {
        try {
            $id = $request->validated()['id'];
            $store = $storeRepo->getStoreById($id);

            return view('admin.pages.stores.edit', compact('store'));
        } catch (\Exception $e) {
            Log::error('Failed to load store for editing', [
                'store_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.stores.index')->with('error', 'Failed to load store for editing: ' . $e->getMessage());
        }
    }

    public function update(UpdateStoreRequest $request, StoreRepo $storeRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            foreach (['store_name', 'store_footer', 'location'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }

            $storeRepo->updateStore($id, $request, $data);

            return redirect()->route('admin.stores.index')->with('success', 'Store updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update store', [
                'store_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Failed to update store: ' . $e->getMessage());
        }
    }

    public function show(ReadStoreRequest $request, StoreRepo $storeRepo)
    {
        try {
            $id = $request->validated()['id'];
            $store = $storeRepo->getStoreById($id);

            return view('admin.pages.stores.show', compact('store'));
        } catch (\Exception $e) {
            Log::error('Failed to load store', [
                'store_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.stores.index')->with('error', 'Failed to load store.');
        }
    }

    public function destroy(DestroyStoreRequest $request, StoreRepo $storeRepo)
    {
        try {
            $id = $request->validated()['id'];
            $storeRepo->deleteStore($id);

            return redirect()->route('admin.stores.index')->with('success', 'Store deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete store', [
                'store_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to delete store: ' . $e->getMessage());
        }
    }

    public function toggleStatus(ReadStoreRequest $request, StoreRepo $storeRepo)
    {
        try {
            $id = $request->validated()['id'];
            $storeRepo->updateStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Store status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle store status', [
                'store_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getDataForDataTable(StoreRepo $storeRepo, ReadStoreRequest $request)
    {
        try {
            $stores = $storeRepo->getDataForDataTable();

            return DataTables::of($stores)
                ->addIndexColumn()
                ->addColumn('store_logo', function ($store) {
                    if ($store->store_logo) {
                        return asset('storage/' . $store->store_logo);
                    }
                    return null;
                })
                ->addColumn('store_name', function ($store) {
                    return $store->getTranslation('store_name', 'en');
                })
                ->addColumn('location', function ($store) {
                    return $store->getTranslation('location', 'en');
                })
                ->addColumn('status', function ($store) use ($request) {
                    if ($request->user()->can('store.update')) {
                        $data['toggle_url'] = $request->user()->can('store.update') ? route('admin.stores.toggleStatus', ['id' => $store->id]) : '';
                        $data['status'] = $store->status;
                        $data['id'] = $store->id;
                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $store->status])->render();
                    }
                })
                ->addColumn('action', function ($store) use ($request) {
                    $data['view_url'] = $request->user()->can('store.read') ? route('admin.stores.show', ['id' => $store->id]) : '';
                    $data['edit_url'] = $request->user()->can('store.update') ? route('admin.stores.edit', ['id' => $store->id]) : '';
                    $data['id'] = $store->id;

                    if($request->user()->can('store.delete') && $store->inventories_count > 0){
                        $data['can_delete'] = false;
                    } else {
                        $data['delete_url'] = $request->user()->can('store.delete') ? route('admin.stores.destroy', ['id' => $store->id]) : '';
                    }

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['store_logo', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Failed to load stores datatable', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage(),
            ], 500);
        }
    }
}

