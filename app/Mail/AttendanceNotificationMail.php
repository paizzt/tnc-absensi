<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $time;
    public $status;
    public $type; // 'Masuk' atau 'Pulang'
    public $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $time, $status, $type, $messageText = null)
    {
        $this->studentName = $studentName;
        $this->time = $time;
        $this->status = $status;
        $this->type = $type;
        $this->messageText = $messageText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Absensi ' . $this->type . ' - ' . $this->studentName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
