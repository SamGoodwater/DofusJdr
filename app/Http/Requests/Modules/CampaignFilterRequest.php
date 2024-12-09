<?php

namespace App\Http\Requests\Modules;

use App\Models\Modules\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class CampaignFilterRequest extends FormRequest
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
            'keyword' => ["string", "min:4", "max:255", "nullable"],
            'slug' => ["string", "min:4", "max:255", "nullable"],
            'state' => ['boolean'],
            'is_public' => ['integer', Campaign::STATE],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("campaigns", "uniqid")->ignore($this->route()->parameter('campaign'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
            "file" => FileRules::rules([FileRules::TYPE_IMAGE, FileRules::TYPE_AUDIO, FileRules::TYPE_DOCUMENT, fileRules::TYPE_VIDEO])
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "is_visible" => $this->input("is_visible") ?: true,
            'created_by' => Auth::user()->id,
            'state' => $this->input("state") ?: Campaign::STATE['draft'],
            'is_public' => $this->input("is_public") ?: false,
            "slug" => $this->input("slug") ?: \Illuminate\Support\Str::slug($this->input("name")),
        ]);
    }
}
