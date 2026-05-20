<?php

namespace Harlew\Passwordless\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ["email" => "required|email", "remember" => "nullable|boolean"];
    }
}
