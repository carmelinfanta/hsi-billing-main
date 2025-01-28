<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillingInvoices extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $partner_name;
    public $invoice_number;
    public $invoice_date;
    public $invoice_price;
    public $invoice_link;

    public function __construct($partner_name, $invoice_number, $invoice_date, $invoice_price, $invoice_link)
    {
        $this->partner_name = $partner_name;
        $this->invoice_number = $invoice_number;
        $this->invoice_date = $invoice_date;
        $this->invoice_price = $invoice_price;
        $this->invoice_link = $invoice_link;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice from Clearlink Technologies LLC (Invoice#:' . $this->invoice_number . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.billing-invoices',
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
