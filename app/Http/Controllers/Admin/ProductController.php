<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\DestroyProductRequest;
use App\Http\Requests\Admin\Product\EditProductRequest;
use App\Http\Requests\Admin\Product\ReadProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Services\AutoTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $translationService;

    public function __construct(AutoTranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function index()
    {
        return view('admin.pages.product.index');
    }

    public function create()
    {
        $brands = Brand::where('status', 'active')->get();
        $categories = Categories::where('status', 'active')->get();
        $subCategories = SubCategories::where('status', 'active')->get();

        return view('admin.pages.product.create', compact('brands', 'categories', 'subCategories'));
    }

    public function store(StoreProductRequest $request, ProductRepo $productRepo)
    {
        DB::beginTransaction();
        try {
            // 1. Get validated data
            $data = $request->validated();

            // 2. Delegate translation logic to Service
            $data = $this->translationService->prepareProductData($data);

            // 3. Delegate creation to Repository
            $productRepo->createProduct($request, $data);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with(['success' => true, 'message' => 'Product created successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.store: '.$e->getMessage());

            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(EditProductRequest $request)
    {
        try {
            // 1. Get ID from Query String (?id=1)
            $id = $request->input('id');

            // 2. Fetch Product with Relations
            $product = Product::with(['productVariants', 'images'])->findOrFail($id);

            // 3. Fetch Dropdowns
            $brands = Brand::where('status', 'active')->get();
            $categories = Categories::where('status', 'active')->get();

            // Only fetch subcategories relevant to the parent category
            $subCategories = SubCategories::where('status', 'active')
                ->where('category_id', $product->category_id)
                ->get();

            return view('admin.pages.product.edit', compact('product', 'brands', 'categories', 'subCategories'));

        } catch (\Exception $e) {
            Log::error('ProductController.edit: '.$e->getMessage());

            return redirect()->route('admin.products.index')->withErrors(['error' => 'Product not found']);
        }
    }

    /**
     * Update the specified resource in storage.
     * Route: PUT /admin/products/update (ID is in the form body)
     */
    public function update(UpdateProductRequest $request, ProductRepo $productRepo)
    {
        DB::beginTransaction();
        try {
            // 1. Validated Data
            $data = $request->validated();

            // 2. Extract ID from the request body (as per your route logic)
            $id = $data['id'];

            // 3. Auto-Translate Service (Fills gaps in translations)
            $data = $this->translationService->prepareProductData($data);

            // 4. Update via Repository
            $productRepo->updateProduct($id, $request, $data);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with(['success' => true, 'message' => 'Product updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.update: '.$e->getMessage());

            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $product = Product::findOrFail($id);

        return view('admin.pages.product.show', compact('product'));
    }

    public function toggleStatus(EditProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $id = $request->input('id');
            $productRepo->updateProductStatus($id);

            return response()->json(['success' => true, 'message' => 'Product status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(DestroyProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $id = $request->validated()['id'];
            $productRepo->deleteProduct($id);

            return redirect()->back()->with('success', 'Record deleted successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'Failed to delete record');
        }
    }

    public function dataTable(ReadProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $products = $productRepo->getDataforDataTable($request);

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('image', function ($product) {
                    if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                        return '<img src="'.asset('storage/'.$product->cover_image).'" alt="Product Image" class="img-thumbnail" width="60" height="60">';
                    }

                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('name', function ($product) {
                    return $product->getTranslation('name', 'en');
                })
                ->addColumn('status', function ($product) use ($request) {
                    $data['toggle_url'] = $request->user()->can('products.update') ? route('admin.products.toggleStatus', ['id' => $product->id]) : '';
                    $data['status'] = $product->status;
                    $data['id'] = $product->id;

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($product) use ($request) {
                    $data['view_url'] = $request->user()->can('products.read') ? route('admin.products.show', ['id' => $product->id]) : '';
                    $data['edit_url'] = $request->user()->can('products.update') ? route('admin.products.edit', ['id' => $product->id]) : '';
                    $data['delete_url'] = $request->user()->can('products.delete') ? route('admin.products.destroy') : '';
                    $data['product_id'] = $product->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['image', 'name', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('ProductController.dataTable: '.$e->getMessage());
            throw $e;
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            // We no longer pass the repo/service here; they are resolved inside the job.
            Excel::import(new ProductsImport, $request->file('file'));

            return redirect()->route('admin.products.index')
                ->with(['success' => true, 'message' => 'Import started! Products are being processed in the background.']);

        } catch (\Exception $e) {
            Log::error('Import Error: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Import failed to start: '.$e->getMessage()]);
        }
    }

public function downloadTemplate()
    {
        // Headers use NAMES now, not IDs
        $headers = [
            'name_en',
            'description_en',
            'brand',        // Changed from brand_id
            'category',     // Changed from category_id
            'sub_category', // Changed from sub_category_id
            'price',
            'warranty_en',
            'dimensions_en',
            'spec_1_title', 'spec_1_value',
            'spec_2_title', 'spec_2_value'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Sample Row with NAMES
            fputcsv($file, [
                'Galaxy S24 Ultra',
                'AI Phone with titanium frame.',
                'Samsung',         // Example Brand Name (Must exist in DB)
                'Electronics',     // Example Category Name (Must exist in DB)
                'Mobile Phones',   // Example SubCategory Name (Must exist in DB)
                '1200.00',
                '1 Year',
                '6.8 inch',
                'Processor', 'Snapdragon 8 Gen 3',
                'Camera', '200MP'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=products_import_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
