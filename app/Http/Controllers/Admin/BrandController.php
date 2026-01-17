<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Brands\StoreBrandRequest;
use App\Http\Requests\Admin\Brands\UpdateBrandRequest;
use App\Http\Requests\Admin\Brands\DestroyBrandRequest;
use App\Http\Requests\Admin\Brands\ReadBrandRequest;
use App\Http\Requests\Admin\Brands\EditBrandRequest;
use App\Repositories\Admin\Interfaces\BrandRepositoryInterface as BrandRepo;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.pages.brands.index');
    }

    public function create()
    {
        return view('admin.pages.brands.create');
    }

    public function store(StoreBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['name'] as $field) {
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
            // ----------------------------------

            $brandRepo->createBrand($request, $data);

            return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditBrandRequest $request)
    {
        $id = $request->validated()['id'];
        $brand = Brand::findOrFail($id);

        return view('admin.pages.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['name'] as $field) {
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
            // ----------------------------------

            $brandRepo->updateBrand($request, $id, $data);

            return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $id = $request->validated()['id'];
            $brandRepo->deleteBrand($id);

            return redirect()->back()->with('success', 'Brand deleted successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'Error deleting brand');
        }
    }

    public function toggleStatus(EditBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $id = $request->validated()['id'];
            $brandRepo->updateBrandStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating brand status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $brands = $brandRepo->getDataForDatatable();

            return DataTables::of($brands)
                ->addIndexColumn()
                ->addColumn('image', function ($brand) {
                    if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                        return '<img src="' . asset('storage/'.$brand->image) . '" width="50" class="rounded">';
                    }
                    return '';
                })
                ->addColumn('name', function ($brand) {
                    return $brand->getTranslation('name', 'en');
                })
                ->addColumn('status', function ($brand) use ($request) {
                    if ($request->user()->can('brands.update')) {
                        $data['toggle_url'] = $request->user()->can('brands.update') ? route('admin.brands.toggleStatus', ['id' => $brand->id]) : '';
                        $data['status'] = $brand->status;
                        $data['id'] = $brand->id;
                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $brand->status])->render();
                    }
                })
                ->addColumn('action', function ($brand) use ($request) {
                    $data['edit_url'] = $request->user()->can('brands.update') ? route('admin.brands.edit', ['id' => $brand->id]) : '';
                    $data['delete_url'] = $request->user()->can('brands.delete') ? route('admin.brands.destroy', ['id' => $brand->id]) : '';
                    $data['id'] = $brand->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['image', 'name', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
