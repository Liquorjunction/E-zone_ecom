<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\DestroyProductRequest;
use App\Http\Requests\Admin\Product\EditProductRequest;
use App\Http\Requests\Admin\Product\ReadProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.pages.product.index');
    }

    public function create()
    {
        return view('admin.pages.product.create');
    }

    public function store(StoreProductRequest $request, ProductRepo $productRepo)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---

            // Translatable fields grouped by model (Product and ProductVariant)
            $translatableFields = [
                'name',
                'description',
                'dimensions',
                'specifications',
                'warranty',
                'variant_name',
                'attributes',
            ];

            foreach ($translatableFields as $field) {
                if (! isset($data[$field]['en'])) {
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

            $productRepo->createProduct($request, $data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.store: '.$e->getMessage());
            throw $e;
        }
    }

    public function show()
    {
        return view('admin.pages.product.show');
    }

    public function edit(EditProductRequest $request)
    {
        try {
            $id = $request->input('id');
            $product = Product::findOrFail($id);

            return view('admin.pages.product.edit', compact('product'));
        } catch (\Exception $e) {
            Log::error('ProductController.edit: '.$e->getMessage());
            throw $e;
        }
    }

    public function destroy(DestroyProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $id = $request->input('id');
            $productRepo->deleteProduct($id);

            return redirect()->back()->with(['success' => true, 'message' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dataTable(ReadProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $products = $productRepo->getDataforDataTable($request);
            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('image', function ($product) {
                    return $product->cover_image;
                })
                ->addColumn('name', function ($product) {
                    return $product->getTranslation('name', 'en');
                })
                ->addColumn('status', function ($product) use ($request) {
                    $data['toggle_url'] = $request->user()->can('products.update') ? route('admin.products.toggleStatus', ['id' => $product->id]) : '';
                    $data['status'] = $product->status;
                    $data['id'] = $product->id; // Ensure ID is passed for toggle

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($product) use ($request) {
                    $data['delete_url'] = $request->user()->can('products.delete') ? route('admin.products.destroy', ['id' => $product->id]) : '';
                    $data['edit_url'] = $request->user()->can('products.update') ? route('admin.products.edit', ['id' => $product->id]) : '';
                    $data['id'] = $product->id; // Ensure ID is passed for delete

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['image', 'name', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('ProductController.dataTable: '.$e->getMessage());
            throw $e;
        }
    }
}
