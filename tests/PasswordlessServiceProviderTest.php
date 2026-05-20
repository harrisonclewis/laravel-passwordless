<?php

use Harlew\Passwordless\PasswordlessServiceProvider;

it('registers the service provider', function () {
    expect(app()->providerIsLoaded(PasswordlessServiceProvider::class))->toBeTrue();
});

it('merges the default config', function () {
    expect(config('passwordless.token_lifetime'))->toBe(900);
    expect(config('passwordless.table'))->toBe('login_tokens');
    expect(config('passwordless.routes.enabled'))->toBeTrue();
    expect(config('passwordless.routes.prefix'))->toBe('passwordless');
});

it('runs package migrations', function () {
    $this->artisan('migrate')->assertSuccessful();

    expect(\Illuminate\Support\Facades\Schema::hasTable(config('passwordless.table')))->toBeTrue();
});
