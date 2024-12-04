<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Creature;
use Illuminate\Support\Facades\Auth;

class CreatureFilterRequest extends FormRequest
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
            'uniqid' => ["string", "min:1", "max:255", "required", Rule::unique("creatures", "uniqid")->ignore($this->route()->parameter('creatures'))],
            'name' => ["string", "min:1", "max:255", "required", Rule::unique("creatures", "name")->ignore($this->route()->parameter('creatures'))],
            'description' => ["string", "nullable"],
            'location' => ["string", "nullable"],
            'level' => ["integer", "min:1", "max:20", "required"],
            'other_info' => ["string", "nullable"],
            'life' => ["string", "min:1", "max:1000", "nullable"],
            'pa' => ["string", "max:255", "nullable"],
            'pm' => ["string", "max:255", "nullable"],
            'po' => ["string", "max:255", "nullable"],
            'ini' => ["string", "max:255", "nullable"],
            'invocation' => ["string", "max:255", "nullable"],
            'touch' => ["string", "max:255", "nullable"],
            'ca' => ["string", "max:255", "nullable"],
            'dodge_pa' => ["string", "max:255", "nullable"],
            'dodge_pm' => ["string", "max:255", "nullable"],
            'fuite' => ["string", "max:255", "nullable"],
            'tacle' => ["string", "max:255", "nullable"],
            'vitality' => ["string", "max:255", "nullable"],
            'sagesse' => ["string", "max:255", "nullable"],
            'strong' => ["string", "max:255", "nullable"],
            'intel' => ["string", "max:255", "nullable"],
            'agi' => ["string", "max:255", "nullable"],
            'chance' => ["string", "max:255", "nullable"],
            'do_fixe_neutre' => ["string", "max:255", "nullable"],
            'do_fixe_terre' => ["string", "max:255", "nullable"],
            'do_fixe_feu' => ["string", "max:255", "nullable"],
            'do_fixe_air' => ["string", "max:255", "nullable"],
            'do_fixe_eau' => ["string", "max:255", "nullable"],
            'res_neutre' => ["string", "max:255", "nullable"],
            'res_terre' => ["string", "max:255", "nullable"],
            'res_feu' => ["string", "max:255", "nullable"],
            'res_air' => ["string", "max:255", "nullable"],
            'res_eau' => ["string", "max:255", "nullable"],
            'acrobatie_bonus' => ["string", "max:255", "nullable"],
            'discretion_bonus' => ["string", "max:255", "nullable"],
            'escamotage_bonus' => ["string", "max:255", "nullable"],
            'athletisme_bonus' => ["string", "max:255", "nullable"],
            'intimidation_bonus' => ["string", "max:255", "nullable"],
            'arcane_bonus' => ["string", "max:255", "nullable"],
            'histoire_bonus' => ["string", "max:255", "nullable"],
            'investigation_bonus' => ["string", "max:255", "nullable"],
            'nature_bonus' => ["string", "max:255", "nullable"],
            'religion_bonus' => ["string", "max:255", "nullable"],
            'dressage_bonus' => ["string", "max:255", "nullable"],
            'medecine_bonus' => ["string", "max:255", "nullable"],
            'perception_bonus' => ["string", "max:255", "nullable"],
            'perspicacite_bonus' => ["string", "max:255", "nullable"],
            'survie_bonus' => ["string", "max:255", "nullable"],
            'persuasion_bonus' => ["string", "max:255", "nullable"],
            'representation_bonus' => ["string", "max:255", "nullable"],
            'supercherie_bonus' => ["string", "max:255", "nullable"],
            'acrobatie_mastery' => ["string", "max:255", "nullable"],
            'discretion_mastery' => ["string", "max:255", "nullable"],
            'escamotage_mastery' => ["string", "max:255", "nullable"],
            'athletisme_mastery' => ["string", "max:255", "nullable"],
            'intimidation_mastery' => ["string", "max:255", "nullable"],
            'arcane_mastery' => ["string", "max:255", "nullable"],
            'histoire_mastery' => ["string", "max:255", "nullable"],
            'investigation_mastery' => ["string", "max:255", "nullable"],
            'nature_mastery' => ["string", "max:255", "nullable"],
            'religion_mastery' => ["string", "max:255", "nullable"],
            'dressage_mastery' => ["string", "max:255", "nullable"],
            'medecine_mastery' => ["string", "max:255", "nullable"],
            'perception_mastery' => ["string", "max:255", "nullable"],
            'perspicacite_mastery' => ["string", "max:255", "nullable"],
            'survie_mastery' => ["string", "max:255", "nullable"],
            'persuasion_mastery' => ["string", "max:255", "nullable"],
            'representation_mastery' => ["string", "max:255", "nullable"],
            'supercherie_mastery' => ["string", "max:255", "nullable"],
            'kamas' => ["string", "max:255", "nullable"],
            'drop_' => ["string", "max:2000", "nullable"],
            'other_item' => ["string", "max:2000", "nullable"],
            'other_consumable' => ["string", "max:2000", "nullable"],
            'other_spell' => ["string", "max:2000", "nullable"],
            'hostility' => ["integer", Rule::in(Creature::HOSTILITY), "required"],
            'usable' => ["boolean"],
            'created_by' => ["integer", "nullable", "exists:users,id"],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'uniqid' => $this->input('uniqid') ?: uniqid(),
            'name' => $this->input('name') ?: '',
            'level' => $this->input('level') ?: 1,
            'usable' => $this->input('usable') ?: true,
            "hostility" => $this->input('hostility') ?: Creature::HOSTILITY["neutre"],
            'created_by' => Auth::user()->id,
        ]);
    }
}
