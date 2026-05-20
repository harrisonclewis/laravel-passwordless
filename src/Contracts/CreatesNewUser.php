<?php

namespace Harlew\Passwordless\Contracts;

use Harlew\Passwordless\Models\Token;
use Illuminate\Contracts\Auth\Authenticatable;

interface CreatesNewUser
{
    public function create(Token $token): Authenticatable;
}
