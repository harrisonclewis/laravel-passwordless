<?php

namespace Harlew\Passwordless\Actions;

use Harlew\Passwordless\Contracts\SendsToken;
use Harlew\Passwordless\Models\Token;
use Harlew\Passwordless\Notifications\PasswordlessNotification;
use Illuminate\Support\Facades\Notification;

class SendToken implements SendsToken
{
    public function send(Token $token): void
    {
        $user = $token->findUser();

        if (!$user && !config("passwordless.register")) {
            return;
        }

        Notification::route("mail", $token->email)->notify(
            new PasswordlessNotification($token),
        );
    }
}
