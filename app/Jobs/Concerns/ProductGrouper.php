<?php

namespace App\Jobs\Concerns;

use App\DTOs\ProductImportDTO;
use Illuminate\Support\Facades\Log;

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
        $hasExplicitVariant = !empty($varName) || !empty($varPrice);
        // Parse attributes from row (support multiple formats) — importer only passes English values.
        $attributes = ['en' => []];
        $attributesFound = false;

        // 1) JSON or semicolon-list column like 'attributes_en'
        $col = 'attributes_en';
        if (! empty($row[$col])) {
            $val = $row[$col];
            $decoded = json_decode($val, true);
            if (is_array($decoded)) {
                $list = [];
                if (array_values($decoded) !== $decoded) {
                    foreach ($decoded as $k => $v) {
                        $list[] = ['key' => (string)$k, 'value' => (string)$v];
                    }
                } else {
                    foreach ($decoded as $item) {
                        if (is_array($item) && (isset($item['key']) || isset($item['value']))) {
                            $list[] = $item;
                        } elseif (is_string($item)) {
                            $list[] = ['key' => '', 'value' => $item];
                        }
                    }
                }
                if (! empty($list)) {
                    $attributes['en'] = $list;
                    $attributesFound = true;
                }
            } else {
                // try semicolon-separated pairs 'color:Red;size:L'
                $pairs = preg_split('/\s*;\s*/', $val);
                $list = [];
                foreach ($pairs as $p) {
                    if (strpos($p, ':') !== false) {
                        [$k, $v] = array_map('trim', explode(':', $p, 2));
                    } else {
                        $k = '';
                        $v = trim($p);
                    }
                    if ($k !== '' || $v !== '') $list[] = ['key' => $k, 'value' => $v];
                }
                if (! empty($list)) {
                    $attributes['en'] = $list;
                    $attributesFound = true;
                }
            }
        }

        // 2) Patterned columns like attribute_1_key_en / attribute_1_value_en or attr_key_en
        $temp = [];
        foreach ($row as $col => $val) {
            if (!is_string($col) || $val === null || $val === '') continue;
            // Pattern 1: attribute[_N]_key|value[_lang]  (existing support)
            if (preg_match('/^(?:attribute|attr)(?:_?(\d+))?_?(key|value)_?(en|fr|ar)$/i', $col, $m)) {
                $index = isset($m[1]) && $m[1] !== '' ? (int)$m[1] : 0;
                $type = strtolower($m[2]);
                $lang = strtolower($m[3]);
                // Only capture English attribute patterned columns
                if ($lang !== 'en') continue;
                if (! isset($temp[$lang][$index])) $temp[$lang][$index] = ['key' => '', 'value' => ''];
                $temp[$lang][$index][$type] = (string)$val;
            }
            // Pattern 2: attributes_{lang}_key or attributes_{lang}_value (sample.csv format)
            elseif (preg_match('/^attributes_(en)_(key|value)$/i', $col, $m2)) {
                $lang = strtolower($m2[1]);
                $type = strtolower($m2[2]);
                $index = 0; // no index provided in this format; treat as first/only
                if (! isset($temp[$lang][$index])) $temp[$lang][$index] = ['key' => '', 'value' => ''];
                $temp[$lang][$index][$type] = (string)$val;
            }
            // Pattern 3: attributes_{lang} single-cell handled earlier (json/semicolon)
        }
        foreach ($temp as $lang => $entries) {
            // only 'en' entries will be present
            foreach ($entries as $entry) {
                if (! empty($entry['key']) || $entry['value'] !== '') {
                    $attributes[$lang][] = ['key' => (string)$entry['key'], 'value' => (string)$entry['value']];
                }
            }
        }

        // Clean and normalize attribute lists: trim and remove empty rows
        // Clean and normalize English attribute list
        $attributesFound = false;
        $clean = [];
        foreach ($attributes['en'] as $it) {
            $k = is_string($it['key']) ? trim($it['key']) : '';
            $v = is_string($it['value']) ? trim($it['value']) : (string)$it['value'];
            if ($k === '' && $v === '') continue;
            $clean[] = ['key' => $k, 'value' => $v];
        }
        $attributes['en'] = array_values($clean);
        if (count($attributes['en']) > 0) $attributesFound = true;

        // If explicit variant data exists
        if ($hasExplicitVariant) {
            if (empty($varPrice) || !is_numeric($varPrice) || floatval($varPrice) < 0) {
                $errors[] = 'Variant missing valid price';
            } else {
                // Auto-generate name if missing but price exists
                $finalVarName = $varName ?: ($dto->mainData['name_en'] . ' - Option');

                $variantEntry = [
                    'name_en'  => $finalVarName,
                    'price'    => $varPrice,
                    // SKU intentionally omitted: importer should not set SKU; repository will generate
                ];
                if ($attributesFound) {
                    $variantEntry['attributes'] = $attributes; // only contains 'en' entries
                }
                $dto->variants[] = $variantEntry;
            }
        } elseif (!empty($varName)) {
            // Partially filled variant row
            $errors[] = 'Variant has name but missing price';
        }

        // If this row contains only attributes (no explicit variant), attach them to last variant if exists,
        // otherwise create a default variant on the parent product.
        if (!$hasExplicitVariant && $attributesFound) {
            if (!empty($dto->variants)) {
                // Merge into last variant
                $lastIndex = array_key_last($dto->variants);
                if ($lastIndex === null) $lastIndex = 0;
                $last = $dto->variants[$lastIndex];
                // Ensure last has attributes structure
                if (! isset($last['attributes']) || ! is_array($last['attributes'])) {
                    $last['attributes'] = ['en' => []];
                }
                // append English attributes preserving order
                $existing = $last['attributes']['en'] ?? [];
                $merged = array_values(array_merge($existing, $attributes['en']));
                $last['attributes']['en'] = $merged;
                $dto->variants[$lastIndex] = $last;
            } else {
                $dto->variants[] = [
                    'name_en' => $dto->mainData['name_en'],
                    'price' => $dto->mainData['price'] ?? 0,
                    'attributes' => $attributes, // only 'en'
                ];
            }
        }

        return empty($errors) ? null : implode('; ', $errors);
    }
}
