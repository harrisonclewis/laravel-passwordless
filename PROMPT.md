# Replace Laravel Authentication With Passwordless Magic Links

**Copy everything below the line into your AI coding agent** to migrate an existing Laravel app from password-based authentication to [`harrisonclewis/laravel-passwordless`](https://github.com/harrisonclewis/laravel-passwordless).

The agent should install the package, wire the app's existing auth UI to it, remove obsolete password flows, and verify the result end to end.

---

## Mission

Replace this Laravel application's password login, registration, and password reset flow with email magic-link authentication using `harrisonclewis/laravel-passwordless`.

Do not build a second passwordless system. Use the package's routes, config, database table, notification, token model, and session flash key.

Preserve the existing app's:

- authenticated and guest route protection
- logout behavior
- intended/post-login redirect behavior where practical
- frontend stack and design conventions
- translations, validation display, and component patterns

Remove or disable old password entry points after the new flow works. There should not be two active login systems unless the user explicitly asks for a temporary fallback.

## First, Inspect The App

Before editing files, identify:

- Laravel version and auth stack: Breeze, Jetstream, Fortify, UI auth scaffolding, custom controllers, Livewire, Inertia React/Vue, Blade, API-only, or something else.
- Existing login, register, forgot-password, reset-password, email-verification, profile-password, and logout routes.
- Where unauthenticated users are redirected for login.
- Existing post-login redirect target, such as `/dashboard`, `RouteServiceProvider::HOME`, Fortify `home`, or custom controller logic.
- User model and provider used by the `web` guard.
- Whether unknown emails should create accounts. If this is not clear, ask the user before choosing.
- Mail configuration for local/staging testing.

Prefer the app's existing patterns. Keep diffs focused.

## Install

Run:

```bash
composer require harrisonclewis/laravel-passwordless
php artisan migrate
```

Publish config when you need to change defaults:

```bash
php artisan vendor:publish --tag=passwordless-config
```

Publish views only if the email template needs branding changes:

```bash
php artisan vendor:publish --tag=passwordless-views
```

Confirm mail is configured. In local development, use Mailpit, Mailhog, Log mail, or another test transport. A passwordless login cannot be tested without being able to inspect the magic-link email.

## Package Behavior To Use

The package registers these routes when `passwordless.routes.enabled` is true:

| Method | Route name | Default URI | Purpose |
| --- | --- | --- | --- |
| `POST` | `passwordless.store` | `/passwordless` | Validate email/remember, create token, send magic-link email |
| `GET` | `passwordless.show` | `/passwordless/{token}` | Consume token, authenticate the user, redirect |

The default route prefix is configurable with `passwordless.routes.prefix`.

`POST passwordless.store` accepts:

- `email`: required valid email
- `remember`: optional boolean

After a successful POST, the controller redirects back with this flash key:

```php
config('passwordless.session.sent') // default: 'passwordless_sent'
```

Use this exact key to show a confirmation message:

```php
session(config('passwordless.session.sent'))
```

When this flash key is present, hide the login form and show a sent-state message instead. The user should not see the email input and submit button again immediately after a successful request.

Use wording that prompts them to check their email, for example:

```text
Check your email for a login link.
```

If the frontend can reliably preserve the submitted email address without adding new package behavior, the message may include it:

```text
Check your email at user@example.com for a login link.
```

After the user clicks the email link, the package logs them in on:

```php
config('passwordless.auth.guard') // default: 'web'
```

Then it redirects to:

```php
config('passwordless.redirect') // default: '/'
```

Token failures:

- expired token: HTTP 403
- already consumed token: HTTP 410
- token for a missing user when registration is disabled: HTTP 401

If the app has custom error pages, make these states understandable to users.

## Configure `config/passwordless.php`

If config is published, set it to match the app:

```php
return [
    'redirect' => '/dashboard',
    'register' => true,

    'session' => [
        'sent' => 'passwordless_sent',
    ],

    'auth' => [
        'provider' => null,
        'model' => null,
        'guard' => 'web',
    ],

    'token_lifetime' => 900,
    'table' => 'login_tokens',

    'routes' => [
        'enabled' => true,
        'prefix' => 'passwordless',
        'middleware' => ['web', 'throttle:6,1'],
    ],
];
```

Notes:

- Leave `auth.model` as `null` unless the app needs an explicit model. The package resolves it from the configured guard's provider.
- Set `auth.guard` to the same guard used by the protected web routes.
- Set `redirect` to the app's real authenticated landing page.
- Set `register` deliberately:
  - `true`: unknown emails receive a link and the user is created when the token is consumed.
  - `false`: unknown emails do not receive a link, but the POST still redirects back with the generic sent flash to avoid account enumeration.
- Ensure the users table supports package-created users if registration is enabled. The default creator writes `name`, `email`, `password`, and `email_verified_at`; adjust the app or bind a custom `CreatesNewUser` implementation if required.

## Replace The Login UI

Keep the existing login page route if other parts of the app expect `route('login')`, but replace its content and submit target.

### Blade

Use the package route:

```blade
@if (session(config('passwordless.session.sent')))
    <p>Check your email for a login link.</p>
@else
    <form method="POST" action="{{ route('passwordless.store') }}">
        @csrf

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="email"
        >

        @error('email')
            <span>{{ $message }}</span>
        @enderror

        <label>
            <input type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <button type="submit">Login</button>
    </form>
@endif
```

Remove password fields, password validation, `LoginRequest`, and `Auth::attempt()` from the login path.

### Inertia React/Vue

- Submit to `route('passwordless.store')` with the existing Inertia form helper, `router.post()`, or Inertia v2 `<Form>`.
- Send `email` and optional `remember`.
- Remove password state, password validation display, password reset links, and password submit handlers.
- Share the package flash key from `HandleInertiaRequests` or equivalent middleware:

```php
'passwordlessSent' => session(config('passwordless.session.sent')),
```

- Display the sent state when `passwordlessSent` is truthy.
- Hide the login form after successful submission and show only the "check your email" message. If the component still has the submitted email in local state, the message may include it.
- No SPA route is needed for `passwordless.show`; link consumption is server-side.

### Livewire

Prefer a normal form POST to `route('passwordless.store')` when it fits the component.

If the component must handle submission itself, validate `email` and `remember`, then delegate to the package route/controller behavior rather than recreating token logic. Keep the sent state based on `session(config('passwordless.session.sent'))`, hide the form after success, and show a "check your email" message instead.

### API-only Or Non-Laravel Frontend

This package is web/session oriented. A separate frontend may POST to `passwordless.store` if it uses the Laravel web guard session and CSRF correctly. The emailed link should open the Laravel app URL for `passwordless.show`.

Do not convert this package into a bearer-token API flow.

## Remove Or Disable Old Password Features

Audit and update these areas:

| Area | Required change |
| --- | --- |
| Login controllers/actions | Remove `Auth::attempt()`, password validation, and password error paths. |
| Login forms/components | Keep only email and optional remember fields before submission; after success, hide the form and show the check-your-email state. |
| Registration | If `passwordless.register` is true, remove the public registration form or redirect it to login. If false, keep only the app's intentional invite/admin-created-user flow. |
| Forgot/reset password | Remove routes, controllers, pages, links, notifications, and tests unless legacy password login intentionally remains disabled-but-recoverable. |
| Profile password update | Remove from navigation/UI unless the app still has another password use case. |
| Navigation | Point login/register links to the passwordless login page. Remove forgot-password links. |
| Guest redirect | Ensure unauthenticated users still land on the passwordless login page. |
| Auth redirects | Ensure authenticated users land on the configured passwordless redirect or the app's intended destination. |
| Tests | Update feature tests to request a link and consume a token instead of posting a password. |

Keep logout. Keep `auth`/`guest` middleware. Keep email verification only if the product still needs it; package-created users are marked verified by default.

For Laravel 11+ apps, check `bootstrap/app.php` for auth middleware redirects. For older apps, check `app/Providers/RouteServiceProvider.php`, `app/Http/Middleware/Authenticate.php`, `RedirectIfAuthenticated`, Fortify config, or custom middleware.

## Auth Scaffold Guidance

### Breeze

- Keep the login route/view name if the app relies on it.
- Replace the login view/page with the magic-link form.
- Remove or disable `AuthenticatedSessionController::store()` password login logic.
- Remove register and password reset routes/pages unless intentionally retained for a non-login purpose.

### Jetstream / Fortify

- Do not bolt a new passwordless form beside Fortify's password form.
- Disable or override password login, registration, password reset, and profile password update features as needed.
- Point Fortify's login view to the magic-link form.
- Keep two-factor authentication only if there is still a coherent post-magic-link second factor flow.

### Laravel UI / Custom Auth

- Replace the existing login controller action and view with the package route.
- Remove password reset controllers/routes/views.
- Keep route names stable when possible to avoid breaking middleware redirects and links.

## Email Template

The default markdown view is:

```text
passwordless::mail.passwordless
```

After publishing views, customize:

```text
resources/views/vendor/passwordless/mail/passwordless.blade.php
```

Use the provided `$url` and `$expiresMinutes` variables. Do not hardcode the magic-link URL.

## Verification

Before finishing, run the relevant checks for the app, such as tests, linting, and static analysis. Then manually verify:

- `composer require harrisonclewis/laravel-passwordless` completed.
- Migrations ran and the `login_tokens` table exists, or the configured custom table exists.
- The login page shows email and optional remember fields only.
- Submitting the login page sends `POST route('passwordless.store')` with CSRF.
- Validation errors appear for an invalid email.
- Successful submission hides the login form and shows the sent-state message from `session(config('passwordless.session.sent'))`.
- A magic-link email is sent and contains a usable `passwordless.show` URL.
- Clicking the link logs the user in and redirects correctly.
- Reusing the same link returns HTTP 410 or the app's friendly equivalent.
- An expired link returns HTTP 403 or the app's friendly equivalent.
- Unknown-email behavior matches the chosen `passwordless.register` setting.
- Logout still works.
- Protected routes still require authentication.
- Guest-only routes still redirect authenticated users away from login.
- Old password login, register, forgot-password, and reset-password pages are no longer reachable unless intentionally retained.

## Constraints

- Keep changes minimal and idiomatic for the app.
- Do not commit `.env` secrets.
- Do not store raw login tokens outside the package table.
- Do not create a custom mailable, token model, controller, or route unless the package extension points are insufficient.
- Do not remove unrelated auth or authorization code.
- Ask the user only when a product decision is required, especially:
  - post-login destination is ambiguous
  - unknown emails should or should not create accounts
  - legacy password login must temporarily remain available
  - the app has multiple user providers/guards

## Final Report

When done, report:

- files changed
- config values set
- routes removed or changed
- tests/checks run and their results
- how to test the magic-link flow locally
