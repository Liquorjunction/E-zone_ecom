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

    protected $skipTranslation;

    // Lookup Maps
    protected $brandsMap = [];

    protected $catsMap = [];

    protected $subCatsMap = [];

    public function __construct($filePath, $batchId, $skipTranslation = false)
    {
        $this->filePath = $filePath;
        $this->batchId = $batchId;
        $this->skipTranslation = $skipTranslation;
    }

    public function handle(ProductRepositoryInterface $productRepo, AutoTranslationService $translationService)
    {
        $cacheKey = 'import_progress_'.$this->batchId;

        Log::info('========== ProcessProductImport Job START ==========');
        Log::info("Batch ID: {$this->batchId}");

        try {
            try {
                $this->startProgress($cacheKey);

                // Conditionally disable translation based on user choice
                if ($this->skipTranslation) {
                    $translationService->disable();
                    Log::info('Translation DISABLED for this import (user option)');
                } else {
                    Log::info('Translation ENABLED for this import');
                }

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

        // Quick validation check for critical fields only
        if (empty($row['name_en']) || empty($row['brand']) || empty($row['category']) || !isset($row['price'])) {
            return ['status' => 'error', 'message' => "Row $rowNum: Missing required fields"];
        }

        // DTO-level errors check
        if ($dto->hasErrors()) {
            return ['status' => 'error', 'message' => "Row $rowNum: ".$dto->getErrorsAsString()];
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
                return ['status' => 'error', 'message' => "Row $rowNum: Product '{$row['name_en']}' already exists in the system."];
            }

            // 4. Prepare Data
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

            // Add description if present
            if (! empty($description)) {
                $data['description'] = ['en' => $description];
            } else {
                $data['description'] = [];
            }

            // 5. Auto Translation
            $data = $transService->prepareProductData($data);

            // 6. Save via Repository
            $product = $repo->createProduct(new Request, $data);

            DB::commit();

            // Log only every 10th product or errors
            if ($rowNum % 10 === 0) {
                Log::info("Row $rowNum: Product created with ID {$product->id}");
            }

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
