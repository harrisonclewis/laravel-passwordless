# Laravel Passwordless

Passwordless authentication for Laravel via magic login links.

## Installation

```bash
composer require harrisonclewis/laravel-passwordless
```

Run migrations (included automatically):

```bash
php artisan migrate
```

Optional — publish config to customize defaults:

```bash
php artisan vendor:publish --tag=passwordless-config
```

## Configuration

```php
// config/passwordless.php
return [
    'redirect' => '/',
    'guard' => 'web',
    'token_lifetime' => 900,
    'table' => 'login_tokens',
    'routes' => [
        'enabled' => true,
        'prefix' => 'passwordless',
        'middleware' => ['web', 'throttle:6,1'],
    ],
];
```

## Requirements

- PHP ^8.1
- Laravel ^10.0|^11.0|^12.0|^13.0

## License

MIT
