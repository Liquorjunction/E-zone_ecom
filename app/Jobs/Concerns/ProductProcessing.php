<?php

namespace App\Jobs\Concerns;

use App\DTOs\ProductImportDTO;

trait ProductProcessing
{
    private function processGroupedProducts($groupedProducts, array $skippedRows, $productRepo, $translationService, string $cacheKey): array
    {
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
                    $successfulProducts[] = $this->buildSuccessRecord($result['product']);
                }
            } else {
                $failed++;
                $errors[] = $result['message'];
                $failedProducts[] = $this->buildFailedRecord($dto, $result['message']);
            }

            if ($processed % 5 === 0 || $processed === $totalProducts) {
                $this->updateProgress($cacheKey, 'processing', $totalProducts, $processed, $success, $failed, $errors);
            }
        }

        foreach ($skippedRows as $skipMsg) {
            $failedProducts[] = [
                'name_en' => 'N/A',
                'sku' => 'N/A',
                'brand' => 'N/A',
                'category' => 'N/A',
                'error' => $skipMsg,
            ];
            $failed++;
            $errors[] = $skipMsg;
        }

        return [
            'total' => $totalProducts,
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
            'successful' => $successfulProducts,
            'failed_records' => $failedProducts,
        ];
    }

    private function buildSuccessRecord($product): array
    {
        $brandName = $product->brand ? ($product->brand->getTranslation('name', 'en') ?? $product->brand->name) : 'N/A';
        $categoryName = $product->category ? ($product->category->getTranslation('category-name', 'en') ?? $product->category->name) : 'N/A';

        return [
            'name_en' => $product->getTranslation('name', 'en'),
            'sku' => $product->sku,
            'brand' => $brandName,
            'category' => $categoryName,
            'id' => $product->id,
        ];
    }

    private function buildFailedRecord(ProductImportDTO $dto, string $message): array
    {
        return [
            'name_en' => $dto->mainData['name_en'] ?? 'Unknown',
            'sku' => $dto->mainData['sku'] ?? 'N/A',
            'brand' => $dto->mainData['brand'] ?? 'N/A',
            'category' => $dto->mainData['category'] ?? 'N/A',
            'error' => $message,
        ];
    }
}
