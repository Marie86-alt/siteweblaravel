<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $type;

    public function __construct(Order $order, $type = 'confirmation')
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    /**
     * Determine if the notification should be queued.
     *
     * @return bool
     */

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
         $message = (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',');

        switch ($this->type) {
            case 'confirmation':
                return $message
                    ->subject('Confirmation de votre commande #' . $this->order->id)
                    ->line('Nous avons bien reçu votre commande.')
                    ->line('Numéro de commande : #' . $this->order->id)
                    ->line('Montant total : ' . number_format($this->order->total_amount, 2) . '€')
                    ->action('Voir ma commande', route('customer.orders.show', $this->order))
                    ->line('Merci pour votre confiance !');

            case 'status_update':
                return $message
                    ->subject('Mise à jour de votre commande #' . $this->order->id)
                    ->line('Le statut de votre commande a été mis à jour.')
                    ->line('Nouveau statut : ' . $this->getStatusLabel($this->order->status))
                    ->action('Voir ma commande', route('customer.orders.show', $this->order));

            default:
                return $message;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'type' => $this->type,
            'status' => $this->order->status,
            'amount' => $this->order->total_amount,
            'message' => $this->getNotificationMessage()
        ];
    }

    public function getStatusLabel($status)
    {
        $statusLabels = [
            'pending' => 'en attente de confirmation',
            'confirmed' => 'confirmée',
            'preparing' => 'en préparation',
            'shipped' => 'expédiée',
            'delivered' => 'livrée',
            'cancelled' => 'annulée'
        ];

        return $statusLabels[$status] ?? $status;
    }
    public function getNotificationMessage()
    {
        switch ($this->type) {
            case 'confirmation':
                return 'Votre commande #' . $this->order->id . ' a été confirmée.';
            case 'status_update':
                return 'Votre commande #' . $this->order->id . ' est maintenant ' . $this->getStatusLabel($this->order->status);
            default:
                return 'Mise à jour de votre commade #' . $this->order->id;
        }
    }
}
