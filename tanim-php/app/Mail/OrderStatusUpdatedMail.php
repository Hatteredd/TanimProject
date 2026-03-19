<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?string $pdfPath = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Update — ' . $this->order->order_number . ' is now ' . ucfirst($this->order->status),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-status-updated');
    }

    public function attachments(): array
    {
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('receipt-' . $this->order->order_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
