<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;
use App\Models\User;

class UserFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'name' => ["string", "min:1", "max:255", "required"],
            'email' => ["string", "email", "min:1", "max:255", "required"],
            'password' => ["string", "min:1", "max:255", "required"],
            'role' => ["string", [User::ROLES], "required"],
            'email_verified_at' => ["date", "nullable"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("sections", "uniqid")->ignore($this->route()->parameter('section'))],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "role" => $this->input("role") ?: User::ROLES["user"],
        ]);
    }
}
