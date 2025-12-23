<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\DestroyProductRequest;
use App\Http\Requests\Admin\Product\EditProductRequest;
use App\Http\Requests\Admin\Product\ReadProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Jobs\ProcessProductImport;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            $product = $productRepo->createProduct($request, $data);
            DB::commit();
            Log::info('ProductController.store: Product created successfully', ['product_id' => $product->id]);
            return redirect()->route('admin.products.index')
                ->with(['success' => true, 'message' => 'Product created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.store: Failed to create product', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create product. Please check logs for details.']);
        }
    }

    public function edit(EditProductRequest $request)
    {
        try {
            $id = $request->input('id');
            // Eager load variants (and their inventory) + images
            $product = Product::with(['productVariants.inventory', 'images'])->findOrFail($id);

            $brands = Brand::where('status', 'active')->get();
            $categories = Categories::where('status', 'active')->get();
            $subCategories = SubCategories::where('status', 'active')
                ->where('category_id', $product->category_id)
                ->get();

            return view('admin.pages.product.edit', compact('product', 'brands', 'categories', 'subCategories'));

        } catch (\Exception $e) {
            Log::error('ProductController.edit: Failed to fetch product for edit', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

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
            $product = $productRepo->updateProduct($id, $request, $data);
            DB::commit();
            Log::info('ProductController.update: Product updated successfully', ['product_id' => $product->id]);
            return redirect()->route('admin.products.index')
                ->with(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.update: Failed to update product', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update product. Please check logs for details.']);
        }
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $product = Product::with('productVariants')->findOrFail($id);

        return view('admin.pages.product.show', compact('product'));
    }

    public function toggleStatus(EditProductRequest $request, ProductRepo $productRepo)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        try {
            $id = $request->input('id');
            $productRepo->updateProductStatus($id);

            return response()->json(['success' => true, 'message' => 'Product status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('ProductController.toggleStatus: Exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update product status.'], 500);
        }
    }

    public function toggleBestseller(EditProductRequest $request, ProductRepo $productRepo)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        try {
            $id = $request->input('id');
            $product = $productRepo->updateProductBestseller($id);

            return response()->json([
                'success' => true,
                'message' => 'Product bestseller status updated successfully.',
                'is_bestseller' => $product->is_bestseller,
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController.toggleBestseller: Exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update bestseller status.'], 500);
        }
    }

    public function destroy(DestroyProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $id = $request->validated()['id'];
            $productRepo->deleteProduct($id);
            Log::info('ProductController.destroy: Product deleted', ['product_id' => $id]);
            return redirect()->back()->with('success', 'Record deleted successfully');
        } catch (\Exception $e) {
            Log::error('ProductController.destroy: Failed to delete product', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to delete record. Please check logs for details.');
        }
    }

    /**
     * Return active variants for a given product as JSON.
     */
    // Variants endpoint moved to InventoryController to centralize inventory-related APIs.

    // --- IMPORT FUNCTIONALITY ---

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240',
            'skip_translation' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('file');
            $filename = 'import_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $relativePath = $file->storeAs('temp_imports', $filename, 'local');
            $fullPath = Storage::disk('local')->path($relativePath);

            $batchId = Str::random(10);
            $skipTranslation = $request->input('skip_translation', false);

            ProcessProductImport::dispatch($fullPath, $batchId, $skipTranslation);

            return response()->json([
                'success' => true,
                'batch_id' => $batchId,
                'message' => 'File uploaded. Processing started in background.',
            ]);

        } catch (\Exception $e) {
            Log::error('Import Start Error: Failed to start import', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json(['success' => false, 'message' => 'Failed to start import. Please check logs for details.'], 500);
        }
    }

    public function importProgress($batchId)
    {
        $progress = Cache::get('import_progress_'.$batchId);

        if (! $progress) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($progress['status'] === 'completed') {
            // Clean up cache entries after completion
            Cache::forget('import_progress_'.$batchId);

            session()->flash('import_status', [
                'type' => 'import_summary',
                'success' => $progress['success'],
                'failed' => $progress['failed'],
                'errors' => $progress['errors'],
            ]);
        }

        if (! isset($progress['percent']) && isset($progress['progress'])) {
            $progress['percent'] = $progress['progress'];
        }

        if (! isset($progress['percent']) && ($progress['total'] ?? 0) > 0) {
            $progress['percent'] = round((($progress['processed'] ?? 0) / $progress['total']) * 100);
        }

        return response()->json($progress);
    }

    public function importReport($batchId)
    {
        $importData = Cache::get('import_details_'.$batchId);

        if (! $importData) {
            return response()->json(['successful' => [], 'failed' => [], 'skipped' => []]);
        }

        $report = [
            'successful' => $importData['successful'] ?? [],
            'failed' => $importData['failed'] ?? [],
            'skipped' => $importData['skipped'] ?? [],
        ];

        return response()->json($report);
    }

    public function downloadTemplate()
    {
        // Updated Headers for Variant-Centric Model
            $headers = [
                'name_en', 'description_en', 'brand', 'category', 'sub_category',
                'price', 'is_bestseller', 'warranty_en', 'dimensions_en',
                'spec_title', 'spec_value',
                'variant_name_en', 'variant_price',
                'attributes_en_key', 'attributes_en_value',
            ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Example 1: Simple Product (Price on Main Row)
            fputcsv($file, [
                'iPhone 15', 'Latest Apple Phone', 'Apple', 'Mobile', 'Smartphones',
                '999', '1', '1 Year', '6.1 inch',
                'Color', 'Black',
                '', '',
                '', '',
            ]);

            // Example 2: Complex Product (Grouped Rows)
            // Row 1: Main Data + First Variant (Titanium Gray)
            fputcsv($file, [
                'Galaxy S24', 'AI Phone', 'Samsung', 'Mobile', 'Smartphones',
                '0', '0', '1 Year', '6.2 inch',
                'Processor', 'Snapdragon 8 Gen 3',
                'Titanium Gray', '899',
                'color', 'Titanium Gray',
                'couleur', 'Gris',
            ]);
            // Row 2: Second Variant (Titanium Black) - Empty main cols
            fputcsv($file, [
                '', '', '', '', '',
                '', '', '', '',
                'Memory', '256GB',
                'Titanium Black', '899',
                'memory', '256GB',
                '', '',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-type' => '.xls/xlsx/csv',
            'Content-Disposition' => 'attachment; filename=product_template.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
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
                    $name = $product->getTranslation('name', 'en');

                    // Simple Translation Check
                    $hasTranslations = true;
                    if (! $product->hasTranslation('name', 'ar') && ! $product->hasTranslation('name', 'fr')) {
                        $hasTranslations = false;
                    }

                    if (! $hasTranslations) {
                        return $name.' <span class="badge bg-warning text-dark ms-2" title="Missing Translations">Not Translated</span>';
                    }

                    return $name;
                })
                ->addColumn('brand', function ($product) {
                    return $product->brand ? $product->brand->getTranslation('name', 'en') : 'N/A';
                })
                ->addColumn('category', function ($product) {
                    return $product->category ? $product->category->getTranslation('category-name', 'en') : 'N/A';
                })
                // NEW: Show Variant Price Range
                ->editColumn('price', function ($product) {
                    // Use the min_price from the repository query to avoid N+1 queries
                    $price = $product->min_price ?? 0;

                    return $price > 0 ? '$'.number_format($price, 2) : '<span class="text-muted">No Stock</span>';
                })
                ->addColumn('is_bestseller', function ($product) use ($request) {
                    $data['toggle_url'] = $request->user()->can('products.update') ? route('admin.products.toggleBestseller', ['id' => $product->id]) : '';
                    $data['is_bestseller'] = $product->is_bestseller;
                    $data['id'] = $product->id;

                    return view('admin.partials.datatable-bestseller', $data)->render();
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
                    $data['delete_url'] = $request->user()->can('products.delete') ? route('admin.products.destroy', ['id' => $product->id]) : '';
                    $data['product_id'] = $product->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['image', 'name', 'brand', 'category', 'is_bestseller', 'status', 'action', 'price'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('ProductController.dataTable: '.$e->getMessage());
            throw $e;
        }
    }
}
