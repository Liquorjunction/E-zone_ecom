<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\DestroyProductRequest;
use App\Http\Requests\Admin\Product\EditProductRequest;
use App\Http\Requests\Admin\Product\ReadProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.pages.product.index');
    }

    public function create()
    {
        // 1. Fetch active Brands and Categories
        $brands = Brand::where('status', 'active')->get();
        $categories = Categories::where('status', 'active')->get();
        $subCategories = SubCategories::where('status', 'active')->get();

        // 2. Pass them to the view
        return view('admin.pages.product.create', compact('brands', 'categories', 'subCategories'));
    }

    public function store(StoreProductRequest $request, ProductRepo $productRepo)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            $translatableFields = [
                'name',
                'description',
                'dimensions',
                'specifications', // Array handling added below
                'warranty',
            ];

            foreach ($translatableFields as $field) {
                // Skip if English data is missing
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // CASE A: Standard String Fields (Name, Desc, etc.)
                if (is_string($data[$field]['en'])) {
                    // Auto-fill French
                    if (empty($data[$field]['fr'])) {
                        $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                    }
                    // Auto-fill Arabic
                    if (empty($data[$field]['ar'])) {
                        $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                    }
                }

                // CASE B: Specifications Array (Dynamic Table)
                elseif ($field === 'specifications' && is_array($data[$field]['en'])) {

                    // Initialize arrays if they don't exist yet
                    if (! isset($data[$field]['fr']) || ! is_array($data[$field]['fr'])) {
                        $data[$field]['fr'] = [];
                    }
                    if (! isset($data[$field]['ar']) || ! is_array($data[$field]['ar'])) {
                        $data[$field]['ar'] = [];
                    }

                    foreach ($data[$field]['en'] as $index => $row) {
                        $enTitle = $row['title'] ?? '';
                        $enValue = $row['value'] ?? '';

                        // Translate to French
                        if (empty($data[$field]['fr'][$index]['title'])) {
                            $data[$field]['fr'][$index]['title'] = $enTitle ? gtrans_html($enTitle, 'fr') : '';
                        }
                        if (empty($data[$field]['fr'][$index]['value'])) {
                            $data[$field]['fr'][$index]['value'] = $enValue ? gtrans_html($enValue, 'fr') : '';
                        }

                        // Translate to Arabic
                        if (empty($data[$field]['ar'][$index]['title'])) {
                            $data[$field]['ar'][$index]['title'] = $enTitle ? gtrans_html($enTitle, 'ar') : '';
                        }
                        if (empty($data[$field]['ar'][$index]['value'])) {
                            $data[$field]['ar'][$index]['value'] = $enValue ? gtrans_html($enValue, 'ar') : '';
                        }
                    }
                    // Re-index array to ensure keys are sequential (0,1,2) for JSON storage
                    $data[$field]['fr'] = array_values($data[$field]['fr']);
                    $data[$field]['ar'] = array_values($data[$field]['ar']);
                }
            }
            // ---------------------------------

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
            // 1. Fetch active Brands and Categories
            $product = Product::findOrFail($id);
            $brands = Brand::where('status', 'active')->get();
            $categories = Categories::where('status', 'active')->get();
            $subCategories = SubCategories::where('status', 'active')->get();

            return view('admin.pages.product.edit', compact('product', 'brands', 'categories', 'subCategories'));
        } catch (\Exception $e) {
            Log::error('ProductController.edit: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(StoreProductRequest $request, ProductRepo $productRepo)
    {
        $id = $request->input('id');
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            $translatableFields = [
                'name',
                'description',
                'dimensions',
                'specifications',
                'warranty',
            ];

            foreach ($translatableFields as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // CASE A: Standard String Fields
                if (is_string($data[$field]['en'])) {
                    if (empty($data[$field]['fr'])) {
                        $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                    }
                    if (empty($data[$field]['ar'])) {
                        $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                    }
                }

                // CASE B: Specifications Array
                elseif ($field === 'specifications' && is_array($data[$field]['en'])) {

                    if (! isset($data[$field]['fr']) || ! is_array($data[$field]['fr'])) {
                        $data[$field]['fr'] = [];
                    }
                    if (! isset($data[$field]['ar']) || ! is_array($data[$field]['ar'])) {
                        $data[$field]['ar'] = [];
                    }

                    foreach ($data[$field]['en'] as $index => $row) {
                        $enTitle = $row['title'] ?? '';
                        $enValue = $row['value'] ?? '';

                        // Translate to French
                        if (empty($data[$field]['fr'][$index]['title'])) {
                            $data[$field]['fr'][$index]['title'] = $enTitle ? gtrans_html($enTitle, 'fr') : '';
                        }
                        if (empty($data[$field]['fr'][$index]['value'])) {
                            $data[$field]['fr'][$index]['value'] = $enValue ? gtrans_html($enValue, 'fr') : '';
                        }

                        // Translate to Arabic
                        if (empty($data[$field]['ar'][$index]['title'])) {
                            $data[$field]['ar'][$index]['title'] = $enTitle ? gtrans_html($enTitle, 'ar') : '';
                        }
                        if (empty($data[$field]['ar'][$index]['value'])) {
                            $data[$field]['ar'][$index]['value'] = $enValue ? gtrans_html($enValue, 'ar') : '';
                        }
                    }
                    // Sort keys to ensure valid JSON array
                    $data[$field]['fr'] = array_values($data[$field]['fr']);
                    $data[$field]['ar'] = array_values($data[$field]['ar']);
                }
            }
            // ---------------------------------

            $productRepo->updateProduct($id, $request, $data);

            DB::commit();

            return redirect()->back()->with(['success' => true, 'message' => 'Record updated successfully']);

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
            $id = $request->input('id'); // Get ID from the request body/query
            $productRepo->updateProductStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
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
}
