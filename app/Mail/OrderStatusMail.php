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
            try {
                $invoiceExportService = app(InvoiceExportService::class);
                $pdf = $invoiceExportService->renderInvoicePdf($this->order);
                $filename = $invoiceExportService->filename($this->order);

                $mail->attachData($pdf, $filename, ['mime' => 'application/pdf']);
            } catch (\Throwable $e) {
                // Do not block order delivered emails if PDF generation fails.
                Log::warning('Unable to attach invoice PDF to delivered order email.', [
                    'order_id' => $this->order->id ?? null,
                    'order_number' => $this->order->order_number ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $mail;
    }
}
