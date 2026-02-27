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
use App\Models\ProductVariant;
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
            @set_time_limit(300);
            @ini_set('max_execution_time', '300');
            $data = $request->validated();
            $data = $this->translationService->prepareProductData($data);
            $product = $productRepo->createProduct($request, $data);
            DB::commit();
            Log::info('ProductController.store: Product created successfully', ['product_id' => $product->id]);
            return redirect()->route('admin.products.index')
                ->with(['success', 'message' => 'Product created successfully']);
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
            @set_time_limit(300);
            @ini_set('max_execution_time', '300');
            $data = $request->validated();
            $id = $data['id'];
            $data = $this->translationService->prepareProductData($data);
            $product = $productRepo->updateProduct($id, $request, $data);
            DB::commit();
            Log::info('ProductController.update: Product updated successfully', ['product_id' => $product->id]);
            return redirect()->route('admin.products.index')
                ->with(['success', 'message' => 'Product updated successfully']);
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

        DB::beginTransaction();
        try {
            $id = $request->input('id');
            Log::info('ProductController.toggleStatus: request received', ['product_id' => $id]);
            $product = $productRepo->updateProductStatus($id);
            // Sync all variants with product status
            $newStatus = $product->status;
            $affected = ProductVariant::where('product_id', $id)->update(['status' => $newStatus]);
            Log::info('ProductController.toggleStatus: applied status to variants', ['product_id' => $id, 'new_status' => $newStatus, 'variants_updated' => $affected]);

            DB::commit();
            Log::info('ProductController.toggleStatus: response sent', ['product_id' => $id]);
            return response()->json([
                'success' => true,
                'message' => 'Product and variants status updated successfully.',
                'status' => $newStatus,
                'variants_updated' => $affected
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.toggleStatus: Exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update product status.'], 500);
        }
    }

    public function toggleBestseller(EditProductRequest $request, ProductRepo $productRepo)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $product = $productRepo->updateProductBestseller($id);

            // Sync all variants with product bestseller status
            $newBestseller = $product->is_bestseller;
            ProductVariant::where('product_id', $id)->update(['is_bestseller' => $newBestseller]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product and variants bestseller status updated successfully.',
                'is_bestseller' => $newBestseller,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController.toggleBestseller: Exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update bestseller status.'], 500);
        }
    }

    public function toggleVariantStatus(EditProductRequest $request, ProductRepo $productRepo)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'id' => 'required|exists:products,id',
        ]);

        try {
            $variantId = $request->input('variant_id');
            $variant = $productRepo->updateProductVariantStatus($variantId);

            // fetch current product status after sync
            $product = Product::find($variant->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Product variant status updated successfully.',
                'product_status' => $product ? $product->status : null,
                'variant_id' => $variant->id,
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController.toggleVariantStatus: Exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update product variant status.'], 500);
        }
    }

    public function toggleVariantBestseller(EditProductRequest $request, ProductRepo $productRepo)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'id' => 'required|exists:products,id',
        ]);

        try {
            $variantId = $request->input('variant_id');
            $variant = $productRepo->updateProductVariantBestseller($variantId);

            $product = Product::find($variant->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Product variant bestseller status updated successfully.',
                'variant_id' => $variant->id,
                'variant_is_bestseller' => $variant->is_bestseller,
                'product_is_bestseller' => $product ? (bool)$product->is_bestseller : null,
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController.toggleVariantBestseller: Exception', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update product variant bestseller status.'], 500);
        }
    }

    public function destroy(DestroyProductRequest $request, ProductRepo $productRepo)
    {
        try {
            $id = $request->validated()['id'];
            $productRepo->deleteProduct($id);
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController.destroy: Failed to delete product', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete'], 500);
        }
    }

    public function deleteVariantImage(Request $request, ProductRepo $productRepo)
    {
        $request->validate([
            'image_id' => 'required|exists:product_images,id',
        ]);
        try {
            $imageId = $request->input('image_id');
            $success = $productRepo->deleteVariantImage($imageId);
            if ($success) {
                return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('ProductController.deleteVariantImage: Failed to delete variant image', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete image.'], 500);
        }
    }

    public function destroyVariant(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        try {
            $variantId = $request->input('variant_id');
            $variant = \App\Models\ProductVariant::findOrFail($variantId);

            foreach ($variant->images as $img) {
                if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }

            $variant->inventory()->delete();
            $variant->delete();

            Log::info('ProductController.destroyVariant: Variant deleted', ['variant_id' => $variantId]);
            return response()->json(['success' => true, 'message' => 'Variant deleted successfully']);
        } catch (\Exception $e) {
            Log::error('ProductController.destroyVariant: Failed to delete variant', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete variant.'], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240',
            'skip_translation' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('file');
            $filename = 'import_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
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
        $progress = Cache::get('import_progress_' . $batchId);

        if (! $progress) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($progress['status'] === 'completed') {
            Cache::forget('import_progress_' . $batchId);

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
        $importData = Cache::get('import_details_' . $batchId);

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
        $headers = [
            'name_en',
            'description_en',
            'brand',
            'category',
            'sub_category',
            'price',
            'warranty_en',
            'dimensions_en',
            'spec_title',
            'spec_value',
            'variant_name_en',
            'variant_price',
            'variant_is_bestseller',
            'attributes_en_key',
            'attributes_en_value',
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Sample 1: Simple product (no explicit variant row); price provided at product-level
            fputcsv($file, [
                'iPhone 15',
                'Latest Apple Phone',
                'Apple',
                'Mobile',
                'Smartphones',
                '999',
                '1 Year',
                '6.1 inch',
                'Color',
                'Black',
                '',
                '',
                '',
                '',
            ]);

            // Sample 2: Product with explicit variant row (price at variant-level)
            fputcsv($file, [
                'Galaxy S24',
                'AI Phone',
                'Samsung',
                'Mobile',
                'Smartphones',
                '0',
                '1 Year',
                '6.2 inch',
                'Processor',
                'Snapdragon 8 Gen 3',
                'Titanium Gray',
                '899',
                '1',
                'color',
                'Titanium Gray',
            ]);

            // Sample 3: Continuation row for Sample 2 with additional variant and attributes
            fputcsv($file, [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Memory',
                '256GB',
                'Titanium Black',
                '899',
                '0',
                'memory',
                '256GB',
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

            if ($products instanceof \Illuminate\Pagination\Paginator || $products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $products->load('productVariants');
            }

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('image', function ($product) use ($productRepo) {
                    if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                        return '<img src="' . asset('storage/' . $product->cover_image) . '" alt="Product Image" class="img-thumbnail" width="60" height="60">';
                    }

                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('name', function ($product) {
                    $name = $product->getTranslation('name', 'en');

                    $hasTranslations = true;
                    if (! $product->hasTranslation('name', 'ar') && ! $product->hasTranslation('name', 'fr')) {
                        $hasTranslations = false;
                    }

                    if (! $hasTranslations) {
                        return $name . ' <span class="badge bg-warning text-dark ms-2" title="Missing Translations">Not Translated</span>';
                    }

                    return $name;
                })
                ->addColumn('brand', function ($product) {
                    return $product->brand ? $product->brand->getTranslation('name', 'en') : 'N/A';
                })
                ->addColumn('category', function ($product) {
                    return $product->category ? $product->category->getTranslation('category-name', 'en') : 'N/A';
                })
                ->addColumn('is_bestseller', function ($product) use ($request) {
                    if ($request->user()->can('products.update')) {
                        $data['toggle_url'] = route('admin.products.toggleBestseller', ['id' => $product->id]);
                        $data['is_bestseller'] = $product->is_bestseller;
                        $data['id'] = $product->id;
                        $totalVariants = $product->productVariants->count();
                        $bestsellerCount = $product->productVariants->where('is_bestseller', 1)->count();
                        $data['variants_all_bestseller'] = ($totalVariants > 0 && $bestsellerCount === $totalVariants);
                        $data['variants_all_not_bestseller'] = ($totalVariants > 0 && $bestsellerCount === 0);

                        return view('admin.partials.datatable-bestseller', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $product->is_bestseller])->render();
                    }
                })
                ->addColumn('status', function ($product) use ($request) {
                    if ($request->user()->can('products.update')) {
                        $data['toggle_url'] = route('admin.products.toggleStatus', ['id' => $product->id]);
                        $data['status'] = $product->status;
                        $data['id'] = $product->id;
                        $totalVariants = $product->productVariants->count();
                        $activeCount = $product->productVariants->where('status', 'active')->count();
                        $data['variants_all_active'] = ($totalVariants > 0 && $activeCount === $totalVariants);
                        $data['variants_all_inactive'] = ($totalVariants > 0 && $activeCount === 0);
                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $product->status])->render();
                    }
                })
                ->addColumn('action', function ($product) use ($request) {
                    $data['view_url'] = $request->user()->can('products.read') ? route('admin.products.show', ['id' => $product->id]) : '';
                    $data['edit_url'] = $request->user()->can('products.update') ? route('admin.products.edit', ['id' => $product->id]) : '';

                    if ($request->user()->can('products.delete') && isset($product->productVariants_count) && $product->productVariants_count == 0) {
                        $data['delete_url'] = route('admin.products.destroy', ['id' => $product->id]);
                    } else {
                        $data['delete_url'] = '';
                    }

                    $data['product_id'] = $product->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->addColumn('variants', function ($product) {
                    return $product->productVariants->count();
                })
                ->addColumn('variants_data', function ($product) use ($request) {
                    $variantsData = [];
                    foreach ($product->productVariants as $variant) {
                        $variantName = is_array($variant->variant_name) ? ($variant->variant_name['en'] ?? 'N/A') : $variant->variant_name;
                        $orderItemsCount = isset($variant->order_items_count) ? $variant->order_items_count : (isset($variant->orderItems_count) ? $variant->orderItems_count : 0);
                        $canDeleteVariant = $request->user()->can('products.delete') && $orderItemsCount == 0;
                        $variantsData[] = [
                            'id' => $variant->id,
                            'product_id' => $product->id,
                            'name' => $variantName,
                            'sku' => $variant->sku,
                            'price' => number_format($variant->price, 2),
                            'status' => $variant->status,
                            'is_bestseller' => $variant->is_bestseller,
                            'can_update' => $request->user()->can('products.update'),
                            'can_delete' => $request->user()->can('products.delete'),
                            'can_delete_variant' => $canDeleteVariant,
                            'status_toggle_url' => route('admin.products.variantToggleStatus', ['variant_id' => $variant->id]),
                            'bestseller_toggle_url' => route('admin.products.variantToggleBestseller', ['variant_id' => $variant->id]),
                            'delete_url' => route('admin.products.variantDestroy'),
                        ];
                    }
                    return json_encode($variantsData);
                })
                ->rawColumns(['image', 'name', 'brand', 'category', 'is_bestseller', 'status', 'action', 'price', 'variants_data'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('ProductController.dataTable: ' . $e->getMessage());
            throw $e;
        }
    }

    public function reorderVariantImages(Request $request, ProductRepo $productRepo)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'order' => 'required|array',
            'order.*' => 'integer|exists:product_images,id',
        ]);
        try {
            $variantId = $request->input('variant_id');
            $order = $request->input('order');
            $productRepo->reorderVariantImages($variantId, $order);
            return response()->json(['success' => true, 'message' => 'Image order updated.']);
        } catch (\Exception $e) {
            Log::error('ProductController.reorderVariantImages: Failed to reorder images', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to reorder images.'], 500);
        }
    }
}
