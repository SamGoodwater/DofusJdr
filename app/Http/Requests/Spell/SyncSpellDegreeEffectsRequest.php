<?php

declare(strict_types=1);

namespace App\Http\Requests\Spell;

use App\Http\Requests\Spell\Concerns\AuthorizesSpellDegreeMutation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Synchronisation des effets d’un degré.
 */
class SyncSpellDegreeEffectsRequest extends FormRequest
{
    use AuthorizesSpellDegreeMutation;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'effects' => 'present|array',
            'effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'effects.*.order' => 'integer|min:0',
            'effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'effects.*.value_min' => 'nullable|integer',
            'effects.*.value_max' => 'nullable|integer',
            'effects.*.dice_num' => 'nullable|integer|min:0',
            'effects.*.dice_side' => 'nullable|integer|min:0',
            'effects.*.duration_formula' => 'nullable|string|max:255',
            'effects.*.logic_group' => 'nullable|string|max:64',
            'effects.*.logic_operator' => 'nullable|string|in:AND,OR',
            'effects.*.logic_condition' => 'nullable|string|max:255',
            'effects.*.params' => 'nullable|array',
            'effects.*.params.characteristic' => 'nullable|string|max:64',
            'effects.*.params.value_formula' => 'nullable|string|max:500',
            'effects.*.params.value_formula_crit' => 'nullable|string|max:500',
            'effects.*.params.life_steal_formula' => 'nullable|string|max:500',
            'effects.*.params.cells_formula' => 'nullable|string|max:500',
            'effects.*.params.movement_kind' => 'nullable|string|in:movement,jump,teleport,push,pull',
            'effects.*.params.creature_id' => 'nullable|integer|exists:creatures,id',
            'effects.*.params.monster_id' => 'nullable|integer|exists:monsters,id',
            'effects.*.params.condition_id' => 'nullable|integer|exists:conditions,id',
            'effects.*.params.condition_dofusdb_id' => 'nullable|integer|min:0',
            'effects.*.params.condition_name' => 'nullable|string|max:255',
            'effects.*.params.teleport' => 'nullable|boolean',
            'effects.*.params.dispellable' => 'nullable|boolean',
            'effects.*.crit_only' => 'nullable|boolean',
        ];
    }
}
