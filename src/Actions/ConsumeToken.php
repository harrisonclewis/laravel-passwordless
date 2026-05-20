<?php

namespace Harlew\Passwordless\Actions;

use Harlew\Passwordless\Contracts\ConsumesToken;
use Harlew\Passwordless\Contracts\CreatesNewUser;
use Harlew\Passwordless\Models\Token;
use Illuminate\Support\Facades\Auth;

class ConsumeToken implements ConsumesToken
{
    public function __construct(protected CreatesNewUser $createNewUser) {}

    public function consume(Token $token): void
    {
        $user = $token->findUser();

        if (!$user && config("passwordless.register")) {
            $user = $this->createNewUser->create($token);
        }

        if (!$user) {
            abort(401, "User not found");
        }

        Auth::guard(config("passwordless.auth.guard"))->login(
            $user,
            $token->remember,
        );

        $token->touch("consumed_at");
    }
}
