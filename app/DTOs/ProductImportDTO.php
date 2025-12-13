<?php

namespace App\DTOs;

class ProductImportDTO
{
    public int $rowNum;
    public array $mainData;
    public array $specs = [];
    public array $variants = [];
    public bool $isBestseller = false;
    public array $errors = [];

    public function __construct(int $rowNum, array $mainData)
    {
        $this->rowNum = $rowNum;
        $this->mainData = $mainData;
        $this->errors = [];

        $this->validateMainData();

        $val = $mainData['is_bestseller'] ?? 0;
        $this->isBestseller = filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    private function validateMainData()
    {
        $requiredFields = [
            'name_en' => 'Product Name (English)',
            'brand' => 'Brand',
            'category' => 'Category',
            'price' => 'Price', // This becomes the "Display Price" or "Default Variant Price"
        ];

        foreach ($requiredFields as $field => $label) {
            $value = $this->mainData[$field] ?? null;

            if (empty($value) && $value !== '0' && $value !== 0) {
                $this->errors[] = "Missing required field: $label";
            } elseif ($field === 'price') {
                if (!is_numeric($value) || floatval($value) < 0) {
                    $this->errors[] = "Invalid price value: must be a positive number";
                }
            } elseif ($field === 'name_en') {
                if (strlen(strval($value)) > 255) {
                    $this->errors[] = "Product Name is too long (max 255 characters)";
                }
            }
        }

        // Optional Quantity Check (if provided for simple product)
        if (isset($this->mainData['quantity']) && !is_numeric($this->mainData['quantity'])) {
            $this->errors[] = "Quantity must be numeric";
        }
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrorsAsString(): string
    {
        return implode('; ', $this->errors);
    }
}
