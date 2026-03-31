<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Tiket ' . $this->ticket->ticket_code . ' Berhasil Dibuat')
            ->view('emails.ticket-created', ['ticket' => $this->ticket])
            ->withSymfonyMessage(function ($message) {
                $message->getHeaders()
                    ->addTextHeader('X-Mailer', 'PHP')
                    ->addTextHeader('Precedence', 'bulk')
                    ->addTextHeader('List-Unsubscribe', '<mailto:' . env('MAIL_FROM_ADDRESS') . '>');
            });

        if ($this->ticket->ticket_document_support) {
            $mail->attach(
                storage_path('app/public/' . $this->ticket->ticket_document_support),
                ['as' => 'Lampiran_' . $this->ticket->ticket_code . '.' . pathinfo($this->ticket->ticket_document_support, PATHINFO_EXTENSION)]
            );
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_code'       => $this->ticket->ticket_code,
            'ticket_title'      => $this->ticket->ticket_title,
            'ticket_name_client'=> $this->ticket->ticket_name_client,
        ];
    }
}