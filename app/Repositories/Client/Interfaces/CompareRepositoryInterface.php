<?php

namespace App\Repositories\Client\Interfaces;

interface CompareRepositoryInterface
{
    public function getActiveVariantsByIds(array $ids);
    public function getActiveVariantById(int $id);
    public function getActiveIds(array $ids): array;
}
