<?php

namespace Harlew\Passwordless\Contracts;

use Harlew\Passwordless\Models\Token;

interface ConsumesToken
{
    public function consume(Token $token): void;
}
