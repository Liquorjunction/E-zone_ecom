<?php

namespace App\Jobs\Concerns;

use App\DTOs\ProductImportDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ProductGrouper
{
    private function groupProducts($collection): array
    {
        Log::info('ProductGrouper: Starting row grouping process...');

        $groupedProducts = [];
        $currentDTO = null;
        $skippedRows = [];

        foreach ($collection as $index => $row) {
            $rowNum = $index + 2; // Adjust for header + 0-index

            // Normalize Row: Cast to array, lowercase keys, trim values
            $row = (array) $row;
            $row = array_change_key_case($row, CASE_LOWER);
            $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

            // --- START NEW PRODUCT ---
            if (! empty($row['name_en'])) {
                // Save previous DTO if exists
                if ($currentDTO) {
                    if ($currentDTO->hasErrors()) {
                        $errorMsg = "Row {$currentDTO->rowNum}: " . $currentDTO->getErrorsAsString();
                        Log::warning($errorMsg);
                        $skippedRows[] = $errorMsg;
                    } else {
                        $groupedProducts[] = $currentDTO;
                    }
                }

                // Create new DTO
                $currentDTO = new ProductImportDTO($rowNum, $row);

                if ($currentDTO->hasErrors()) {
                    $errorMsg = "Row {$currentDTO->rowNum}: " . $currentDTO->getErrorsAsString();
                    Log::warning($errorMsg);
                    $skippedRows[] = $errorMsg;
                    $currentDTO = null;
                }
                // Check if this row ITSELF contains variant data (Simple product or 1st variant inline)
                else {
                    $this->appendDetailsToDTO($currentDTO, $row, $rowNum);
                }
            }
            // --- APPEND TO CURRENT PRODUCT (Variant/Spec row) ---
            else {
                if ($currentDTO) {
                    $appendError = $this->appendDetailsToDTO($currentDTO, $row, $rowNum);
                    if ($appendError) {
                        $skippedRows[] = "Row $rowNum: $appendError";
                    }
                } else {
                    $skippedRows[] = "Row $rowNum: Orphan row (no parent product name_en found)";
                    Log::warning("Row $rowNum: Orphan row skipped");
                }
            }
        }

        // Add the last DTO
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
            Log::warning('ProductGrouper: Total skipped rows: ' . count($skippedRows));
        }

        return [$groupedProducts, $skippedRows];
    }

    private function appendDetailsToDTO(ProductImportDTO $dto, array $row, int $rowNum)
    {
        $errors = [];

        // 1. Parse Specs
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

        // 2. Parse Variants
        // We look for explicit variant columns.
        // Note: For simple products (on the main row), these might be empty, but 'price' is in mainData.
        $varName  = $row['variant_name_en'] ?? $row['variant_1_name_en'] ?? null;
        $varPrice = $row['variant_price'] ?? $row['variant_1_price'] ?? null;
        $varSku   = $row['variant_sku'] ?? null;
        // Support 'quantity', 'variant_quantity' or 'qty'
        $varQty   = $row['variant_quantity'] ?? $row['quantity'] ?? $row['qty'] ?? 0;

        // If explicit variant data exists
        if (!empty($varName) || !empty($varPrice) || !empty($varSku)) {
            if (empty($varPrice) || !is_numeric($varPrice) || floatval($varPrice) < 0) {
                $errors[] = 'Variant missing valid price';
            } else {
                // Auto-generate name if missing but price exists
                $finalVarName = $varName ?: ($dto->mainData['name_en'] . ' - Option');

                $dto->variants[] = [
                    'name_en'  => $finalVarName,
                    'price'    => $varPrice,
                    'sku'      => $varSku, // Nullable, repo will generate if null
                    'quantity' => is_numeric($varQty) ? $varQty : 0,
                ];
            }
        } elseif (!empty($varName) || !empty($varSku)) {
            // Partially filled variant row
            $errors[] = 'Variant has name/SKU but missing price';
        }

        return empty($errors) ? null : implode('; ', $errors);
    }
}
