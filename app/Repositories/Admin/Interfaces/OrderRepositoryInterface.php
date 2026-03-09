<?php

namespace App\Repositories\Admin\Interfaces;
use App\Models\Order;

interface OrderRepositoryInterface
{
    public function getAllOrders();
    public function getOrderById($id);
    public function getOrderItemById($id);
    public function getDataForDataTable();
    public function getCustomerNameByOrderId($orderId);
    public function findOrderWithRelations(int $orderId, array $relations = []);
    public function getOrderWithItemsAndInventory($orderId);
}
