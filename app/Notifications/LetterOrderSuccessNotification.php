<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LetterOrderSuccessNotification extends Notification
{
    use Queueable;
    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembelian Berhasil - ' . $this->order->order_product_name)
            ->view('emails.letter-order-success', [
                'order' => $this->order,
            ])
            ->withSymfonyMessage(function ($message) {
                $message->getHeaders()
                    ->addTextHeader('X-Mailer', 'PHP')
                    ->addTextHeader('Precedence', 'bulk')
                    ->addTextHeader('List-Unsubscribe', '<mailto:' . env('MAIL_FROM_ADDRESS') . '>');
            });
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->order_id,
            'customer_name' => $this->order->order_customer_name,
            'customer_email' => $this->order->order_customer_email,
            'product_name' => $this->order->order_product_name,
            'product_slug' => $this->order->order_product_slug,
            'gross_amount' => $this->order->order_gross_amount,
        ];
    }
}
