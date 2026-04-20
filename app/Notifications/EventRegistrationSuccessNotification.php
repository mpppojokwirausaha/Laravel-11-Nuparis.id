<?php

namespace App\Notifications;

use App\Models\EventParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRegistrationSuccessNotification extends Notification
{
    use Queueable;

    public EventParticipant $participant;

    public function __construct(EventParticipant $participant)
    {
        $this->participant = $participant;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pendaftaran Berhasil - ' . $this->participant->event->event_title)
            ->view('emails.event-registration-success', [
                'participant' => $this->participant,
                'event' => $this->participant->event,
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
            'participant_name' => $this->participant->participant_name,
            'participant_email' => $this->participant->participant_email,
            'ticket_code' => $this->participant->ticket_code,
            'event_title' => $this->participant->event->event_title,
            'event_date' => $this->participant->event->event_date_start,
            'event_location' => $this->participant->event->event_location,
        ];
    }
}
