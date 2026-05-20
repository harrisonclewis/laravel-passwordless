<x-mail::message>
# Login to your account

You are receiving this email because a login was requested for your account.

<x-mail::button :url="$url">
Sign in
</x-mail::button>

Or visit, {{ $url }}

This link will expire in {{ $expiresMinutes }} minutes.

If you did not request this link, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
