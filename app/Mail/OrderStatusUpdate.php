<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

   public $order;
   public $previousStatus;
    public function __construct(Order $order, $previousStatus)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
    }

    public function build()
    {
         $statusMessages = [
            'pending' => 'en attente de confirmation',
            'confirmed' => 'confirmée',
            'preparing' => 'en préparation',
            'shipped' => 'expédiée',
            'delivered' => 'livrée',
            'cancelled' => 'annulée'
        ];

        $subject = 'Votre commande #' . $this->order->id . ' est ' . ($statusMessages[$this->order->status] ?? $this->order->status);

        return $this->subject($subject)
                    ->view('emails.orders.status-update')
                    ->with([
                        'order' => $this->order,
                        'user' => $this->order->user,
                        'statusMessage' => $statusMessages[$this->order->status] ?? $this->order->status,
                        'previousStatus' => $this->previousStatus
                    ]);
    }



    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Status Update',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
