<?php

namespace App\Jobs;

use App\DTOs\ProductImportDTO;
use App\Jobs\Concerns\ImportFileLoader;
use App\Jobs\Concerns\ImportProgress;
use App\Jobs\Concerns\ImportReporting;
use App\Jobs\Concerns\ProductGrouper;
use App\Jobs\Concerns\ProductProcessing;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ImportProgress;
    use ImportReporting;
    use ImportFileLoader;
    use ProductGrouper;
    use ProductProcessing;

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

        Log::info('========== ProcessProductImport Job START ==========');
        Log::info("Batch ID: {$this->batchId}");
        Log::info("File path: {$this->filePath}");
        Log::info('File exists: '.(file_exists($this->filePath) ? 'YES' : 'NO'));

        try {
            try {
                $this->startProgress($cacheKey);
                $this->preloadMaps();

                $collection = $this->loadFile($this->filePath, $cacheKey);
                if (! $collection) {
                    return;
                }

                [$groupedProducts, $skippedRows] = $this->groupProducts($collection);

                $result = $this->processGroupedProducts(
                    $groupedProducts,
                    $skippedRows,
                    $productRepo,
                    $translationService,
                    $cacheKey
                );

                $this->persistReport($result['successful'], $result['failed_records'], $this->batchId);
                $this->finalizeProgress($cacheKey, $result);
                $this->cleanupFile();

            } catch (\Exception $e) {
                Log::error('Import Job Fatal: '.$e->getMessage());
                $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['Fatal Error: '.$e->getMessage()]);
            }
        } finally {
            Log::info('========== ProcessProductImport Job END ==========');
        }
    }
    private function importSingleProduct(ProductImportDTO $dto, $repo, $transService)
    {
        $row = $dto->mainData;
        $rowNum = $dto->rowNum;

        Log::info("========== importSingleProduct Row $rowNum START ==========");
        Log::info("DTO mainData - name_en: '".($row['name_en'] ?? '')."'");
        Log::info("DTO mainData - description_en: '".($row['description_en'] ?? '')."'");

        // 0. Check DTO-level errors (required fields validation)
        if ($dto->hasErrors()) {
            Log::error("Row $rowNum: DTO Validation FAILED - ".$dto->getErrorsAsString());

            return ['status' => 'error', 'message' => "Row $rowNum: ".$dto->getErrorsAsString()];
        }

        // 1. Validation (additional field-level checks)
        $validator = Validator::make($row, [
            'name_en' => 'required|string|max:255',
            'brand' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::error("Row $rowNum: Validation FAILED - ".json_encode($validator->errors()));

            return ['status' => 'error', 'message' => "Row $rowNum: ".$validator->errors()->first()];
        }

        Log::info("Row $rowNum: Validation PASSED");

        // Validate sub_category if provided
        if (! empty($row['sub_category'])) {
            $validator = Validator::make($row, [
                'sub_category' => 'string',
            ]);
            if ($validator->fails()) {
                Log::error("Row $rowNum: Sub-category validation FAILED");

                return ['status' => 'error', 'message' => "Row $rowNum: Invalid sub-category format"];
            }
        }

            // Validate variants if present
            if (! empty($dto->variants)) {
                foreach ($dto->variants as $varIndex => $variant) {
                    $varValidator = Validator::make($variant, [
                        'name_en' => 'required|string|max:255',
                        'price' => 'required|numeric|min:0',
                        // SKU removed from validation as it's now auto-generated from product SKU
                    ]);

                    if ($varValidator->fails()) {
                        Log::error("Row $rowNum: Variant $varIndex validation FAILED - ".json_encode($varValidator->errors()));

                        return ['status' => 'error', 'message' => "Row $rowNum: Variant validation failed - ".$varValidator->errors()->first()];
                    }
                }
            }        // Validate specs if present
        if (! empty($dto->specs)) {
            foreach ($dto->specs as $specIndex => $spec) {
                if (empty($spec['title']) || empty($spec['value'])) {
                    Log::error("Row $rowNum: Spec $specIndex is incomplete - missing title or value");

                    return ['status' => 'error', 'message' => "Row $rowNum: Specification $specIndex is incomplete"];
                }

                $specValidator = Validator::make($spec, [
                    'title' => 'required|string|max:255',
                    'value' => 'required|string|max:1000',
                ]);

                if ($specValidator->fails()) {
                    Log::error("Row $rowNum: Spec $specIndex validation FAILED - ".json_encode($specValidator->errors()));

                    return ['status' => 'error', 'message' => "Row $rowNum: Specification validation failed - ".$specValidator->errors()->first()];
                }
            }
        }

        // 2. Resolve IDs (Case Insensitive)
        $brandId = $this->brandsMap[strtolower(trim($row['brand']))] ?? null;
        $catId = $this->catsMap[strtolower(trim($row['category']))] ?? null;

        if (! $brandId) {
            Log::error("Row $rowNum: Brand '{$row['brand']}' not found in map. Available brands: ".json_encode(array_keys($this->brandsMap)));

            return ['status' => 'error', 'message' => "Row $rowNum: Brand '{$row['brand']}' not found. Check spelling or ensure brand exists in system."];
        }
        if (! $catId) {
            Log::error("Row $rowNum: Category '{$row['category']}' not found in map. Available categories: ".json_encode(array_keys($this->catsMap)));

            return ['status' => 'error', 'message' => "Row $rowNum: Category '{$row['category']}' not found. Check spelling or ensure category exists in system."];
        }

        Log::info("Row $rowNum: Brand resolved to ID {$brandId}");
        Log::info("Row $rowNum: Category resolved to ID {$catId}");

        $subId = null;
        if (! empty($row['sub_category'])) {
            $subId = $this->subCatsMap[strtolower(trim($row['sub_category']))] ?? null;
            if (! $subId) {
                Log::error("Row $rowNum: SubCategory '{$row['sub_category']}' not found in map. Available subcategories: ".json_encode(array_keys($this->subCatsMap)));

                return ['status' => 'error', 'message' => "Row $rowNum: SubCategory '{$row['sub_category']}' not found. Check spelling or ensure sub-category exists in system."];
            }
            Log::info("Row $rowNum: SubCategory resolved to ID {$subId}");
        }

        DB::beginTransaction();
        try {
            // 3. Check Duplicates (by English Name) - only active products
            $exists = Product::whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) = ?', [strtolower($row['name_en'])])->exists();
            if ($exists) {
                DB::rollBack();
                Log::warning("Row $rowNum: Product '{$row['name_en']}' already exists");

                return ['status' => 'error', 'message' => "Row $rowNum: Product '{$row['name_en']}' already exists in the system."];
            }

            Log::info("Row $rowNum: Duplicate check PASSED");

            // 4. Prepare Data
            // CRITICAL: Trim and validate translatable fields to prevent Spatie from removing empty values

            // Debug: Log all keys in $row to see what's available
            Log::critical("ROW $rowNum - All keys available in row: ".json_encode(array_keys($row)));
            Log::critical("ROW $rowNum - Raw row data (first 500 chars): ".substr(json_encode($row), 0, 500));

            $description = isset($row['description_en']) ? trim($row['description_en']) : '';
            $warranty = isset($row['warranty_en']) ? trim($row['warranty_en']) : '';
            $dimensions = isset($row['dimensions_en']) ? trim($row['dimensions_en']) : '';

            // ENCODING FIX: Ensure UTF-8 encoding and remove any invalid sequences
            // FastExcel may produce invalid UTF-8 from Excel/CSV with mixed encodings
            $description = $this->ensureUtf8($description);
            $warranty = $this->ensureUtf8($warranty);
            $dimensions = $this->ensureUtf8($dimensions);

            // Simple processing - don't use aggressive regex that corrupts UTF-8
            // Only remove actual null bytes, not valid UTF-8 sequences
            $description = str_replace("\x00", '', $description);
            $warranty = str_replace("\x00", '', $warranty);
            $dimensions = str_replace("\x00", '', $dimensions);

            $data = [
                'brand_id' => $brandId,
                'category_id' => $catId,
                'sub_category_id' => $subId,
                'price' => $row['price'],
                'status' => 'active',
                // 'sku' is OMITTED so Repository generates the Main Product SKU

                // Translatable Fields
                // NOTE: Only include locale key if value is non-empty to prevent Spatie from removing it
                'name' => ['en' => trim($row['name_en'])],
                'warranty' => ! empty($warranty) ? ['en' => $warranty] : [],
                'dimensions' => ! empty($dimensions) ? ['en' => $dimensions] : [],

                // Specs (Already grouped array)
                'specifications' => ['en' => $dto->specs],

                // Variants (SKU will be auto-generated by repository from product SKU)
                'variants' => array_map(fn ($v) => [
                    'variant_name' => ['en' => $v['name_en']],
                    'price' => $v['price'],
                    'status' => 'active',
                ], $dto->variants),
            ];

            // Add description separately with proper validation
            // Description is important but optional - only add if non-empty
            Log::critical("ROW $rowNum DESCRIPTION CHECK: isset=".(isset($row['description_en']) ? 'YES' : 'NO').', trimmed_len='.strlen($description).', empty_check='.(! empty($description) ? 'HAS_VALUE' : 'IS_EMPTY'));
            Log::critical("ROW $rowNum DESCRIPTION RAW VALUE: '".$description."'");

            if (! empty($description)) {
                $data['description'] = ['en' => $description];
                Log::critical("ROW $rowNum SETTING description to array with en value - length: ".strlen($description));
            } else {
                // Log warning but continue - some products might not have descriptions
                Log::warning("Row $rowNum: Product '{$row['name_en']}' has empty description (after cleaning)");
                Log::critical("ROW $rowNum SETTING description to empty array");
                $data['description'] = [];
            }

            Log::critical('BEFORE TRANSLATION - description isset in data? '.(isset($data['description']) ? 'YES' : 'NO'));
            Log::critical('BEFORE TRANSLATION - description value: '.json_encode($data['description']));
            Log::info("Row $rowNum: Data prepared, about to call translation service");

            // 5. Auto Translation
            $data = $transService->prepareProductData($data);

            Log::info("Row $rowNum: Translation service completed");

            // 6. Save via Repository
            Log::info("Row $rowNum: Calling repository createProduct");
            $product = $repo->createProduct(new Request, $data);

            Log::info("Row $rowNum: Product created with ID {$product->id}");

            DB::commit();

            Log::info("Row $rowNum: Transaction committed successfully");
            Log::info("========== importSingleProduct Row $rowNum SUCCESS ==========");

            return ['status' => 'success', 'product' => $product];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Row $rowNum: Import FAILED - ".$e->getMessage());
            Log::error("Row $rowNum: Stack trace: ".$e->getTraceAsString());
            Log::info("========== importSingleProduct Row $rowNum FAILED ==========");

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

    private function cleanupFile(): void
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    /**
     * Ensure string is valid UTF-8 encoding
     * FastExcel may produce invalid UTF-8 from Excel files with mixed encodings
     *
     * @param  string  $string
     * @return string
     */
    private function ensureUtf8($string)
    {
        if (empty($string)) {
            return $string;
        }

        // Check if string is valid UTF-8
        if (mb_check_encoding($string, 'UTF-8')) {
            return $string;
        }

        // If not valid UTF-8, try to detect encoding and convert
        $detected = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

        if ($detected && $detected !== 'UTF-8') {
            return mb_convert_encoding($string, 'UTF-8', $detected);
        }

        // Last resort: remove invalid UTF-8 sequences
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }
}
