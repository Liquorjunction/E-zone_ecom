<?php

namespace App\DTOs;

class ProductImportDTO
{
    public int $rowNum;

    public array $mainData;

    public array $specs = [];

    public array $variants = [];

    public array $errors = [];

    public function __construct(int $rowNum, array $mainData)
    {
        $this->rowNum = $rowNum;
        $this->mainData = $mainData;
        $this->errors = [];

        $this->validateMainData();
    }

    private function validateMainData()
    {
        $requiredFields = [
            'name_en' => 'Product Name (English)',
            'brand' => 'Brand',
            'category' => 'Category',
        ];

        foreach ($requiredFields as $field => $label) {
            $value = $this->mainData[$field] ?? null;
            if (empty($value) && $value !== '0' && $value !== 0) {
                $this->errors[] = "Missing required field: $label";
            }
        }

        if (isset($this->mainData['price']) && $this->mainData['price'] !== '') {
            if (! is_numeric($this->mainData['price']) || floatval($this->mainData['price']) < 0) {
                $this->errors[] = 'Invalid price value: must be a positive number';
            }
        }

        if (isset($this->mainData['name_en']) && strlen(strval($this->mainData['name_en'])) > 255) {
            $this->errors[] = 'Product Name is too long (max 255 characters)';
        }
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    public function getErrorsAsString(): string
    {
        return implode('; ', $this->errors);
    }
}
