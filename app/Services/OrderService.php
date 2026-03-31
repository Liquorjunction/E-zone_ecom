<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\DB;
use App\Services\PaymentService;
use App\Services\CouponService;
use App\Services\MXPlusService;
use App\Services\SessionCouponService;
use App\Services\PriceCalculatorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class OrderService
{
    protected OrderRepositoryInterface $orders;
    protected CartRepositoryInterface $cartRepo;
    protected AddressRepositoryInterface $addressRepo;
    protected InventoryService $inventoryService;
    protected PriceCalculatorService $priceCalculator;
    protected LoggerInterface $logger;
    protected PaymentService $paymentService;
    protected CouponService $couponService;
    protected SessionCouponService $sessionCoupon;
    protected OrderStatusService $orderStatusService;

    protected MXPlusService $mXPlusService;

    public function __construct(
        OrderRepositoryInterface $orders,
        CartRepositoryInterface $cartRepo,
        AddressRepositoryInterface $addressRepo,
        InventoryService $inventoryService,
        PaymentService $paymentService,
        PriceCalculatorService $priceCalculator,
        CouponService $couponService,
        SessionCouponService $sessionCoupon,
        OrderStatusService $orderStatusService,
        LoggerInterface $logger,
        MXPlusService $mXPlusService
    ) {
        $this->orders = $orders;
        $this->cartRepo = $cartRepo;
        $this->addressRepo = $addressRepo;
        $this->inventoryService = $inventoryService;
        $this->priceCalculator = $priceCalculator;
        $this->paymentService = $paymentService;
        $this->couponService = $couponService;
        $this->sessionCoupon = $sessionCoupon;
        $this->orderStatusService = $orderStatusService;
        $this->logger = $logger;
        $this->mXPlusService = $mXPlusService;
    }

    public function processCheckout(Cart $cart, array $data, ?int $userId, ?string $userEmail)
    {
        $this->logger->info('Processing checkout', ['cart_id' => $cart->id, 'user_id' => $userId]);

        $data['payment_method'] = $data['payment_method'] ?? $data['payment'] ?? null;
        $data['shipping_method'] = $data['shipping_method'] ?? 'standard';
        $data['email'] = $data['email'] ?? $userEmail ?? null;

        $shippingAddress = $this->resolveShippingAddressData($data, $userId);

        $billingSame = (bool) ($data['billing_same_as_shipping'] ?? false);
        if ($billingSame) {
            $billingAddress = $shippingAddress;
        } else {
            $billingAddress = $this->resolveBillingAddressData($data, $shippingAddress, $userId);
        }

        return DB::transaction(function () use ($cart, $data, $userId, $shippingAddress, $billingAddress, $billingSame) {
            $shippingMethod = $data['shipping_method'] ?? 'standard';
            $pricing = $this->priceCalculator->calculate($cart, $shippingMethod);
            $shippingAddressRecord = $this->resolveShippingAddressRecord($data, $shippingAddress, $userId);
            $billingAddressRecord = $billingSame
                ? $shippingAddressRecord
                : $this->resolveBillingAddressRecord($data, $billingAddress, $userId);

            // Pull applied coupon from session BEFORE clearing cart/session.
            // We do this here so the coupon_id / coupon_code are saved on the order.
            $appliedCoupon = $this->sessionCoupon->getForCart($cart->id);

            $order = $this->orders->create([
                'user_id' => $userId,
                'subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['total_item_discount'],
                // Persist coupon info from session (not from cart DB columns – those are always null)
                'coupon_id' => $appliedCoupon['id'] ?? null,
                'coupon_code' => $appliedCoupon['code'] ?? null,
                'coupon_discount_amount' => $pricing['coupon_discount'],
                'shipping_method' => $shippingMethod,
                'shipping_cost' => $pricing['shipping_cost'],
                'tax_amount' => $pricing['tax_amount'] ?? 0,
                'tax_percentage' => $pricing['tax_rate'] ?? 0,
                'taxable_amount' => $pricing['taxable_amount'] ?? ($pricing['final_total'] ?? 0),
                'total' => $pricing['final_total'],
                'status' => 'pending',
                'shipping_address_id' => $shippingAddressRecord?->id,
                'billing_address_id' => $billingAddressRecord?->id,
                'phone_no' => $shippingAddress['phone'] ?? $data['phone_no'] ?? null,
                'email' => $data['email'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'order_number' => config('app.PREFIX') . '/ORD/' . uniqid(),
            ]);

            foreach ($cart->items as $item) {
                $this->orders->createItem([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->final_price ?? $item->unit_price,
                    'unit_price_original' => $item->unit_price ?? 0,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'line_subtotal' => round(($item->unit_price ?? 0) * $item->quantity, 2),
                    'line_discount' => round(($item->discount_amount ?? 0) * $item->quantity, 2),
                    'total_price' => round(($item->final_price ?? 0) * $item->quantity, 2),
                    'line_total' => round(($item->final_price ?? 0) * $item->quantity, 2),
                ]);

                $reserved = $this->inventoryService->reserveStock(
                    $item->product_id,
                    $item->variant_id,
                    $item->quantity,
                    'order',
                    $order->id,
                    $userId ?? 1
                );

                if ($reserved < $item->quantity) {
                    throw new \Exception('Insufficient stock available to complete this order.');
                }
            }

            // Pad order number with the real DB id now that we have it
            $this->orders->update($order->id, [
                'order_number' => config('app.PREFIX') . '/ORD/' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            ]);
            $order->refresh();
            // Send confirmation emails
            if (isset($order->user)) {
                $mailTrigger = app(\App\Services\MailTriggerService::class);
                $mailTrigger->sendOrderStatusUpdate($order, 'order_placed');
            }
            $mailTrigger = app(\App\Services\MailTriggerService::class);
            $mailTrigger->sendAdminNewOrderMail($order);

            $this->cartRepo->clearCart($cart);
            return $this->processPayment($order, $data, $appliedCoupon);
        });
    }

    protected function resolveShippingAddressData(array $data, ?int $userId): array
    {
        if ($userId && !empty($data['saved_address_id'])) {
            $saved = $this->addressRepo->findByIdAndUser((int) $data['saved_address_id'], $userId);
            if ($saved) {
                return $saved->toArray();
            }
        }

        if (!empty($data['shipping_address']) && is_array($data['shipping_address'])) {
            return $data['shipping_address'];
        }

        return [
            'name' => trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')) ?: null,
            'street' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['zip'] ?? $data['postal_code'] ?? null,
            'country' => $data['country'] ?? null,
            'phone' => $data['phone'] ?? $data['phone_no'] ?? null,
        ];
    }

    protected function resolveBillingAddressData(array $data, array $shippingAddress, ?int $userId): array
    {
        if ($userId && !empty($data['saved_billing_address_id'])) {
            $savedBilling = $this->addressRepo->findByIdAndUser((int) $data['saved_billing_address_id'], $userId);
            if ($savedBilling) {
                return $savedBilling->toArray();
            }
        }

        if (!empty($data['billing_address']) && is_array($data['billing_address'])) {
            return $data['billing_address'];
        }

        return [
            'name' => trim(($data['billing_firstName'] ?? '') . ' ' . ($data['billing_lastName'] ?? '')) ?: ($shippingAddress['name'] ?? null),
            'street' => $data['billing_address_line'] ?? null,
            'city' => $data['billing_city'] ?? null,
            'postal_code' => $data['billing_zip'] ?? null,
            'country' => $data['billing_country'] ?? null,
            'phone' => $data['billing_phone'] ?? ($shippingAddress['phone'] ?? null),
        ];
    }

    protected function resolveShippingAddressRecord(array $data, array $shippingAddress, ?int $userId)
    {
        if ($userId && !empty($data['saved_address_id'])) {
            return $this->addressRepo->findByIdAndUser((int) $data['saved_address_id'], $userId);
        }

        return $this->persistCheckoutAddress($shippingAddress, 'shipping', $userId, (bool) ($data['save_address'] ?? false));
    }

    protected function resolveBillingAddressRecord(array $data, array $billingAddress, ?int $userId)
    {
        if ($userId && !empty($data['saved_billing_address_id'])) {
            return $this->addressRepo->findByIdAndUser((int) $data['saved_billing_address_id'], $userId);
        }

        return $this->persistCheckoutAddress($billingAddress, 'billing', $userId, false);
    }

    protected function persistCheckoutAddress(array $address, string $type, ?int $userId, bool $attachToUser)
    {
        $payload = [
            'type' => $type,
            'user_id' => $attachToUser ? $userId : null,
            'address_label' => ucfirst($type),
            'name' => $address['name'] ?? '',
            'phone' => $address['phone'] ?? null,
            'street' => $address['street'] ?? '',
            'city' => $address['city'] ?? '',
            'postal_code' => $address['postal_code'] ?? null,
            'country' => $address['country'] ?? '',
        ];

        if ($attachToUser && $userId) {
            return $this->addressRepo->firstOrCreateAddress([
                'user_id' => $userId,
                'type' => $type,
                'street' => $payload['street'],
                'city' => $payload['city'],
                'postal_code' => $payload['postal_code'],
                'country' => $payload['country'],
            ], $payload);
        }

        return $this->addressRepo->create($payload);
    }

    public function cancelClientOrder(int $orderId, ?string $reason, ?int $userId)
    {
        $refundId = null;
        $order = $this->orders->findOrderWithRelations($orderId, ['items']);
        if (!$order || ($userId && $order->user_id !== $userId)) {
            return false;
        }

        if (in_array($order->status, ['cancelled', 'refunded'], true)) {
            return false;
        }

        if (!in_array(strtolower($order->status ?? ''), ['pending', 'paid'], true)) {
            return false;
        }
        $currentStatus = strtolower($order->status);
        if ($currentStatus === 'paid') {
            $refundData = [
                'orderid' => $order->order_number,
                'amount' => $order->total,
                'authnumber' => $order->naps_auth_number, // Ensure this column exists in DB
                'paymentid' => $order->transaction_id,   // Ensure this column exists in DB
                'firstName' => $order->shipping_address['name'] ?? 'Customer',
                'email' => $order->email,
            ];

            $apiResult = $this->mXPlusService->refund($refundData);

            // Handle nested response structure
            $res = $apiResult;

            // Status '07' or '00' usually indicates success in refund/status checks
            if (isset($res['statuscode']) && in_array($res['statuscode'], ['00', '07'])) {
                // Capture the new paymentid/refundid returned by NAPS
                $refundId = $res['refundid'] ?? $res['operation_id'] ?? null;
            } else {
                throw new \Exception("Refund failed: " . ($res['status'] ?? 'API Error'));
            }
        }

        DB::transaction(function () use ($order, $reason, $userId, $refundId) {
            foreach ($order->items as $item) {
                $this->inventoryService->releaseReservation(
                    $item->product_id,
                    $item->variant_id,
                    $item->quantity,
                    'Order Cancellation.' . ' Order ID: #' . $order->id,
                    $order->id,
                    $userId ?? 1
                );
            }

            // Determine final status
            $finalStatus = ($order->status === 'paid') ? 'refunded' : 'cancelled';

            // Update the Refund ID column ONLY if we got one from the API
            if ($refundId) {
                $updateFields['naps_refund_id'] = $refundId;
            }

            $updateFields['status'] = $finalStatus;

            $order->update($updateFields);

            // Record status history
            $this->orderStatusService->changeStatus($order, $finalStatus, $reason, false, null);

            //$this->orderStatusService->changeStatus($order, 'cancelled', $reason, false, null);
        });

        return true;
    }

    /**
     * Process payment and always clear the coupon session regardless of outcome.
     *
     * @param  array|null  $appliedCoupon  The coupon data captured before the cart was cleared.
     */
    protected function processPayment(Order $order, array $data, ?array $appliedCoupon)
    {
        // Record coupon usage if one was applied
        if ($appliedCoupon) {
            try {
                $this->couponService->recordUsage(
                    $appliedCoupon['id'],
                    $order->id,
                    $order->user_id
                );
            } catch (\Exception $e) {
                $this->logger->error('Failed to record coupon usage: ' . $e->getMessage());
            }
        }

        // Always clear the coupon session – even if recordUsage threw an exception above.
        $this->sessionCoupon->remove();

        try {
            $paymentResult = $this->paymentService->process($data, $order);

            if (!empty($paymentResult['success'])) {
                $order->payment_method = $data['payment_method'] ?? $data['payment'] ?? $order->payment_method;
                Log::error('SAVE FIELDS IF PAYMENT IS SUCCESS', [
                    'response' => $paymentResult,
                ]);

                $order->status = !empty($paymentResult['transaction_id']) ? 'paid' : 'pending';
                $order->transaction_id = $paymentResult['transaction_id'] ?? null;
                $order->naps_auth_number = $paymentResult['auth_number'] ?? null;
                $order->save();

                $this->logger->info('Payment processed', ['order_id' => $order->id, 'payment' => $paymentResult]);
            } else {
                $this->orders->update($order->id, ['status' => 'failed']);
                $order->status = 'failed';
                throw new \Exception('Payment failed: ' . ($paymentResult['message'] ?? 'unknown'));
            }
        } catch (\Throwable $e) {
            $this->orders->update($order->id, ['status' => $order->status ?? 'failed']);
            throw $e;
        }

        return $order;
    }
}
