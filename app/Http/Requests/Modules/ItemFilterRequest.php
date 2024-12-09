<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class ItemFilterRequest extends FormRequest
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
            'level' => ["integer", "min;0", "max:20", "required"],
            'effect' => ["string", "min:4", "max:1000", "nullable"],
            'bonus' => ["string", "min:4", "max:1000", "nullable"],
            'recepe' => ["string", "min:0", "max:1000", "nullable"],
            'price' => ["integer", "min:0", "max:1000000", "required"],
            'rarity' => ["integer", \App\Models\Modules\Item::RARITIES],
            'usable' => ["boolean"],
            'dofus_version' => ["string", "min:1", "max:255", "nullable"],
            'itemtype_id' => ["integer", "required", "exists:itemtypes,id"],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("items", "uniqid")->ignore($this->route()->parameter('item'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
            "auto_update" => ["boolean"],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "is_visible" => $this->input("is_visible") ?: true,
            'created_by' => Auth::user()->id,
            'usable' => $this->input("usable") ?: true,
            'level' => $this->input("level") ?: 1,
            'price' => $this->input("price") ?: 0,
            'rarity' => $this->input("rarity") ?: \App\Models\Modules\Item::RARITIES["Commun"],
            'dofus_version' => $this->input("dofus_version") ?: "3",
            'auto_update' => $this->input("auto_update") ?: true,
        ]);
    }
}
