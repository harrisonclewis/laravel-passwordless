<?php

namespace Harlew\Passwordless\Contracts;

use Harlew\Passwordless\Models\Token;
interface CreatesToken
{
    public function create(array $input): Token;
}
