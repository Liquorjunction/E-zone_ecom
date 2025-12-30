<?php

namespace App\Repositories\Admin\Interfaces;

interface DiscountTargetRepositoryInterface
{
    public function getAll(array $filters = []);
    public function getById($id);
    public function addTargets($discountId, array $targets);
    public function updateTarget($id, array $data);
    public function deleteTarget($id);
    public function assignTargetsToDiscount($discountId, array $targets);
    public function removeTargetsFromDiscount($discountId, array $targets);
    public function updateStatus($id);
    public function setStatusByDiscountId($discountId, $status);
    public function getActiveDiscountTargets();
    public function getDiscountTargetsForDataTable(array $filters = []);
}
