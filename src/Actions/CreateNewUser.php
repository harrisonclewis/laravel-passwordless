<?php

namespace Harlew\Passwordless\Actions;

use Harlew\Passwordless\Contracts\CreatesNewUser;
use Harlew\Passwordless\Models\Token;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateNewUser implements CreatesNewUser
{
    public function create(Token $token): Authenticatable
    {
        return config("passwordless.auth.model")::create([
            "name" => $this->name($token->email),
            "email" => $token->email,
            "password" => $this->password(),
            "email_verified_at" => now(),
        ]);
    }

    protected function name(string $email): string
    {
        return str($email)->before("@");
    }

    protected function password(): string
    {
        return Hash::make(Str::random(64));
    }
}
