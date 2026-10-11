<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class WeeklyAttendanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $startDate;
    public $endDate;
    public $summary;
    public $dailyRecords;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, string $startDate, string $endDate, array $summary, array $dailyRecords)
    {
        $this->student = $student;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->summary = $summary;
        $this->dailyRecords = $dailyRecords;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $schoolName = $this->student->school ? $this->student->school->name : 'Sekolah';
        return new Envelope(
            subject: 'Laporan Kehadiran Mingguan: ' . $this->student->name . ' (' . date('d/m/Y', strtotime($this->startDate)) . ' - ' . date('d/m/Y', strtotime($this->endDate)) . ') - ' . $schoolName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly_report',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
