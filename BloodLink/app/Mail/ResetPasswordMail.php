<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $resetUrl;

    public function __construct(User $user, string $resetToken)
    {
        $this->user = $user;
        $this->resetUrl = route('password.reset', $resetToken);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password - BloodLink',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
