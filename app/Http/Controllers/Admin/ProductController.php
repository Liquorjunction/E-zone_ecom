<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\DestroyProductRequest;
use App\Http\Requests\Admin\Product\EditProductRequest;
use App\Http\Requests\Admin\Product\ReadProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
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
use Rap2hpoutre\FastExcel\FastExcel;
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

    public function import(Request $request, ProductRepo $productRepo)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240',
        ]);

        $fullPath = null;

        try {
            // 1. FIX FILE LOCKING: Store file locally first
            $file = $request->file('file');
            $filename = 'import_'.time().'.'.$file->getClientOriginalExtension();
            $relativePath = $file->storeAs('temp_imports', $filename, 'local');

            // Get the absolute path for FastExcel to read
            $fullPath = Storage::disk('local')->path($relativePath);

            // 2. Pre-load Relationships (Name -> ID Map)
            // We map the English name (lowercase) to the ID to avoid querying the DB for every row.
            $brands = Brand::all()->mapWithKeys(function ($item) {
                return [strtolower(trim($item->getTranslation('name', 'en'))) => $item->id];
            });

            $categories = Categories::all()->mapWithKeys(function ($item) {
                return [strtolower(trim($item->getTranslation('category-name', 'en'))) => $item->id];
            });

            $subCategories = SubCategories::all()->mapWithKeys(function ($item) {
                return [strtolower(trim($item->getTranslation('sub-category-name', 'en'))) => $item->id];
            });

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            // 3. Process Import on the STORED file
            $collection = (new FastExcel)->import($fullPath);

            foreach ($collection as $index => $line) {
                $rowNumber = $index + 2; // Adjust for 0-index and header row

                // Basic Validation
                if (empty($line['name_en'])) {
                    continue;
                }

                // Lookup IDs by Name (Case-insensitive)
                $brandName = strtolower(trim($line['brand'] ?? ''));
                $catName = strtolower(trim($line['category'] ?? ''));
                $subName = strtolower(trim($line['sub_category'] ?? ''));

                $brandId = $brands[$brandName] ?? null;
                $catId = $categories[$catName] ?? null;
                $subId = $subCategories[$subName] ?? null;

                // Check for Missing Relationships
                $rowErrors = [];
                if (! $brandId) {
                    $rowErrors[] = "Brand '{$line['brand']}' not found";
                }
                if (! $catId) {
                    $rowErrors[] = "Category '{$line['category']}' not found";
                }
                // Fail if sub-category is provided but not found
                if (! empty($subName) && ! $subId) {
                    $rowErrors[] = "Sub Category '{$line['sub_category']}' not found";
                }

                if (! empty($rowErrors)) {
                    $failedCount++;
                    $errors[] = "Row {$rowNumber} (".$line['name_en'].'): '.implode(', ', $rowErrors);

                    continue; // Skip this row
                }

                // 4. Create Product
                try {
                    $data = [
                        'brand_id' => $brandId,
                        'category_id' => $catId,
                        'sub_category_id' => $subId,
                        'price' => $line['price'],
                        'status' => 'active',
                        'name' => ['en' => $line['name_en']],
                        'description' => ['en' => $line['description_en']],
                        'warranty' => ['en' => $line['warranty_en']],
                        'dimensions' => ['en' => $line['dimensions_en']],
                        // Map specs manually
                        'specifications' => [
                            'en' => [
                                ['title' => $line['spec_1_title'] ?? '', 'value' => $line['spec_1_value'] ?? ''],
                                ['title' => $line['spec_2_title'] ?? '', 'value' => $line['spec_2_value'] ?? ''],
                            ],
                        ],
                    ];

                    // Run Auto-Translation
                    $processedData = $this->translationService->prepareProductData($data);

                    // Save to DB
                    $productRepo->createProduct(new Request, $processedData);
                    $successCount++;

                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Row {$rowNumber}: Database Error - ".$e->getMessage();
                }
            }

            // 5. Cleanup: Delete the temp file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // 6. Return with Status for Popup
            $status = [
                'type' => 'import_summary',
                'success' => $successCount,
                'failed' => $failedCount,
                'errors' => $errors,
            ];

            return redirect()->route('admin.products.index')->with('import_status', $status);

        } catch (\Exception $e) {
            // Cleanup on crash
            if ($fullPath && file_exists($fullPath)) {
                unlink($fullPath);
            }

            return redirect()->back()->withErrors(['error' => 'Import failed: '.$e->getMessage()]);
        }
    }

    /**
     * Download Template using FastExcel
     */
    public function downloadTemplate()
    {
        $dummyData = collect([
            [
                'name_en' => 'Galaxy S24 Ultra',
                'description_en' => 'AI Phone with titanium frame.',
                'brand' => 'Samsung',
                'category' => 'Electronics',
                'price' => '1200.00',
                'warranty_en' => '1 Year',
                'dimensions_en' => '6.8 inch',
                'spec_1_title' => 'Processor',
                'spec_1_value' => 'Snapdragon 8 Gen 3',
                'spec_2_title' => 'Camera',
                'spec_2_value' => '200MP',
            ],
        ]);

        return (new FastExcel($dummyData))->download('product_import_template.csv');
    }
}
