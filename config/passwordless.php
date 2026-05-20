<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redirects
    |--------------------------------------------------------------------------
    */

    "redirect" => env("PASSWORDLESS_REDIRECT", "/"),

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    "register" => env("PASSWORDLESS_REGISTER", true),

    /*
    |--------------------------------------------------------------------------
    | Flash
    |--------------------------------------------------------------------------
    |
    | Flash key used to indicate that a magic login link has been sent.
    |
    */
    "flash" => env("PASSWORDLESS_FLASH", "passwordless"),

    /*
    |--------------------------------------------------------------------------
    | Authentication guard
    |--------------------------------------------------------------------------
    */

    "auth" => [
        "provider" => env("PASSWORDLESS_AUTH_PROVIDER", null),
        "model" => env("PASSWORDLESS_AUTH_MODEL", null),
        "guard" => env("PASSWORDLESS_AUTH_GUARD", "web"),
    ],

    /*
    |--------------------------------------------------------------------------
    | Login token lifetime
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) a magic login link remains valid.
    |
    */

    "token_lifetime" => (int) env("PASSWORDLESS_TOKEN_LIFETIME", 900),

    /*
    |--------------------------------------------------------------------------
    | Login tokens table
    |--------------------------------------------------------------------------
    */

    "table" => env("PASSWORDLESS_TABLE", "login_tokens"),

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */

    "routes" => [
        "enabled" => env("PASSWORDLESS_ROUTES_ENABLED", true),
        "prefix" => env("PASSWORDLESS_ROUTE_PREFIX", "passwordless"),
        "middleware" => ["web", "throttle:6,1"],
    ],
];
