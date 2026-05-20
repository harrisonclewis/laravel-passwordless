<?php

use Harlew\Passwordless\Actions\SendToken;
use Harlew\Passwordless\Mail\PasswordlessMailable;
use Harlew\Passwordless\Models\Token;
use Harlew\Passwordless\Notifications\PasswordlessNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('email');
    });
});

it('sends the magic login link notification', function () {
    Notification::fake();

    $token = Token::create([
        'email' => 'user@example.com',
        'remember' => false,
        'expires_at' => now()->addMinutes(15),
    ]);

    app(SendToken::class)->send($token);

    Notification::assertSentOnDemand(
        PasswordlessNotification::class,
        function (PasswordlessNotification $notification, array $channels, object $notifiable) use ($token) {
            expect($channels)->toBe(['mail'])
                ->and($notifiable->routes['mail'])->toBe('user@example.com')
                ->and($notification->token->is($token))->toBeTrue();

            $mailable = $notification->toMail($notifiable);

            expect($mailable)->toBeInstanceOf(PasswordlessMailable::class)
                ->and($mailable->token->url())->toContain($token->id);

            return true;
        },
    );
});

it('does not send a notification when the user is missing and registration is disabled', function () {
    Notification::fake();

    config(['passwordless.register' => false]);

    $token = Token::create([
        'email' => 'unknown@example.com',
        'remember' => false,
        'expires_at' => now()->addMinutes(15),
    ]);

    app(SendToken::class)->send($token);

    Notification::assertNothingSent();
});
