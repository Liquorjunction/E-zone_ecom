<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Order;
use App\Services\InvoiceExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $order;
    public $status;
    public $reviewUrl;
    public $trackingUrl;

    public function __construct(User $user, Order $order, $status)
    {
        $this->user = $user;
        $this->order = $order;
        $this->status = strtolower($status);
        $this->reviewUrl = route('order-detail', $order->order_number);
        // Provide tracking URL for shipped and out_for_delivery mails
        if (in_array($this->status, ['shipped', 'out_for_delivery'])) {
            $this->trackingUrl = $order->tracking_url ?? null;
        } else {
            $this->trackingUrl = null;
        }
    }

    public function build()
    {
        $templateMap = [
            'order_confirmation' => 'client.emails.order_confirmed',
            'dispatched' => 'client.emails.order_dispatched',
            'delivered'  => 'client.emails.order_delivered',
            'cancelled'  => 'client.emails.order_cancelled',
            'order_placed' => 'client.emails.order_placed',
            'shipped' => 'client.emails.order_shipped',
            'out_for_delivery' => 'client.emails.order_outForDelivery',
        ];

        $view = $templateMap[$this->status] ?? 'client.emails.order_confirmed';

        $mail = $this->subject('Order ' . ucfirst(str_replace('_', ' ', $this->status)) . ' - #' . $this->order->order_number)
            ->view($view)
            ->with(['trackingUrl' => $this->trackingUrl]);

        if ($this->status === 'delivered') {
            $filename = 'invoice-' . ($this->order->order_number ?: ('ORDER-' . $this->order->id)) . '.pdf';

            try {
                $invoiceExportService = app(InvoiceExportService::class);
                $pdf = $invoiceExportService->renderInvoicePdf($this->order);
                $filename = $invoiceExportService->filename($this->order);

                $mail->attachData($pdf, $filename, ['mime' => 'application/pdf']);
            } catch (\Throwable $e) {
                // Keep attachment behavior resilient even if the full invoice template fails.
                Log::warning('Primary invoice PDF generation failed for delivered order email; using fallback PDF.', [
                    'order_id' => $this->order->id ?? null,
                    'order_number' => $this->order->order_number ?? null,
                    'error' => $e->getMessage(),
                ]);

                try {
                    $fallbackPdf = $this->buildFallbackInvoicePdf();
                    $mail->attachData($fallbackPdf, $filename, ['mime' => 'application/pdf']);
                } catch (\Throwable $fallbackError) {
                    Log::error('Fallback invoice PDF generation also failed for delivered order email.', [
                        'order_id' => $this->order->id ?? null,
                        'order_number' => $this->order->order_number ?? null,
                        'primary_error' => $e->getMessage(),
                        'fallback_error' => $fallbackError->getMessage(),
                    ]);
                }
            }
        }

        return $mail;
    }

        private function buildFallbackInvoicePdf(): string
        {
                $sym = config('app.CURRENCY_SYMBOL', '$');
                $total = money_format_number($this->order->total ?? 0, 2, '');
                $orderNumber = e($this->order->order_number ?: ('ORDER-' . $this->order->id));
                $customerName = e($this->user->name ?? 'Customer');
                $date = e(optional($this->order->created_at)->format('F j, Y, g:i A') ?: now()->format('F j, Y, g:i A'));

                $html = "<!doctype html>
<html>
<head>
    <meta charset='utf-8'>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 12px; }
        .wrap { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; }
        h1 { margin: 0 0 12px; font-size: 20px; }
        p { margin: 6px 0; }
    </style>
</head>
<body>
    <div class='wrap'>
        <h1>Invoice</h1>
        <p><strong>Order #:</strong> {$orderNumber}</p>
        <p><strong>Customer:</strong> {$customerName}</p>
        <p><strong>Date:</strong> {$date}</p>
        <p><strong>Total:</strong> {$sym} {$total}</p>
    </div>
</body>
</html>";

                $pdf = app('dompdf.wrapper');
                return $pdf->loadHTML($html)->setPaper('a4', 'portrait')->output();
        }
}
