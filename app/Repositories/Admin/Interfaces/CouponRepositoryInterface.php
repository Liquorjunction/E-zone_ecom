<?php

namespace App\Repositories\Admin\Interfaces;

interface CouponRepositoryInterface
{
    public function getAllCoupons($filters = []);
    public function getActiveCoupons();
    public function getCouponById($id);
    public function storeCoupon($request, $data = null);
    public function updateCoupon($request, $id, $data = null);
    public function deleteCoupon($id);
    public function updateStatus($id, $status = null);
    public function getCouponTypeList();
    public function getDataForDataTable($filters = []);
}
