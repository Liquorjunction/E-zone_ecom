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
            $rowNum = $index + 2;

            $row = (array) $row;
            $row = array_change_key_case($row, CASE_LOWER);
            $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

            // --- START NEW PRODUCT ---
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

        // Parse Specs
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

        // Parse Variants
        $varName  = $row['variant_name_en'] ?? $row['variant_1_name_en'] ?? null;
        $varPrice = $row['variant_price'] ?? $row['variant_1_price'] ?? null;
        $hasExplicitVariant = !empty($varName) || !empty($varPrice);
        $varBestRaw = $row['variant_is_bestseller']
            ?? $row['variant_1_is_bestseller']
            ?? $row['is_bestseller']
            ?? null;
        $varIsBestseller = false;
        if ($varBestRaw !== null && $varBestRaw !== '') {
            $varIsBestseller = filter_var($varBestRaw, FILTER_VALIDATE_BOOLEAN);
        }
        // Parse attributes from row
        $attributes = ['en' => []];
        $attributesFound = false;

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

        $temp = [];
        foreach ($row as $col => $val) {
            if (!is_string($col) || $val === null || $val === '') continue;
            if (preg_match('/^(?:attribute|attr)(?:_?(\d+))?_?(key|value)_?(en|fr|ar)$/i', $col, $m)) {
                $index = isset($m[1]) && $m[1] !== '' ? (int)$m[1] : 0;
                $type = strtolower($m[2]);
                $lang = strtolower($m[3]);
                if ($lang !== 'en') continue;
                if (! isset($temp[$lang][$index])) $temp[$lang][$index] = ['key' => '', 'value' => ''];
                $temp[$lang][$index][$type] = (string)$val;
            }
            elseif (preg_match('/^attributes_(en)_(key|value)$/i', $col, $m2)) {
                $lang = strtolower($m2[1]);
                $type = strtolower($m2[2]);
                $index = 0; // no index provided in this format; treat as first/only
                if (! isset($temp[$lang][$index])) $temp[$lang][$index] = ['key' => '', 'value' => ''];
                $temp[$lang][$index][$type] = (string)$val;
            }
        }

        foreach ($temp as $lang => $entries) {
            foreach ($entries as $entry) {
                if (! empty($entry['key']) || $entry['value'] !== '') {
                    $attributes[$lang][] = ['key' => (string)$entry['key'], 'value' => (string)$entry['value']];
                }
            }
        }

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

        if ($hasExplicitVariant) {
            if (empty($varPrice) || !is_numeric($varPrice) || floatval($varPrice) < 0) {
                $errors[] = 'Variant missing valid price';
            } else {
                $finalVarName = $varName ?: ($dto->mainData['name_en'] . ' - Option');

                $variantEntry = [
                    'name_en'  => $finalVarName,
                    'price'    => $varPrice,
                ];
                $variantEntry['is_bestseller'] = $varIsBestseller;
                if ($attributesFound) {
                    $variantEntry['attributes'] = $attributes;
                }
                $dto->variants[] = $variantEntry;
            }
        } elseif (!empty($varName)) {
            $errors[] = 'Variant has name but missing price';
        }

        if (!$hasExplicitVariant && $attributesFound) {
            if (!empty($dto->variants)) {
                $lastIndex = array_key_last($dto->variants);
                if ($lastIndex === null) $lastIndex = 0;
                $last = $dto->variants[$lastIndex];
                if (! isset($last['attributes']) || ! is_array($last['attributes'])) {
                    $last['attributes'] = ['en' => []];
                }
                $existing = $last['attributes']['en'] ?? [];
                $merged = array_values(array_merge($existing, $attributes['en']));
                $last['attributes']['en'] = $merged;
                if ($varBestRaw !== null && $varBestRaw !== '') {
                    $last['is_bestseller'] = $varIsBestseller;
                }
                $dto->variants[$lastIndex] = $last;
            } else {
                $dto->variants[] = [
                    'name_en' => $dto->mainData['name_en'],
                    'price' => $dto->mainData['price'] ?? 0,
                    'attributes' => $attributes,
                    'is_bestseller' => $varIsBestseller,
                ];
            }
        }

        return empty($errors) ? null : implode('; ', $errors);
    }
}
