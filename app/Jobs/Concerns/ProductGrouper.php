<?php

namespace App\Jobs\Concerns;

use App\DTOs\ProductImportDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ProductGrouper
{
    private function groupProducts($collection): array
    {
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

            if (! empty($row['name_en'])) {
                if ($currentDTO) {
                    if ($currentDTO->hasErrors()) {
                        $errorMsg = "Row {$currentDTO->rowNum}: " . $currentDTO->getErrorsAsString();
                        Log::warning($errorMsg);
                        $skippedRows[] = $errorMsg;
                    } else {
                        $groupedProducts[] = $currentDTO;
                    }
                }

                $currentDTO = new ProductImportDTO($rowNum, $row);

                if ($currentDTO->hasErrors()) {
                    $errorMsg = "Row {$currentDTO->rowNum}: " . $currentDTO->getErrorsAsString();
                    Log::warning($errorMsg);
                    $skippedRows[] = $errorMsg;
                    $currentDTO = null;
                }
            } else {
                if ($currentDTO) {
                    $appendError = $this->appendDetailsToDTO($currentDTO, $row, $rowNum);
                    if ($appendError) {
                        $skippedRows[] = "Row $rowNum: $appendError";
                    }
                } else {
                    $skippedRows[] = "Row $rowNum: Detail row without parent product (no name_en found in previous rows)";
                    Log::warning("Row $rowNum: Detail row without parent product");
                }
            }
        }

        if ($currentDTO) {
            if ($currentDTO->hasErrors()) {
                $errorMsg = "Row {$currentDTO->rowNum}: " . $currentDTO->getErrorsAsString();
                Log::warning($errorMsg);
                $skippedRows[] = $errorMsg;
            } else {
                $groupedProducts[] = $currentDTO;
            }
        }

        if (! empty($skippedRows)) {
            Log::warning('Total skipped rows: '.count($skippedRows));
            foreach ($skippedRows as $skip) {
                Log::warning($skip);
            }
        }

        return [$groupedProducts, $skippedRows];
    }

    private function appendDetailsToDTO(ProductImportDTO $dto, array $row, int $rowNum = 0)
    {
        $errors = [];

        $specTitle = $row['spec_title'] ?? $row['spec_1_title'] ?? null;
        $specValue = $row['spec_value'] ?? $row['spec_1_value'] ?? null;

        if (! empty($specTitle) || ! empty($specValue)) {
            if (empty($specTitle)) {
                $errors[] = 'Spec has value but missing title';
            } elseif (empty($specValue)) {
                $errors[] = 'Spec has title but missing value';
            } else {
                $dto->specs[] = ['title' => $specTitle, 'value' => $specValue];
            }
        }

        $varPrice = $row['variant_price'] ?? $row['variant_1_price'] ?? null;

        if (! empty($varPrice)) {
            if (! is_numeric($varPrice) || floatval($varPrice) <= 0) {
                $errors[] = 'Invalid variant price: must be a positive number';
            } else {
                $varName = $row['variant_name_en'] ?? $row['variant_1_name_en'] ?? null;
                $finalVarName = $varName ?: ($dto->mainData['name_en'].' - Variant');

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
            $errors[] = 'Variant has name/SKU but missing price';
        }

        return empty($errors) ? null : implode('; ', $errors);
    }
}
