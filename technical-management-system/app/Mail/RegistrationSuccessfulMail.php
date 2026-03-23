<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccessfulMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Successful - Gemarc Enterprises Inc',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-successful',
            with: [
                'user' => $this->user,
                'appName' => config('app.name', 'Technical Management System'),
            ],
        );
    }
}
