<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemtypeFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            "name" => ["string", "min:4", "max:255", "required"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("itemtypes", "uniqid")->ignore($this->route()->parameter('itemtype'))],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
        ]);
    }
}
