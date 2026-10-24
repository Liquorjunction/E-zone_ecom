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
use Illuminate\Database\QueryException;
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
            foreach (['name', 'tagline', 'description'] as $field) {
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

            if (isset($data['showcases']) && is_array($data['showcases'])) {
                foreach ($data['showcases'] as &$sc) {
                    if (empty(trim((string)($sc['button_text']['en'] ?? '')))) {
                        $sc['button_text'] = ['en' => '', 'fr' => '', 'ar' => ''];
                        $sc['button_url'] = null;
                    } else {
                        if (empty($sc['button_text']['fr'])) {
                            $sc['button_text']['fr'] = gtrans($sc['button_text']['en'], 'fr');
                        }
                        if (empty($sc['button_text']['ar'])) {
                            $sc['button_text']['ar'] = gtrans($sc['button_text']['en'], 'ar');
                        }
                    }

                    foreach (['title', 'description'] as $f) {
                        if (isset($sc[$f]['en']) && !empty($sc[$f]['en'])) {
                            if (empty($sc[$f]['fr'])) {
                                $sc[$f]['fr'] = gtrans($sc[$f]['en'], 'fr');
                            }
                            if (empty($sc[$f]['ar'])) {
                                $sc[$f]['ar'] = gtrans($sc[$f]['en'], 'ar');
                            }
                        }
                    }
                }
            }
            // ----------------------------------

            $brandRepo->createBrand($request, $data);

            return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
        } catch (QueryException $e) {
            Log::error('Brand create database error: ' . $e->getMessage());

            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1062 || str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), 'Duplicate entry')) {
                return redirect()->back()
                    ->withErrors(['name.en' => 'A brand with this name already exists. Please choose a different name.'])
                    ->withInput();
            }

            return redirect()->back()
                ->withErrors(['error' => 'A database error occurred while creating the brand. Please try again.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Brand create error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'An unexpected error occurred while creating the brand. Please try again.'])
                ->withInput();
        }
    }

    public function edit(EditBrandRequest $request)
    {
        $id = $request->validated()['id'];
        $brand = Brand::with([
            'banners',
            'categoryShowcases.category',
            'categoryShowcases.subCategory',
        ])->findOrFail($id);

        return view('admin.pages.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['name', 'tagline', 'description'] as $field) {
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

            if (isset($data['showcases']) && is_array($data['showcases'])) {
                foreach ($data['showcases'] as &$sc) {
                    if (empty(trim((string)($sc['button_text']['en'] ?? '')))) {
                        $sc['button_text'] = ['en' => '', 'fr' => '', 'ar' => ''];
                        $sc['button_url'] = null;
                    } else {
                        if (empty($sc['button_text']['fr'])) {
                            $sc['button_text']['fr'] = gtrans($sc['button_text']['en'], 'fr');
                        }
                        if (empty($sc['button_text']['ar'])) {
                            $sc['button_text']['ar'] = gtrans($sc['button_text']['en'], 'ar');
                        }
                    }

                    foreach (['title', 'description'] as $f) {
                        if (isset($sc[$f]['en']) && !empty($sc[$f]['en'])) {
                            if (empty($sc[$f]['fr'])) {
                                $sc[$f]['fr'] = gtrans($sc[$f]['en'], 'fr');
                            }
                            if (empty($sc[$f]['ar'])) {
                                $sc[$f]['ar'] = gtrans($sc[$f]['en'], 'ar');
                            }
                        }
                    }
                }
            }
            // ----------------------------------

            $brandRepo->updateBrand($request, $id, $data);

            return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
        } catch (QueryException $e) {
            Log::error('Brand update database error: ' . $e->getMessage());

            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1062 || str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), 'Duplicate entry')) {
                return redirect()->back()
                    ->withErrors(['name.en' => 'A brand with this name already exists. Please choose a different name.'])
                    ->withInput();
            }

            return redirect()->back()
                ->withErrors(['error' => 'A database error occurred while updating the brand. Please try again.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Brand update error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'An unexpected error occurred while updating the brand. Please try again.'])
                ->withInput();
        }
    }

    public function destroy(DestroyBrandRequest $request, BrandRepo $brandRepo)
    {
        try {
            $id = $request->validated()['id'];
            $brandRepo->deleteBrand($id);

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting brand',
            ], 500);
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
            $brands = $brandRepo->getDataForDatatable($request->all());

            return DataTables::of($brands)
                ->addIndexColumn()
                ->filterColumn('name', function ($query, $keyword) {
                    $like = "%{$keyword}%";
                    $langs = ['en', 'fr', 'ar'];

                    $query->where(function ($q) use ($like, $langs) {
                        foreach ($langs as $lang) {
                            $q->orWhere("name->$lang", 'LIKE', $like);
                        }
                    });
                })
                ->addColumn('image', function ($brand) {
                    if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                        return '<img src="' . asset('storage/' . $brand->image) . '" width="50" class="rounded">';
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

                    if ($request->user()->can('brands.delete') && isset($brand->products_count) && $brand->products_count == 0) {
                        $data['delete_url'] = route('admin.brands.destroy', ['id' => $brand->id]);
                    } else {
                        $data['delete_url'] = '';
                    }

                    $data['id'] = $brand->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
