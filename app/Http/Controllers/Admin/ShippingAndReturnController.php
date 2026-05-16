<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingAndReturns\DestroyShippingAndReturnRequest;
use App\Http\Requests\Admin\ShippingAndReturns\EditShippingAndReturnRequest;
use App\Http\Requests\Admin\ShippingAndReturns\ReadShippingAndReturnRequest;
use App\Http\Requests\Admin\ShippingAndReturns\StoreShippingAndReturnRequest;
use App\Http\Requests\Admin\ShippingAndReturns\UpdateShippingAndReturnRequest;
use App\Models\ShippingAndReturns;
use App\Repositories\Admin\Interfaces\ShippingAndReturnRepositoryInterface as ShippingRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShippingAndReturnController extends Controller
{
    /**
     * Show the Index Page
     */
    public function index()
    {
        $shippingReturnCount = ShippingAndReturns::count();
        return view('admin.pages.shipping-return.index', compact('shippingReturnCount'));
    }

    /**
     * Show the Create Page
     */
    public function create()
    {
        return view('admin.pages.shipping-return.create');
    }

    /**
     * Store a new Shipping & Return record
     */
    public function store(StoreShippingAndReturnRequest $request, ShippingRepo $shippingRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            // Automatically fills empty French/Arabic fields using English input
            foreach (['title', 'content'] as $field) {
                if (!isset($data[$field]['en'])) {
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
            // ---------------------------------

            // Pass the modified data (with translations) to the repository
            $shippingRepo->createShippingAndReturn($request, $data);

            return redirect()->route('admin.shipping-return.index')
                ->with('success', 'Shipping & Return Policy created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating Shipping & Return: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the Edit Page
     */
    public function edit(EditShippingAndReturnRequest $request)
    {
        // ID is guaranteed by the Request's prepareForValidation method
        $id = $request->validated()['id'];
        $shippingAndReturn = ShippingAndReturns::findOrFail($id);

        return view('admin.pages.shipping-return.edit', compact('shippingAndReturn'));
    }

    /**
     * Update an existing record
     */
    public function update(UpdateShippingAndReturnRequest $request, ShippingRepo $shippingRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic for Update ---
            // Only auto-translates if the user leaves the FR/AR fields empty
            foreach (['title', 'content'] as $field) {
                if (!isset($data[$field]['en'])) {
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
            // --------------------------------------------

            $shippingRepo->updateShippingAndReturn($id, $request, $data);

            return redirect()->route('admin.shipping-return.index')
                ->with('success', 'Shipping & Return Policy updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating Shipping & Return: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete a record
     */
    public function destroy(DestroyShippingAndReturnRequest $request, ShippingRepo $shippingRepo)
    {
        try {
            $id = $request->validated()['id'];
            $shippingRepo->deleteShippingAndReturn($id);

            return response()->json([
                'success' => true,
                'message' => 'Shipping & Return Policy deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting Shipping & Return Policy',
            ], 500);
        }
    }

    /**
     * Toggle Status (Active/Inactive)
     */
    public function status(EditShippingAndReturnRequest $request, ShippingRepo $shippingRepo)
    {
        try {
            $id = $request->validated()['id'];
            $shippingRepo->updateStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DataTables API Endpoint
     */
    public function data(ReadShippingAndReturnRequest $request, ShippingRepo $shippingRepo)
    {
        try {
            $records = $shippingRepo->getDataForDataTable();

            return DataTables::of($records)
                ->addIndexColumn()
                ->addColumn('title', function ($record) {
                    return $record->getTranslation('title', 'en');
                })
                ->addColumn('content', function ($record) {
                    return Str::limit(strip_tags($record->getTranslation('content', 'en')), 50, '...');
                })
                ->addColumn('updated_at', function ($record) {
                    return $record->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('status', function ($record) use ($request) {
                    if ($request->user()->can('shipping_return.update')) {
                        $data['toggle_url'] = $request->user()->can('shipping_return.update')
                            ? route('admin.shipping-return.toggleStatus', ['id' => $record->id])
                            : '';
                        $data['status'] = $record->status;
                        $data['id'] = $record->id;

                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $record->status])->render();
                    }
                })
                ->addColumn('action', function ($record) use ($request) {
                    $data['edit_url'] = $request->user()->can('shipping_return.update')
                        ? route('admin.shipping-return.edit', ['id' => $record->id])
                        : '';
                    $data['id'] = $record->id;

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
