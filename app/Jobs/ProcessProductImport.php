<?php

namespace App\Jobs;

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
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 Hour Timeout for large batches

    public $tries = 1;      // Don't retry automatically to avoid duplicate partial inserts

    protected $filePath;

    protected $batchId;

    public function __construct($filePath, $batchId)
    {
        $this->filePath = $filePath;
        $this->batchId = $batchId;
    }

    public function handle(ProductRepositoryInterface $productRepo, AutoTranslationService $translationService)
    {
        $cacheKey = 'import_progress_'.$this->batchId;

        try {
            // 1. Initialize Status
            $this->updateProgress($cacheKey, 'processing', 0, 0, 0, 0, []);

            // 2. Read File Count
            $collection = (new FastExcel)->import($this->filePath);
            $totalRows = $collection->count();

            if ($totalRows === 0) {
                $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File is empty. Please upload a valid CSV.']);

                return;
            }

            // 3. Pre-load Relationships (Map Lowercase Name -> ID)
            $brands = Brand::all()->mapWithKeys(fn ($i) => [strtolower(trim($i->getTranslation('name', 'en'))) => $i->id]);
            $categories = Categories::all()->mapWithKeys(fn ($i) => [strtolower(trim($i->getTranslation('category-name', 'en'))) => $i->id]);
            $subCategories = SubCategories::all()->mapWithKeys(fn ($i) => [strtolower(trim($i->getTranslation('sub-category-name', 'en'))) => $i->id]);

            $processedNames = [];
            $success = 0;
            $failed = 0;
            $processed = 0;
            $errors = [];

            foreach ($collection as $index => $line) {
                $processed++;
                $rowNum = $index + 2; // Excel row number (header is 1)

                // --- A. VALIDATION CHECKS ---

                // 1. Check Name Presence
                if (empty($line['name_en'])) {
                    $failed++;
                    $errors[] = "Row $rowNum: Product Name is missing.";
                    $this->updateProgress($cacheKey, 'processing', $totalRows, $processed, $success, $failed, $errors);

                    continue;
                }

                $nameLower = strtolower(trim($line['name_en']));

                // 2. Check File Duplicates
                if (in_array($nameLower, $processedNames)) {
                    $failed++;
                    $errors[] = "Row $rowNum: Skipped - Duplicate name found in this file.";
                    $this->updateProgress($cacheKey, 'processing', $totalRows, $processed, $success, $failed, $errors);

                    continue;
                }
                $processedNames[] = $nameLower;

                // 3. Check Database Duplicates
                // Compares against the English translation in the JSON column
                if (Product::whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) = ?', [$nameLower])->exists()) {
                    $failed++;
                    $errors[] = "Row $rowNum: Skipped - Product already exists in the system.";
                    $this->updateProgress($cacheKey, 'processing', $totalRows, $processed, $success, $failed, $errors);

                    continue;
                }

                // 4. Resolve Relationships
                $brandName = strtolower(trim($line['brand'] ?? ''));
                $catName = strtolower(trim($line['category'] ?? ''));
                $subName = strtolower(trim($line['sub_category'] ?? ''));

                $brandId = $brands[$brandName] ?? null;
                $catId = $categories[$catName] ?? null;
                $subId = (! empty($subName)) ? ($subCategories[$subName] ?? null) : null;

                // 5. Logical Validation Errors
                $rowErrors = [];
                if (! $brandId) {
                    $rowErrors[] = "Brand '{$line['brand']}' not found";
                }
                if (! $catId) {
                    $rowErrors[] = "Category '{$line['category']}' not found";
                }

                // SubCategory Validation Logic
                if (! empty($subName) && ! $subId) {
                    $rowErrors[] = "Sub Category '{$line['sub_category']}' not found";
                } elseif (empty($subId)) {
                    // Remove this elseif block if you enabled nullable sub_category_id in migration
                    $rowErrors[] = 'Sub Category is required';
                }

                if (! empty($rowErrors)) {
                    $failed++;
                    $errors[] = "Row $rowNum: ".implode(', ', $rowErrors).'.';
                    $this->updateProgress($cacheKey, 'processing', $totalRows, $processed, $success, $failed, $errors);

                    continue;
                }

                // --- B. PROCESSING ---
                try {
                    // 1. Dynamic Specifications Parsing (1 to 20)
                    $specifications = [];
                    for ($i = 1; $i <= 20; $i++) {
                        $titleKey = "spec_{$i}_title";
                        $valueKey = "spec_{$i}_value";

                        // Only add if both title and value exist for this index
                        if (! empty($line[$titleKey]) && ! empty($line[$valueKey])) {
                            $specifications[] = [
                                'title' => $line[$titleKey],
                                'value' => $line[$valueKey],
                            ];
                        }
                    }

                    // 2. Dynamic Variants Parsing (1 to 20)
                    // Looks for columns like 'variant_1_name', 'variant_1_price'
                    $variants = [];
                    for ($i = 1; $i <= 20; $i++) {
                        $vNameKey = "variant_{$i}_name";
                        $vPriceKey = "variant_{$i}_price";

                        if (! empty($line[$vNameKey]) && ! empty($line[$vPriceKey])) {
                            $variants[] = [
                                'name' => $line[$vNameKey],
                                'price' => $line[$vPriceKey],
                                'status' => 'active',
                            ];
                        }
                    }

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
                        // Assign parsed arrays
                        'specifications' => [
                            'en' => $specifications,
                        ],
                        'variants' => $variants, // Variants passed to repository
                    ];

                    // Auto Translation
                    $data = $translationService->prepareProductData($data);

                    // Save
                    $productRepo->createProduct(new Request, $data);
                    $success++;

                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle SQL Errors Gracefully
                    $failed++;
                    $msg = $e->getMessage();

                    $userMsg = 'Database Error';
                    if (str_contains($msg, 'sub_category_id')) {
                        $userMsg = 'Sub Category is missing or invalid.';
                    } elseif (str_contains($msg, 'sku')) {
                        $userMsg = 'SKU generation failed.';
                    } elseif (str_contains($msg, 'Data too long')) {
                        $userMsg = 'Data too long for one of the fields.';
                    }

                    $errors[] = "Row $rowNum: $userMsg";
                    Log::error("Import SQL Error Row $rowNum: ".$msg);

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row $rowNum: System Error. Please check logs.";
                    Log::error("Import Generic Error Row $rowNum: ".$e->getMessage());
                }

                // Update Progress
                $this->updateProgress($cacheKey, 'processing', $totalRows, $processed, $success, $failed, $errors);
            }

            // Cleanup
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

            // Mark Complete
            $this->updateProgress($cacheKey, 'completed', $totalRows, $processed, $success, $failed, $errors);

        } catch (\Exception $e) {
            Log::error('Import Job Fatal Error: '.$e->getMessage());
            $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['Fatal System Error: '.$e->getMessage()]);
        }
    }

    private function updateProgress($key, $status, $total, $processed, $success, $failed, $errors)
    {
        $percent = ($total > 0) ? round(($processed / $total) * 100) : 0;

        Cache::put($key, [
            'status' => $status,
            'progress' => $percent,
            'total' => $total,
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ], 3600);
    }
}
