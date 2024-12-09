<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class ClasseFilterRequest extends FormRequest
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
            'official_id' => ["string", "min:1", "max:255", "nullable"],
            'dofusdb_id' => ["string", "min:1", "max:255", "nullable"],
            'description_fast' => ["string", "min:4", "max:1000", "nullable"],
            'life' => ["integer", "required"],
            'life_dice' => ["integer", "min:4", "max:12", "required"],
            'specificity' => ["string", "min:4", "max:1000", "nullable"],
            'usable' => ["boolean"],
            'dofus_version' => ["string", "min:1", "max:255", "nullable"],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("classes", "uniqid")->ignore($this->route()->parameter('classe'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
            "icon" => FileRules::rules([FileRules::TYPE_IMAGE]),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "is_visible" => $this->input("is_visible") ?: true,
            'created_by' => Auth::user()->id,
            'usable' => $this->input("usable") ?: true,
            'life' => $this->input("life") ?: 0,
            'life_dice' => $this->input("life_dice") ?: 8,
            'dofus_version' => $this->input("dofus_version") ?: "3",
        ]);
    }
}
