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

        // Validate required main product fields
        $this->validateMainData();

        // Parse CSV value: accepts "1", "yes", "true", "on" as true
        $val = $mainData['is_bestseller'] ?? 0;
        $this->isBestseller = filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Validate all required main product fields
     */
    private function validateMainData()
    {
        // Required fields that must exist and be non-empty
        $requiredFields = [
            'name_en' => 'Product Name (English)',
            'brand' => 'Brand',
            'category' => 'Category',
            'price' => 'Price',
        ];

        foreach ($requiredFields as $field => $label) {
            $value = $this->mainData[$field] ?? null;

            if (empty($value)) {
                $this->errors[] = "Missing required field: $label";
            } elseif ($field === 'price') {
                // Validate price is numeric and positive
                if (!is_numeric($value) || floatval($value) <= 0) {
                    $this->errors[] = "Invalid price value: must be a positive number";
                }
            } elseif ($field === 'name_en') {
                // Name too long
                if (strlen(strval($value)) > 255) {
                    $this->errors[] = "Product Name is too long (max 255 characters)";
                }
            }
        }

        // Optional but if provided, should be valid
        if (!empty($this->mainData['price']) && !is_numeric($this->mainData['price'])) {
            if (!in_array("Invalid price value: must be a positive number", $this->errors)) {
                $this->errors[] = "Price must be numeric";
            }
        }
    }

    /**
     * Check if DTO has any validation errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get all error messages
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get error messages as a single string
     */
    public function getErrorsAsString(): string
    {
        return implode('; ', $this->errors);
    }
}
