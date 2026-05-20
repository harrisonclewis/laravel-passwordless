<?php

namespace Harlew\Passwordless\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasUuids;

    protected $casts = [
        "id" => "string",
        "email" => "string",
        "remember" => "boolean",
        "expires_at" => "datetime",
        "consumed_at" => "datetime",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $fillable = ["email", "remember", "expires_at", "consumed_at"];

    public function getTable(): string
    {
        return config("passwordless.table");
    }

    public function findUser(): ?Authenticatable
    {
        return config("passwordless.auth.model")
            ::query()
            ->where("email", $this->email)
            ->first();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return (bool) $this->consumed_at;
    }

    public function url(): string
    {
        return route("passwordless.show", $this, false);
    }

    public function expiresInMinutes(): int
    {
        return (int) ceil(config("passwordless.token_lifetime") / 60);
    }
}
