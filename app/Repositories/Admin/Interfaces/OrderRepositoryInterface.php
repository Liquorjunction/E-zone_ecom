<?php

namespace App\Repositories\Admin\Interfaces;
use App\Models\Order;

interface OrderRepositoryInterface
{
    public function getAllOrders();
    public function getOrderById($id);
    public function updateOrderStatus($id, $status);
    public function getDataForDataTable();
    public function getCustomerNameByOrderId($orderId);
}
