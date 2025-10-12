<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $orderItems;
    public $storeInfo;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $customer, $orderItems, $storeInfo = null)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->orderItems = $orderItems;
        $this->storeInfo = $storeInfo ?? [
            'name' => 'Minimoda',
            'email' => 'info@minimoda.id',
            'phone' => '+62 812 3456 7890',
            'address' => 'Jakarta, Indonesia',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice #' . $this->order->order_number . ' - Pesanan Anda di ' . $this->storeInfo['name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
