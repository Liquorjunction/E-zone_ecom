<?php

namespace App\Services;

use App\Models\Order;

class InvoiceExportService
{
    public function __construct(private PriceCalculatorService $priceCalculator)
    {
    }

    public function renderInvoicePdf(Order $order): string
    {
        $order->loadMissing(['items.product', 'items.variant', 'user']);
        $totals = $this->priceCalculator->calculate($order);
        $sym = config('app.CURRENCY_SYMBOL', '$');
        $ship = is_array($order->shipping_address)
            ? $order->shipping_address
            : (json_decode($order->shipping_address ?? '[]', true) ?? []);
        $bill = is_array($order->billing_address)
            ? $order->billing_address
            : (json_decode($order->billing_address ?? '[]', true) ?? []);
        $siteContent = function_exists('get_site_setting') ? get_site_setting() : null;

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

        return $pdf->loadView('invoices.invoice-pdf', [
            'order' => $order,
            'totals' => $totals,
            'sym' => $sym,
            'ship' => $ship,
            'bill' => $bill,
            'siteContent' => $siteContent,
        ])->setPaper('a4', 'portrait')->output();
    }

    public function filename(Order $order): string
    {
        $orderNumber = $order->order_number ?: ('ORDER-' . $order->id);

        return 'invoice-' . str_replace(['/', '\\', ' '], '-', $orderNumber) . '.pdf';
    }
}
