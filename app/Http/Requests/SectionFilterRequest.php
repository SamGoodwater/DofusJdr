<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SectionFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "component" => ["string", "min:4", "max:255", "required"],
            "title" => ["string", "min:4", "max:255", "required"],
            "content" => ["string", "nullable"],
            "order_num" => ["integer",],
            "is_visible" => ["boolean"],
            "page_id" => ["integer", "exists:pages,uniqid", "nullable"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("sections", "uniqid")->ignore($this->route()->parameter('section'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "order_num" => $this->input("order_num") ?: 0,
            "is_visible" => $this->input("is_visible") ?: true,
            "page_id" => $this->input("page_id") ?: null,
            'created_by' => Auth::user()->id,
        ]);
    }
}
