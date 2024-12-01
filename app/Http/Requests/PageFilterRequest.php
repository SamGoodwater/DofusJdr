<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageFilterRequest extends FormRequest
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
            "name" => ["string", "min:4", "max:255", "required", Rule::unique("pages", "name")->ignore($this->route()->parameter('page'))],
            "keyword" => ["string", "nullable"],
            "slug" => ["string", "min:1", "max:255", "required", Rule::unique("pages", "slug")->ignore($this->route()->parameter('page')), "regex:/^[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*$/"],
            "order_num" => ["integer", "min:0"],
            "is_dropdown" => ["boolean"],
            "public" => ["boolean"],
            "is_editable" => ["boolean"],
            "page_id" => ["integer", "exists:pages,uniqid", "nullable"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("pages", "uniqid")->ignore($this->route()->parameter('page'))],
            "sections" => ["array", 'exists:sections,uniqid'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "slug" => $this->input("slug") ?: \Illuminate\Support\Str::slug($this->input("name")),
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "order_num" => $this->input("order_num") ?: 0,
            "is_dropdown" => $this->input("is_dropdown") ?: false,
            "public" => $this->input("public") ?: false,
            "is_editable" => $this->input("is_editable") ?: true,
            "page_id" => $this->input("page_id") ?: 0
        ]);
    }
}
