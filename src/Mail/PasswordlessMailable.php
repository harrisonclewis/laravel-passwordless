<?php

namespace Harlew\Passwordless\Mail;

use Harlew\Passwordless\Models\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordlessMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Token $token) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config("app.name") . " - Login to your account",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: "passwordless::mail.passwordless",
            with: [
                "url" => $this->token->url(),
                "expiresMinutes" => $this->token->expiresInMinutes(),
            ],
        );
    }
}
