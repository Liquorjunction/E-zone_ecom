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

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ImportFileLoader, ImportProgress, ImportReporting, ProductGrouper, ProductProcessing;

    public $timeout = 3600;
    public $tries = 1;
    protected $filePath;
    protected $batchId;
    protected $skipTranslation;
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

        Log::info("========== ProcessProductImport Job START (Batch: {$this->batchId}) ==========");

        try {
            $this->startProgress($cacheKey);

            if ($this->skipTranslation) {
                $translationService->disable();
                Log::info('Translation DISABLED for this import (user option)');
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
        } finally {
            Log::info('========== ProcessProductImport Job END ==========');
        }
    }

    private function importSingleProduct(ProductImportDTO $dto, $repo, $transService)
    {
        $row = $dto->mainData;
        $rowNum = $dto->rowNum;

        if (empty($row['name_en']) || empty($row['brand']) || empty($row['category'])) {
            return ['status' => 'error', 'message' => "Row $rowNum: Missing required fields (Name, Brand, Category)"];
        }

        if (empty($dto->variants) && ! isset($row['price'])) {
            return ['status' => 'error', 'message' => "Row $rowNum: Missing required field: Price (required when no variants are provided)"];
        }

        if ($dto->hasErrors()) {
            return ['status' => 'error', 'message' => "Row $rowNum: ".$dto->getErrorsAsString()];
        }

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
            $exists = Product::whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) = ?', [strtolower($row['name_en'])])->exists();
            if ($exists) {
                DB::rollBack();

                return ['status' => 'error', 'message' => "Row $rowNum: Product '{$row['name_en']}' already exists."];
            }

            $data = [
                'brand_id' => $brandId,
                'category_id' => $catId,
                'sub_category_id' => $subId,
                'price' => 0,
                'name' => ['en' => trim($row['name_en'])],
                'specifications' => ['en' => $dto->specs],
            ];

            if (! empty($row['description_en'])) {
                $data['description'] = ['en' => $this->ensureUtf8($row['description_en'])];
            }
            if (! empty($row['warranty_en'])) {
                $data['warranty'] = ['en' => $this->ensureUtf8($row['warranty_en'])];
            }
            if (! empty($row['dimensions_en'])) {
                $data['dimensions'] = ['en' => $this->ensureUtf8($row['dimensions_en'])];
            }
            if (! empty($row['cover_image'])) {
                $data['cover_image'] = $row['cover_image'];
            }

            $variantsData = [];

            if (! empty($dto->variants)) {
                foreach ($dto->variants as $v) {
                    $entry = [
                        'variant_name' => ['en' => $v['name_en']],
                        'price' => $v['price'],
                        'is_bestseller' => (bool)($v['is_bestseller'] ?? false),
                    ];
                    if (! empty($v['attributes']) && is_array($v['attributes'])) {
                        if (isset($v['attributes']['en'])) {
                            $entry['attributes'] = ['en' => $v['attributes']['en']];
                        } else {
                            $entry['attributes'] = ['en' => $v['attributes']];
                        }
                    }
                    $variantsData[] = $entry;
                }
            } else {
                $variantsData[] = [
                    'variant_name' => ['en' => trim($row['name_en'])],
                    'price' => $row['price'],
                    'attributes' => ['en' => []],
                    'is_bestseller' => false,
                ];
            }

            $data['variants'] = $variantsData;
            $variantSamples = [];
            foreach ($variantsData as $vd) {
                $variantSamples[] = [
                    'name_en' => $vd['variant_name']['en'] ?? ($vd['name']['en'] ?? null),
                    'attributes_count' => isset($vd['attributes']['en']) ? count($vd['attributes']['en']) : 0,
                ];
            }
            Log::info("Row $rowNum: Prepared variants for import", ['variants' => count($variantsData), 'samples' => $variantSamples]);

            $data = $transService->prepareProductData($data);
            $product = $repo->createProduct(new Request, $data);

            DB::commit();

            if ($rowNum % 10 === 0) {
                Log::info("Row $rowNum: Product created successfully (ID: {$product->id})");
            }

            return ['status' => 'success', 'product' => $product];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Row $rowNum: Import FAILED", ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return ['status' => 'error', 'message' => "Row $rowNum: Import failed. Please check logs."];
        }
    }

    private function preloadMaps()
    {
        // 1. Brands
        $this->brandsMap = Brand::all()->mapWithKeys(function ($i) {
            try {
                $name = $i->getTranslation('name', 'en');
            } catch (\Exception $e) {
                $name = $i->name;
            }

            return [strtolower(trim($name)) => $i->id];
        })->toArray();

        // 2. Categories
        $this->catsMap = Categories::all()->mapWithKeys(function ($i) {
            try {
                $name = $i->getTranslation('category-name', 'en');
            } catch (\Exception $e) {
                $name = $i->name;
            }

            return [strtolower(trim($name)) => $i->id];
        })->toArray();

        // 3. SubCategories
        $this->subCatsMap = SubCategories::all()->mapWithKeys(function ($i) {
            try {
                $name = $i->getTranslation('sub-category-name', 'en');
            } catch (\Exception $e) {
                $name = $i->name;
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

    private function ensureUtf8($string)
    {
        if (empty($string)) {
            return $string;
        }

        $string = str_replace("\x00", '', $string);

        if (mb_check_encoding($string, 'UTF-8')) {
            return $string;
        }

        $detected = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($detected && $detected !== 'UTF-8') {
            return mb_convert_encoding($string, 'UTF-8', $detected);
        }

        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }
}
