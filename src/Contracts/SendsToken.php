<?php

namespace Harlew\Passwordless\Contracts;

use Harlew\Passwordless\Models\Token;

interface SendsToken
{
    public function send(Token $token): void;
}
