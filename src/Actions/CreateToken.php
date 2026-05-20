<?php

namespace Harlew\Passwordless\Actions;

use Harlew\Passwordless\Contracts\CreatesToken;
use Harlew\Passwordless\Models\Token;

class CreateToken implements CreatesToken
{
    public function create(array $input): Token
    {
        return Token::create([
            "email" => $input["email"],
            "remember" => $input["remember"] ?? false,
            "expires_at" => now()->addSeconds(
                config("passwordless.token_lifetime"),
            ),
        ]);
    }
}
