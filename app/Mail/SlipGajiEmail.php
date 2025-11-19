<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SlipGajiEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    // 1. Terima data dari Controller
    public function __construct($data)
    {
        $this->data = $data;
    }

    // 2. Atur Subjek Email
    public function envelope(): Envelope
    {
        $bulan = date('F', mktime(0, 0, 0, $this->data['bulan'], 1));
        return new Envelope(
            subject: 'Slip Gaji Karyawan - Periode ' . $bulan . ' ' . $this->data['tahun'],
        );
    }

    // 3. Tentukan View dan Data yang dikirim ke View
    public function content(): Content
    {
        return new Content(
            view: 'admin.slip-gaji.print',
            with: [
                'slipData' => $this->data['data_gaji'],
            ],
        );
    }
}
