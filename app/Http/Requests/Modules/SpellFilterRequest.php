<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class SpellFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'official_id' => ['string', 'min:1', 'max:255', 'nullable'],
            'dofusdb_id' => ['string', 'min:1', 'max:255', 'nullable'],
            'effect' => ['string', 'min:1', 'max:5000', 'nullable'],
            'effect_array' => ['array', 'nullable'],
            'area' => ['integer', \App\Models\Modules\Spell::AREAS, 'nullable'],
            'level' => ['integer', 'min:0', 'max:20', 'nullable'],
            'po' => ['string', 'min:0', 'max:100', 'nullable'],
            'po_editable' => ['boolean'],
            'pa' => ['integer', 'min:0', 'max:100', 'nullable'],
            'cast_per_turn' => ['integer', 'min:0', 'max:100', 'nullable'],
            'cast_per_target' => ['integer', 'min:0', 'max:100', 'nullable'],
            'sight_line' => ['boolean'],
            'number_between_two_cast' => ['integer', 'min:0', 'max:100', 'nullable'],
            'element' => ['integer', \App\Models\Modules\Spell::ELEMENTS, 'nullable'],
            'category' => ['integer', \App\Models\Modules\Spell::CATEGORIES, 'nullable'],
            'is_magic' => ['boolean'],
            'powerful' => ['integer', 'min:0', 'max:10', 'nullable'],
            'usable' => ['boolean'],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("spells", "uniqid")->ignore($this->route()->parameter('spell'))],
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
            'pa' => $this->input("pa") ?: 3,
            'cast_per_turn' => $this->input("cast_per_turn") ?: 1,
            'cast_per_target' => $this->input("cast_per_target") ?: 1,
            'number_between_two_cast' => $this->input("number_between_two_cast") ?: 0,
            'element' => $this->input("element") ?: \App\Models\Modules\Spell::ELEMENTS["Neutre"],
            'category' => $this->input("category") ?: \App\Models\Modules\Spell::CATEGORIES["Sort de créature"],
            'is_magic' => $this->input("is_magic") ?: false,
            'powerful' => $this->input("powerful") ?: 0,
            'po_editable' => $this->input("po_editable") ?: false,
            'auto_update' => $this->input("auto_update") ?: true,
        ]);
    }
}
