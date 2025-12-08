<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\DestroyProductRequest;
use App\Http\Requests\Admin\Product\EditProductRequest;
use App\Http\Requests\Admin\Product\ReadProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Jobs\ProcessProductImport; // Import the Job
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Services\AutoTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Import Cache
use Illuminate\Support\Str; // Import Str
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
            $data = $request->validated();
            $data = $this->translationService->prepareProductData($data);
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
            $id = $request->input('id');
            $product = Product::with(['productVariants', 'images'])->findOrFail($id);
            $brands = Brand::where('status', 'active')->get();
            $categories = Categories::where('status', 'active')->get();
            $subCategories = SubCategories::where('status', 'active')
                ->where('category_id', $product->category_id)
                ->get();

            return view('admin.pages.product.edit', compact('product', 'brands', 'categories', 'subCategories'));

        } catch (\Exception $e) {
            Log::error('ProductController.edit: '.$e->getMessage());

            return redirect()->route('admin.products.index')->withErrors(['error' => 'Product not found']);
        }
    }

    public function update(UpdateProductRequest $request, ProductRepo $productRepo)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $id = $data['id'];
            $data = $this->translationService->prepareProductData($data);
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

    // --- IMPORT FUNCTIONALITY ---

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240',
        ]);

        try {
            // 1. Store file in a permanent location for the Queue Job to access
            // Using 'local' disk to ensure it's accessible by CLI PHP
            $file = $request->file('file');
            $filename = 'import_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $relativePath = $file->storeAs('temp_imports', $filename, 'local');
            $fullPath = storage_path('app/private/'.$relativePath);

            // Adjust path if your 'local' disk config is different
            if (! file_exists($fullPath)) {
                $fullPath = Storage::disk('local')->path($relativePath);
            }

            // 2. Generate a unique Batch ID to track progress
            $batchId = Str::random(10);

            // 3. Dispatch the Job
            ProcessProductImport::dispatch($fullPath, $batchId);

            // 4. Return JSON for the frontend polling
            return response()->json([
                'success' => true,
                'batch_id' => $batchId,
                'message' => 'File uploaded. Processing started in background.',
            ]);

        } catch (\Exception $e) {
            Log::error('Import Start Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to start import: '.$e->getMessage()], 500);
        }
    }

    public function importProgress($batchId)
    {
        // Retrieve progress from Cache
        $progress = Cache::get('import_progress_'.$batchId);

        if (! $progress) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // If completed, we can clear the cache (optional, or let it expire)
        // if ($progress['status'] === 'completed') { Cache::forget('import_progress_' . $batchId); }

        // Flash session for the popup if complete
        if ($progress['status'] === 'completed') {
            session()->flash('import_status', [
                'type' => 'import_summary',
                'success' => $progress['success'],
                'failed' => $progress['failed'],
                'errors' => $progress['errors'],
            ]);
        }

        return response()->json($progress);
    }

    public function downloadTemplate()
    {
        // 1. Basic Headers
        $headers = [
            'name_en',
            'description_en',
            'brand',
            'category',
            'sub_category',
            'price',
            'warranty_en',
            'dimensions_en',
        ];

        // 2. Add Specification Headers (1-5 for template simplicity, Job supports 20)
        for ($i = 1; $i <= 5; $i++) {
            $headers[] = "spec_{$i}_title";
            $headers[] = "spec_{$i}_value";
        }

        // 3. Add Variant Headers (1-3 for template simplicity, Job supports 20)
        for ($i = 1; $i <= 3; $i++) {
            $headers[] = "variant_{$i}_name_en";
            $headers[] = "variant_{$i}_price";
        }

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Sample Row data
            $row = [
                'Galaxy S24 Ultra',
                'AI Phone with titanium frame.',
                'Samsung',
                'Electronics',
                'Mobile Phones',
                '1200.00',
                '1 Year',
                '6.8 inch',
                // Specs
                'Processor', 'Snapdragon 8 Gen 3',
                'Camera', '200MP',
                'Battery', '5000mAh',
                '', '', '', '', // Empty specs placeholders
                // Variants
                'Titanium Black', '1200.00',
                'Titanium Gray', '1200.00',
                'Titanium Violet', '1200.00',
            ];

            fputcsv($file, $row);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products_import_template.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
