<?php

namespace Tests;

use Harlew\Passwordless\PasswordlessServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [PasswordlessServiceProvider::class];
    }
}
