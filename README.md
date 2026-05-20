<p align="center">
    <img alt="Laravel Passwordless" src="banner.png" width="100%">
</p>

# Introduction

Passwordless authentication for Laravel via email magic links. No passwords, no hassle. Users enter their email and receive a one-time login link.

## Installation

### Migrating an existing app with an AI agent

If you are replacing an existing Laravel auth flow, use the included [`PROMPT.md`](PROMPT.md) with your AI coding agent. It gives the agent package-specific instructions for installing this package, updating your auth UI, removing old password routes, preserving redirects and middleware, and verifying the magic-link flow.

Copy the contents of [`PROMPT.md`](PROMPT.md) into your AI coding agent.

### Install Package

```bash
composer require harrisonclewis/laravel-passwordless
```

Run migrations:

```bash
php artisan migrate
```

Optional — publish the configurations

```bash
php artisan vendor:publish --tag=passwordless-config
php artisan vendor:publish --tag=passwordless-views
```

## Usage

### Sending authentication link

```blade
<form method="POST" action="{{ route('passwordless.store') }}">
    @csrf
    <input type="email" name="email" placeholder="you@example.com" />
    <button type="submit">Login</button>
</form>

@if (session(config('passwordless.session.sent')))
    <p>Check your email for a login link.</p>
@endif
```

### Routes

The package registers two routes automatically:

| Method | URI | Description |
|--------|-----|-------------|
| `POST` | `/passwordless` | Accepts an email, creates and sends the magic link |
| `GET`  | `/passwordless/{token}` | Consumes the token and authenticates the user |

Point your login form at `route('passwordless.store')` and the rest is handled for you.

### Registration

By default, users who don't have an account are created automatically when they submit their email. Disable this if you want to restrict login to existing users only:

```php
// config/passwordless.php
'register' => false,
```

## Configuration

```php
// config/passwordless.php
return [
    'redirect'      => '/',           // Where to send the user after login
    'register'      => true,          // Auto-create users for unknown emails
    'token_lifetime' => 900,          // Link expiry in seconds (default: 15 min)
    
    ... others
];
```

## Requirements

- PHP ^8.1
- Laravel ^10.0|^11.0|^12.0|^13.0

## License

MIT
