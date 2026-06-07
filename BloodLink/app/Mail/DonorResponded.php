<?php

namespace App\Mail;

use App\Models\DonorResponse;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonorResponded extends Mailable
{
    use Queueable, SerializesModels;

    public User $hospital;

    public DonorResponse $response;

    public function __construct(User $hospital, DonorResponse $response)
    {
        $this->hospital = $hospital;
        $this->response = $response;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A Donor Has Responded to Your Request - BloodLink',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.donor-responded',
        );
    }
}
