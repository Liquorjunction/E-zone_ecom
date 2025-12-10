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

        Log::info('========== ProcessProductImport Job START ==========');
        Log::info("Batch ID: {$this->batchId}");
        Log::info("File path: {$this->filePath}");
        Log::info('File exists: '.(file_exists($this->filePath) ? 'YES' : 'NO'));

        try {
            $this->updateProgress($cacheKey, 'processing', 0, 0, 0, 0, []);

            // 1. Preload Maps (Fixes the Translation Error)
            Log::info('Preloading lookup maps...');
            $this->preloadMaps();
            Log::info('Brands map count: '.count($this->brandsMap));
            Log::info('Categories map count: '.count($this->catsMap));
            Log::info('SubCategories map count: '.count($this->subCatsMap));

            // 2. Read File
            Log::info('Reading file with FastExcel...');
            try {
                $collection = (new FastExcel)->import($this->filePath);
            } catch (\Exception $e) {
                Log::error('File read FAILED: '.$e->getMessage());
                $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File error: '.$e->getMessage()]);

                return;
            }

            Log::info('File read SUCCESS, total rows: '.$collection->count());

            if ($collection->count() === 0) {
                Log::error('File is empty');
                $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File is empty.']);

                return;
            }

            // 3. Group Rows Logic
            Log::info('Starting row grouping process...');
            $groupedProducts = [];
            $currentDTO = null;
            $skippedRows = [];

            foreach ($collection as $index => $row) {
                $rowNum = $index + 2;

                // Normalize Row: Cast to array, lowercase keys, trim values
                $row = (array) $row;
                $row = array_change_key_case($row, CASE_LOWER);
                $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

                // Check for Main Product Row (non-empty name_en indicates a new product)
                if (! empty($row['name_en'])) {
                    // If we were building a product, validate and save it
                    if ($currentDTO) {
                        if ($currentDTO->hasErrors()) {
                            // Log errors for previous product
                            $errorMsg = "Row {$currentDTO->rowNum}: ".$currentDTO->getErrorsAsString();
                            Log::warning($errorMsg);
                            $skippedRows[] = $errorMsg;
                        } else {
                            $groupedProducts[] = $currentDTO;
                        }
                    }

                    // Start new product DTO
                    $currentDTO = new ProductImportDTO($rowNum, $row);

                    // Check immediately if this new product has errors
                    if ($currentDTO->hasErrors()) {
                        $errorMsg = "Row {$currentDTO->rowNum}: ".$currentDTO->getErrorsAsString();
                        Log::warning($errorMsg);
                        $skippedRows[] = $errorMsg;
                        $currentDTO = null; // Skip this invalid product
                    }
                } else {
                    // This is a detail row (variant/spec)
                    // Only add if we have an active valid product
                    if ($currentDTO) {
                        $appendError = $this->appendDetailsToDTO($currentDTO, $row, $rowNum);
                        if ($appendError) {
                            $skippedRows[] = "Row $rowNum: $appendError";
                        }
                    } else {
                        // Detail row without a parent product
                        $skippedRows[] = "Row $rowNum: Detail row without parent product (no name_en found in previous rows)";
                        Log::warning("Row $rowNum: Detail row without parent product");
                    }
                }
            }

            // Don't forget the last product in the loop
            if ($currentDTO) {
                if ($currentDTO->hasErrors()) {
                    $errorMsg = "Row {$currentDTO->rowNum}: ".$currentDTO->getErrorsAsString();
                    Log::warning($errorMsg);
                    $skippedRows[] = $errorMsg;
                } else {
                    $groupedProducts[] = $currentDTO;
                }
            }

            // Log summary of skipped rows
            if (! empty($skippedRows)) {
                Log::warning('Total skipped rows: '.count($skippedRows));
                foreach ($skippedRows as $skip) {
                    Log::warning($skip);
                }
            }

            // 4. Process the Groups
            $totalProducts = count($groupedProducts);
            $processed = 0;
            $success = 0;
            $failed = 0;
            $errors = [];
            $successfulProducts = [];
            $failedProducts = [];

            foreach ($groupedProducts as $dto) {
                $processed++;
                $result = $this->importSingleProduct($dto, $productRepo, $translationService);

                if ($result['status'] === 'success') {
                    $success++;
                    if (! empty($result['product'])) {
                        $successfulProducts[] = [
                            'name_en' => $result['product']->getTranslation('name', 'en'),
                            'sku' => $result['product']->sku,
                            'brand' => $result['product']->brand->getTranslation('name', 'en') ?? '',
                            'category' => $result['product']->category->getTranslation('category-name', 'en') ?? '',
                            'id' => $result['product']->id,
                        ];
                    }
                } else {
                    $failed++;
                    $errors[] = $result['message'];
                    $failedProducts[] = [
                        'name_en' => $dto->mainData['name_en'] ?? 'Unknown',
                        'sku' => $dto->mainData['sku'] ?? 'N/A',
                        'brand' => $dto->mainData['brand'] ?? 'N/A',
                        'category' => $dto->mainData['category'] ?? 'N/A',
                        'error' => $result['message'],
                    ];
                }

                // Update Progress every 5 items
                if ($processed % 5 === 0 || $processed === $totalProducts) {
                    $this->updateProgress($cacheKey, 'processing', $totalProducts, $processed, $success, $failed, $errors);
                }
            }

            // Add validation/skip errors from earlier steps to failed list
            foreach ($skippedRows as $skipMsg) {
                $failedProducts[] = [
                    'name_en' => 'N/A',
                    'sku' => 'N/A',
                    'brand' => 'N/A',
                    'category' => 'N/A',
                    'error' => $skipMsg,
                ];
            }

            // Cleanup
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }

            // 5. Store Detailed Results in Cache for Retrieval
            Cache::put('import_details_'.$this->batchId, [
                'successful' => $successfulProducts,
                'failed' => $failedProducts,
            ], 86400);

            $this->updateProgress($cacheKey, 'completed', $totalProducts, $processed, $success, $failed, $errors);

        } catch (\Exception $e) {
            Log::error('Import Job Fatal: '.$e->getMessage());
            $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['Fatal Error: '.$e->getMessage()]);
        }
    }

    /**
     * Helper to add Specs and Variants to the DTO
     * Returns error message if any validation fails, null otherwise
     */
    private function appendDetailsToDTO(ProductImportDTO $dto, array $row, int $rowNum = 0)
    {
        $errors = [];

        // 1. Add Specification (both fields must be non-empty)
        $specTitle = $row['spec_title'] ?? $row['spec_1_title'] ?? null;
        $specValue = $row['spec_value'] ?? $row['spec_1_value'] ?? null;

        if (! empty($specTitle) || ! empty($specValue)) {
            // If one is empty, that's an error - both must exist together
            if (empty($specTitle)) {
                $errors[] = 'Spec has value but missing title';
            } elseif (empty($specValue)) {
                $errors[] = 'Spec has title but missing value';
            } else {
                // Both present and valid
                $dto->specs[] = ['title' => $specTitle, 'value' => $specValue];
            }
        }

        // 2. Add Variant (price is REQUIRED for variant)
        $varPrice = $row['variant_price'] ?? $row['variant_1_price'] ?? null;

        if (! empty($varPrice)) {
            // Validate price is numeric and positive
            if (! is_numeric($varPrice) || floatval($varPrice) <= 0) {
                $errors[] = 'Invalid variant price: must be a positive number';
            } else {
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
        } elseif (! empty($row['variant_name_en']) || ! empty($row['variant_sku'])) {
            // Variant name or SKU provided but no price
            $errors[] = 'Variant has name/SKU but missing price';
        }

        // Return error message if any validation failed
        return empty($errors) ? null : implode('; ', $errors);
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
                    'sku' => 'required|string|max:100',
                ]);

                if ($varValidator->fails()) {
                    Log::error("Row $rowNum: Variant $varIndex validation FAILED - ".json_encode($varValidator->errors()));

                    return ['status' => 'error', 'message' => "Row $rowNum: Variant validation failed - ".$varValidator->errors()->first()];
                }
            }
        }

        // Validate specs if present
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
            // 3. Check Duplicates (by English Name)
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

                // Variants (With auto-generated SKU if missing)
                'variants' => array_map(fn ($v) => [
                    'variant_name' => ['en' => $v['name_en']],
                    'price' => $v['price'],
                    'sku' => $v['sku'],
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

    private function updateProgress($key, $status, $total, $processed, $success, $failed, $errors)
    {
        $percent = ($total > 0) ? round(($processed / $total) * 100) : 0;
        // Keep both keys so any consumer (progress or percent) sees the same value
        Cache::put($key, [
            'status' => $status,
            'percent' => $percent,
            'progress' => $percent,
            'total' => $total,
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ], 3600);
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
