<?php

namespace App\Mail;

use App\Models\DonorResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonorConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public DonorResponse $response;

    public function __construct(DonorResponse $response)
    {
        $this->response = $response;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Donation Offer Has Been Accepted - BloodLink',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.donor-confirmed',
        );
    }
}
