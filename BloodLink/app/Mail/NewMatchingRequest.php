<?php

namespace App\Mail;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMatchingRequest extends Mailable
{
    use Queueable, SerializesModels;

    public User $donor;

    public BloodRequest $bloodRequest;

    public function __construct(User $donor, BloodRequest $bloodRequest)
    {
        $this->donor = $donor;
        $this->bloodRequest = $bloodRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Blood Request Matching Your Type - BloodLink',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-matching-request',
        );
    }
}
