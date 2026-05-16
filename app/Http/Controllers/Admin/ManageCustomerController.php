<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageCustomer\BatchDestroyCustomerRequest;
use App\Http\Requests\Admin\ManageCustomer\BatchUpdateStatusCustomerRequest;
use App\Http\Requests\Admin\ManageCustomer\DestroyCustomerRequest;
use App\Http\Requests\Admin\ManageCustomer\EditCustomerRequest;
use App\Http\Requests\Admin\ManageCustomer\ReadCustomerRequest;
use App\Http\Requests\Admin\ManageCustomer\UpdateCustomerRequest;
use App\Repositories\Admin\Interfaces\ManageCustomerRepositoryInterface as ManageCustomerRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ManageCustomerController extends Controller
{
    public function index()
    {
        return view('admin.pages.manage-customers.index');
    }

    public function show(ReadCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $id = $request->input('id');
            $customer = $customerRepo->getCustomerById($id);

            return view('admin.pages.manage-customers.show', compact('customer'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(EditCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $id = $request->input('id');
            $customer = $customerRepo->getCustomerById($id);

            return view('admin.pages.manage-customers.edit', compact('customer'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();
            $customerRepo->updateCustomer($request, $id, $data);

            Log::info('Customer Updated: ID ' . $id);
            return redirect()->route('admin.customers.index')->with(['success', 'message' => 'Customer updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Customer Update Error: ' . $e->getMessage());
            return redirect()->back()->with(['failure', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(DestroyCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $id = $request->input('id');
            $customerRepo->deleteCustomer($id);

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete',
            ], 500);
        }
    }

    public function batchDestroy(BatchDestroyCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $ids = $request->input('ids', []);
            $customerRepo->batchDelete($ids);

            return response()->json([
                'success' => true,
                'message' => 'Selected customers deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
        $query = $customerRepo->getDataForDataTable($request->all());

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                })
                ->addColumn('batch_checkbox', function ($customer) {
                    return '<input type="checkbox" class="customer-checkbox" value="' . $customer->id . '">';
                })
                ->addColumn('name', function ($customer) {
                    return $customer->name;
                })
                ->addColumn('email', function ($customer) {
                    return $customer->email;
                })
                ->addColumn('last_login_at', function ($customer) {
                    return $customer->last_login_at ? $customer->last_login_at->format('M d, Y H:i') : 'Never';
                })
                ->addColumn('phone', function ($customer) {
                    return $customer->phone;
                })
                ->addColumn('created_at', function ($customer) {
                    return $customer->created_at ? $customer->created_at->format('M d, Y H:i') : '';
                })
                ->addColumn('action', function ($customer) use ($request) {
                    $data['edit_url'] = $request->user()->can('manageCustomers.update') ? route('admin.customers.edit', ['id' => $customer->id]) : '';
                    $data['view_url'] = $request->user()->can('manageCustomers.read') ? route('admin.customers.show', ['id' => $customer->id]) : '';
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->addColumn('status', function ($customer) use ($request) {
                    if ($request->user()->can('manageCustomers.update')) {
                        $data['id'] = $customer->id;
                        $data['toggle_url'] = $request->user()->can('manageCustomers.update') ? route('admin.customers.toggleStatus', ['id' => $customer->id]) : '';
                        $data['status'] = $customer->is_active;
                        return view('admin.partials.sliderdatatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $customer->is_active])->render();
                    }
                })
                ->rawColumns(['batch_checkbox', 'name', 'email', 'last_login_at', 'phone', 'created_at', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTable Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load data.'], 500);
        }
    }

    public function updateCustomerStatus(ReadCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $id = $request->input('id');
            $customerRepo->updateCustomerStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Status Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function batchUpdateCustomerStatus(BatchUpdateStatusCustomerRequest $request, ManageCustomerRepo $customerRepo)
    {
        try {
            $ids = $request->input('ids', []);
            $status = $request->input('status');
            $customerRepo->batchUpdateStatus($ids, $status);

            return response()->json([
                'success' => true,
                'message' => 'Selected customers status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Batch Status Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
