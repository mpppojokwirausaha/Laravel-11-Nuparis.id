<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportExportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $emailSubject;
    protected string $emailBody;
    protected string $pdfContent;
    protected string $fileName;

    public function __construct(
        string $subject,
        string $body,
        string $pdfContent,
        string $fileName
    ) {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        $this->pdfContent = $pdfContent;
        $this->fileName = $fileName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-export',
            with: ['body' => $this->emailBody],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfContent, $this->fileName)
                ->withMime('application/pdf'),
        ];
    }
}
