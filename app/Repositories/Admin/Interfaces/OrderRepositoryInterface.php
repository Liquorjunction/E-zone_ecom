<?php

namespace App\Repositories\Admin\Interfaces;
use App\Services\OrderStatusService;

interface OrderRepositoryInterface
{
    public function getAllOrders();
    public function getOrderById($id);
    public function getOrderItemById($id);
    public function getStatusList(OrderStatusService $orderStatusService);
    public function getPaymentMethodsList();
    public function getDataForDataTable($request);
    public function getCustomerNameByOrderId($orderId);
    public function findOrderWithRelations(int $orderId, array $relations = []);
    public function getOrderWithItemsAndInventory($orderId);
}
