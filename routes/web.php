<?php

use Harlew\Passwordless\Http\Controllers\PasswordlessController;
use Illuminate\Support\Facades\Route;

if (!config("passwordless.routes.enabled", true)) {
    return;
}

Route::middleware(config("passwordless.routes.middleware"))
    ->prefix(config("passwordless.routes.prefix", "passwordless"))
    ->name("passwordless.")
    ->group(function () {
        Route::post("/", [PasswordlessController::class, "store"])->name(
            "store",
        );

        Route::get("/{token}", [PasswordlessController::class, "show"])->name(
            "show",
        );
    });
