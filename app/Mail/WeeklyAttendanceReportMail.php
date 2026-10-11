<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use Carbon\Carbon;

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
        $startCarbon = Carbon::parse($this->startDate);
        $endCarbon = Carbon::parse($this->endDate);

        $startFormatted = $startCarbon->format('d/m/Y');
        $endFormatted = $endCarbon->format('d/m/Y');
        $periodText = ($startFormatted === $endFormatted)
            ? $startFormatted
            : ($startFormatted . ' s.d. ' . $endFormatted);

        $diffDays = $startCarbon->diffInDays($endCarbon) + 1;
        if ($diffDays == 1) {
            $periodType = 'Laporan Kehadiran Harian';
        } elseif ($diffDays >= 6 && $diffDays <= 7) {
            $periodType = 'Laporan Kehadiran Mingguan';
        } elseif ($diffDays >= 28 && $diffDays <= 31) {
            $periodType = 'Laporan Kehadiran Bulanan';
        } else {
            $periodType = 'Laporan Kehadiran Siswa';
        }

        return new Envelope(
            subject: "{$periodType}: {$this->student->name} ({$periodText}) - {$schoolName}",
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
