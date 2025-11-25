<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $karyawan;
    public $targetDate;

    public function __construct($karyawan, $targetDate)
    {
        $this->karyawan = $karyawan;
        $this->targetDate = $targetDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Waktunya Ajukan Jadwal MCU - ' . $this->karyawan->nama_karyawan,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission-reminder', // Template Blade baru
            with: [
                'nama' => $this->karyawan->nama_karyawan,
                'departemen' => $this->karyawan->departemen->nama_departemen ?? 'N/A',
                'tanggal_ref' => \Carbon\Carbon::parse($this->targetDate)->format('d F Y'),
            ],
        );
    }
}