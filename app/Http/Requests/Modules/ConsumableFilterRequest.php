<?php

namespace App\Http\Requests\Modules;

use App\Models\Modules\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class ConsumableFilterRequest extends FormRequest
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
            'effect' => ["string", "min:4", "max:1000", "nullable"],
            'level' => ["integer", "min;0", "max:20", "required"],
            'recepe' => ["string", "min:0", "max:1000", "nullable"],
            'price' => ["integer", "min:0", "max:1000000", "required"],
            'rarity' => ["integer", \App\Models\Modules\Item::RARITIES, "nullable"],
            'usable' => ["boolean"],
            'dofus_version' => ["string", "min:1", "max:255", "nullable"],
            'consumabletype_id' => ["integer", "required", "exists:consumabletypes,id"],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("consumables", "uniqid")->ignore($this->route()->parameter('consumable'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
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
        ]);
    }
}
