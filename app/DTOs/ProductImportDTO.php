<?php

namespace App\DTOs;

class ProductImportDTO
{
    public int $rowNum;
    public array $mainData;
    public array $specs = [];
    public array $variants = [];
    public bool $isBestseller = false;

    public function __construct(int $rowNum, array $mainData)
    {
        $this->rowNum = $rowNum;
        $this->mainData = $mainData;

        // Parse CSV value: accepts "1", "yes", "true", "on" as true
        $val = $mainData['is_bestseller'] ?? 0;
        $this->isBestseller = filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }
}
