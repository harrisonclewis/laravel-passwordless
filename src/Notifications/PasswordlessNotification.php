<?php

namespace Harlew\Passwordless\Notifications;

use Harlew\Passwordless\Mail\PasswordlessMailable;
use Harlew\Passwordless\Models\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;

class PasswordlessNotification extends Notification
{
    use Queueable;

    public function __construct(public Token $token) {}

    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    public function toMail(object $notifiable): Mailable
    {
        return new PasswordlessMailable($this->token)->to(
            $notifiable->routeNotificationFor("mail", $this) ??
                $this->token->email,
        );
    }
}
