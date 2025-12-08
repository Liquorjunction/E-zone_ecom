<?php

namespace App\Jobs;

use App\DTOs\ProductImportDTO;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface;
use App\Services\AutoTranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout

    public $tries = 1;

    protected $filePath;

    protected $batchId;

    // Lookup Maps
    protected $brandsMap = [];

    protected $catsMap = [];

    protected $subCatsMap = [];

    public function __construct($filePath, $batchId)
    {
        $this->filePath = $filePath;
        $this->batchId = $batchId;
    }

    public function handle(ProductRepositoryInterface $productRepo, AutoTranslationService $translationService)
    {
        $cacheKey = 'import_progress_'.$this->batchId;

        try {
            $this->updateProgress($cacheKey, 'processing', 0, 0, 0, 0, []);

            // 1. Preload Maps (Fixes the Translation Error)
            $this->preloadMaps();

            // 2. Read File
            try {
                $collection = (new FastExcel)->import($this->filePath);
            } catch (\Exception $e) {
                $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File error: '.$e->getMessage()]);

                return;
            }

            if ($collection->count() === 0) {
                $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File is empty.']);

                return;
            }

            // 3. Group Rows Logic
            $groupedProducts = [];
            $currentDTO = null;

            foreach ($collection as $index => $row) {
                $rowNum = $index + 2;

                // Normalize Row: Cast to array, lowercase keys, trim values
                $row = (array) $row;
                $row = array_change_key_case($row, CASE_LOWER);
                $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

                // Check for Main Product Row
                if (! empty($row['name_en'])) {
                    // If we were building a product, save it to the list
                    if ($currentDTO) {
                        $groupedProducts[] = $currentDTO;
                    }
                    // Start new product DTO
                    $currentDTO = new ProductImportDTO($rowNum, $row);
                }

                // If we have an active product, add Details (Specs & Variants)
                // This applies to the Main Row AND Child Rows
                if ($currentDTO) {
                    $this->appendDetailsToDTO($currentDTO, $row);
                }
            }

            // Don't forget the last product in the loop
            if ($currentDTO) {
                $groupedProducts[] = $currentDTO;
            }

            // 4. Process the Groups
            $totalProducts = count($groupedProducts);
            $processed = 0;
            $success = 0;
            $failed = 0;
            $errors = [];

            foreach ($groupedProducts as $dto) {
                $processed++;
                $result = $this->importSingleProduct($dto, $productRepo, $translationService);

                if ($result['status'] === 'success') {
                    $success++;
                } else {
                    $failed++;
                    $errors[] = $result['message'];
                }

                // Update Progress every 5 items
                if ($processed % 5 === 0 || $processed === $totalProducts) {
                    $this->updateProgress($cacheKey, 'processing', $totalProducts, $processed, $success, $failed, $errors);
                }
            }

            // Cleanup
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }

            $this->updateProgress($cacheKey, 'completed', $totalProducts, $processed, $success, $failed, $errors);

        } catch (\Exception $e) {
            Log::error('Import Job Fatal: '.$e->getMessage());
            $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['Fatal Error: '.$e->getMessage()]);
        }
    }

    /**
     * Helper to add Specs and Variants to the DTO
     */
    private function appendDetailsToDTO(ProductImportDTO $dto, array $row)
    {
        // 1. Add Specification
        $specTitle = $row['spec_title'] ?? $row['spec_1_title'] ?? null;
        $specValue = $row['spec_value'] ?? $row['spec_1_value'] ?? null;

        if (! empty($specTitle) && ! empty($specValue)) {
            $dto->specs[] = ['title' => $specTitle, 'value' => $specValue];
        }

        // 2. Add Variant (Only if price exists)
        $varPrice = $row['variant_price'] ?? $row['variant_1_price'] ?? null;

        if (! empty($varPrice)) {
            $varName = $row['variant_name_en'] ?? $row['variant_1_name_en'] ?? null;
            $finalVarName = $varName ?: ($dto->mainData['name_en'].' - Variant');

            // Auto-Generate Variant SKU ONLY if missing
            $varSku = $row['variant_sku'] ?? null;
            if (empty($varSku)) {
                $slug = Str::slug($dto->mainData['name_en']).'-'.Str::slug($finalVarName);
                $varSku = substr($slug, 0, 40).'-'.strtoupper(Str::random(6));
            }

            $dto->variants[] = [
                'name_en' => $finalVarName,
                'price' => $varPrice,
                'sku' => $varSku,
            ];
        }
    }

    private function importSingleProduct(ProductImportDTO $dto, $repo, $transService)
    {
        $row = $dto->mainData;
        $rowNum = $dto->rowNum;

        // 1. Validation
        $validator = Validator::make($row, [
            'name_en' => 'required|string|max:255',
            'brand' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['status' => 'error', 'message' => "Row $rowNum: ".$validator->errors()->first()];
        }

        // 2. Resolve IDs (Case Insensitive)
        $brandId = $this->brandsMap[strtolower(trim($row['brand']))] ?? null;
        $catId = $this->catsMap[strtolower(trim($row['category']))] ?? null;

        if (! $brandId) {
            return ['status' => 'error', 'message' => "Row $rowNum: Brand '{$row['brand']}' not found."];
        }
        if (! $catId) {
            return ['status' => 'error', 'message' => "Row $rowNum: Category '{$row['category']}' not found."];
        }

        $subId = null;
        if (! empty($row['sub_category'])) {
            $subId = $this->subCatsMap[strtolower(trim($row['sub_category']))] ?? null;
            if (! $subId) {
                return ['status' => 'error', 'message' => "Row $rowNum: SubCategory '{$row['sub_category']}' not found."];
            }
        }

        DB::beginTransaction();
        try {
            // 3. Check Duplicates (by English Name)
            $exists = Product::whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) = ?', [strtolower($row['name_en'])])->exists();
            if ($exists) {
                DB::rollBack();

                return ['status' => 'error', 'message' => "Row $rowNum: Product '{$row['name_en']}' already exists."];
            }

            // 4. Prepare Data
            $data = [
                'brand_id' => $brandId,
                'category_id' => $catId,
                'sub_category_id' => $subId,
                'price' => $row['price'],
                'status' => 'active',
                // 'sku' is OMITTED so Repository generates the Main Product SKU

                // Translatable Fields
                'name' => ['en' => $row['name_en']],
                'description' => ['en' => $row['description_en'] ?? ''],
                'warranty' => ['en' => $row['warranty_en'] ?? ''],
                'dimensions' => ['en' => $row['dimensions_en'] ?? ''],

                // Specs (Already grouped array)
                'specifications' => ['en' => $dto->specs],

                // Variants (With auto-generated SKU if missing)
                'variants' => array_map(fn ($v) => [
                    'variant_name' => ['en' => $v['name_en']],
                    'price' => $v['price'],
                    'sku' => $v['sku'],
                    'status' => 'active',
                ], $dto->variants),
            ];

            // 5. Auto Translation
            $data = $transService->prepareProductData($data);

            // 6. Save via Repository
            $repo->createProduct(new Request, $data);

            DB::commit();

            return ['status' => 'success'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import Error Row $rowNum: ".$e->getMessage());

            return ['status' => 'error', 'message' => "Row $rowNum: ".$e->getMessage()];
        }
    }

    private function preloadMaps()
    {
        // 1. Brands (Key: name)
        $this->brandsMap = Brand::all()->mapWithKeys(function ($i) {
            try {
                $name = $i->getTranslation('name', 'en');
            } catch (\Exception $e) {
                $name = $i->name;
            }

            return [strtolower(trim($name)) => $i->id];
        })->toArray();

        // 2. Categories (Key: category-name) <-- FIXED
        $this->catsMap = Categories::all()->mapWithKeys(function ($i) {
            try {
                // Must use the exact attribute name from your Model's $translatable array
                $name = $i->getTranslation('category-name', 'en');
            } catch (\Exception $e) {
                $name = $i->name; // Fallback
            }

            return [strtolower(trim($name)) => $i->id];
        })->toArray();

        // 3. SubCategories (Key: sub-category-name) <-- FIXED
        $this->subCatsMap = SubCategories::all()->mapWithKeys(function ($i) {
            try {
                // Must use the exact attribute name from your Model's $translatable array
                $name = $i->getTranslation('sub-category-name', 'en');
            } catch (\Exception $e) {
                $name = $i->name; // Fallback
            }

            return [strtolower(trim($name)) => $i->id];
        })->toArray();
    }

    private function updateProgress($key, $status, $total, $processed, $success, $failed, $errors)
    {
        $percent = ($total > 0) ? round(($processed / $total) * 100) : 0;
        Cache::put($key, compact('status', 'percent', 'total', 'processed', 'success', 'failed', 'errors'), 3600);
    }
}
