<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface;
use App\Services\AutoTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class ProductsImport implements
    ToCollection,
    WithHeadingRow,
    WithLimit,
    ShouldQueue,
    WithChunkReading
{
    public function collection(Collection $rows)
    {
        // Resolve dependencies lazily for Queue
        $productRepo = app(ProductRepositoryInterface::class);
        $translationService = app(AutoTranslationService::class);

        // 1. OPTIMIZATION: Extract all names to query DB once per chunk
        // This prevents running 100 queries for 100 rows.
        $brandNames = $rows->pluck('brand')->filter()->unique()->toArray();
        $catNames = $rows->pluck('category')->filter()->unique()->toArray();
        $subCatNames = $rows->pluck('sub_category')->filter()->unique()->toArray();

        // 2. Fetch Maps (Name -> ID)
        // Note: We assume names in DB are English or match the CSV input exactly
        $brandsMap = Brand::whereIn('name', $brandNames)->pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $catsMap = Categories::whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en'))) IN ('".implode("','", array_map('strtolower', $catNames))."')")
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [strtolower($item->getTranslation('name', 'en')) => $item->id];
                    });
        // Note: For Spatie Translatable, querying by JSON key is tricky.
        // Simple approach: Fetch all active categories and filter in PHP if dataset is small,
        // OR use the whereRaw query above for MySQL 5.7+.

        $subCatsMap = SubCategories::all() // Fetching all to safely map translatable names
                    ->mapWithKeys(function ($item) {
                        return [strtolower($item->getTranslation('name', 'en')) => $item->id];
                    });


        foreach ($rows as $index => $row) {
            try {
                $rowNum = $index + 2; // +2 because of header row and 0-index

                // 3. Normalize Input
                $brandName = strtolower(trim($row['brand']));
                $catName = strtolower(trim($row['category']));
                $subCatName = strtolower(trim($row['sub_category']));

                // 4. Validate Existence
                $errors = [];
                if (!isset($brandsMap[$brandName])) $errors[] = "Brand '$brandName' not found.";
                if (!isset($catsMap[$catName])) $errors[] = "Category '$catName' not found.";
                if (!isset($subCatsMap[$subCatName])) $errors[] = "SubCategory '$subCatName' not found.";

                if (!empty($errors)) {
                    Log::error("Import Row $rowNum Skipped: " . implode(', ', $errors));
                    continue; // SKIP this row
                }

                // 5. Standard Validation
                $validator = Validator::make($row->toArray(), [
                    'name_en' => 'required|string',
                    'price' => 'required|numeric',
                    'variant_name_en' => 'nullable|string',
                    'variant_price' => 'nullable|numeric',
                    'variant_attributes_en' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    Log::error("Import Row $rowNum Validation Error: " . json_encode($validator->errors()));
                    continue;
                }

                // 6. Prepare Data with LOOKUP IDs
                $data = [
                    'name' => ['en' => $row['name_en']],
                    'description' => ['en' => $row['description_en'] ?? ''],

                    // Use mapped IDs
                    'brand_id' => $brandsMap[$brandName],
                    'category_id' => $catsMap[$catName],
                    'sub_category_id' => $subCatsMap[$subCatName],

                    'price' => $row['price'],
                    'status' => 'active',
                    'is_bestseller' => filter_var($row['is_bestseller'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'specifications' => [
                        'en' => $this->parseSpecs($row)
                    ],
                    'warranty' => ['en' => $row['warranty_en'] ?? ''],
                    'dimensions' => ['en' => $row['dimensions_en'] ?? ''],
                ];

                // 7. Auto-Translate & Save
                $data = $translationService->prepareProductData($data);
                $productRepo->createProduct(request(), $data);

            } catch (\Exception $e) {
                Log::error('Import Failed for Row ' . ($index+2) . ': ' . $e->getMessage());
            }
        }
    }

    public function limit(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 50;
    }

    private function parseSpecs($row)
    {
        $specs = [];
        for ($i = 1; $i <= 20; $i++) {
            if (!empty($row["spec_{$i}_title"]) && !empty($row["spec_{$i}_value"])) {
                $specs[] = [
                    'title' => $row["spec_{$i}_title"],
                    'value' => $row["spec_{$i}_value"],
                ];
            }
        }
        return $specs;
    }
}
