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
    public function removeTargetsFromDiscount($discountId, array $targetIds);
    // status columns are not present on discount_targets table in this project
    // so status-related operations are not supported here.
    public function getDiscountTargetsForDataTable(array $filters = []);
}
