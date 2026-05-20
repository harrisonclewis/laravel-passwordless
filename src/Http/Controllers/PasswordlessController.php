<?php

namespace Harlew\Passwordless\Http\Controllers;

use Harlew\Passwordless\Contracts\ConsumesToken;
use Harlew\Passwordless\Contracts\CreatesToken;
use Harlew\Passwordless\Contracts\SendsToken;
use Harlew\Passwordless\Http\Requests\CreateTokenRequest;
use Harlew\Passwordless\Models\Token;
use Illuminate\Routing\Controller;

class PasswordlessController extends Controller
{
    public function store(
        CreateTokenRequest $request,
        CreatesToken $createToken,
        SendsToken $sendToken,
    ) {
        $token = $createToken->create($request->validated());

        $sendToken->send($token);

        $flash = config("passwordless.flash");
        $data = [
            "sent" => true,
            "email" => $token->email,
        ];

        $request->session()->flash($flash, $data);

        if (class_exists(\Inertia\Inertia::class)) {
            \Inertia\Inertia::flash($flash, $data);
        }

        return redirect()->back();
    }

    public function show(Token $token, ConsumesToken $consumeToken)
    {
        if ($token->isExpired()) {
            abort(403);
        }

        if ($token->isConsumed()) {
            abort(410);
        }

        $consumeToken->consume($token);

        return response()->redirectTo(config("passwordless.redirect"));
    }
}
