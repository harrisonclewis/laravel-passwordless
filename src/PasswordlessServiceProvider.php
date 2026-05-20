<?php

namespace Harlew\Passwordless;

use Harlew\Passwordless\Actions\ConsumeToken;
use Harlew\Passwordless\Actions\CreateNewUser;
use Harlew\Passwordless\Actions\CreateToken;
use Harlew\Passwordless\Actions\SendToken;
use Harlew\Passwordless\Contracts\ConsumesToken;
use Harlew\Passwordless\Contracts\CreatesNewUser;
use Harlew\Passwordless\Contracts\CreatesToken;
use Harlew\Passwordless\Contracts\SendsToken;
use Illuminate\Support\ServiceProvider;

class PasswordlessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . "/../config/passwordless.php",
            "passwordless",
        );

        $this->app->bind(CreatesToken::class, CreateToken::class);
        $this->app->bind(SendsToken::class, SendToken::class);
        $this->app->bind(ConsumesToken::class, ConsumeToken::class);
        $this->app->bind(CreatesNewUser::class, CreateNewUser::class);

        if (! config("passwordless.auth.model")) {
            $provider = config(
                "auth.guards.".config("passwordless.auth.guard").".provider",
                config("auth.defaults.provider"),
            );

            config([
                "passwordless.auth.model" => config(
                    "auth.providers.{$provider}.model",
                ),
            ]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ . "/../config/passwordless.php" => config_path(
                    "passwordless.php",
                ),
            ],
            "passwordless-config",
        );

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'passwordless');

        $this->publishes(
            [
                __DIR__.'/../resources/views' => resource_path(
                    'views/vendor/passwordless',
                ),
            ],
            'passwordless-views',
        );

        $this->loadRoutesFrom(__DIR__ . "/../routes/web.php");
    }
}
