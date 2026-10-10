<?php

declare(strict_types=1);

namespace App\Http\Requests\Spell;

use App\Http\Requests\Spell\Concerns\AuthorizesSpellDegreeMutation;
use App\Models\SpellDegree;
use App\Rules\ValidAreaNotation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Création / mise à jour d’un degré de sort.
 */
class StoreSpellDegreeRequest extends FormRequest
{
    use AuthorizesSpellDegreeMutation;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'required_level' => 'nullable|integer|min:0|max:65535',
            'inherits_effects' => 'nullable|boolean',
            'properties_source' => ['nullable', 'string', Rule::in(SpellDegree::PROPERTIES_SOURCES)],
            'area' => ['nullable', 'string', 'max:64', new ValidAreaNotation],
            'pa' => 'nullable|string|max:64',
            'po_min' => 'nullable|string|max:64',
            'po_max' => 'nullable|string|max:64',
            'po_editable' => 'nullable|boolean',
            'sight_line' => 'nullable|boolean',
            'cast_in_line' => 'nullable|boolean',
            'cast_in_diagonal' => 'nullable|boolean',
            'cast_per_turn' => 'nullable|string|max:64',
            'cast_per_target' => 'nullable|string|max:64',
            'number_between_two_cast' => 'nullable|string|max:64',
            'global_cooldown' => 'nullable|integer|min:0|max:255',
            'max_stack' => 'nullable|integer|min:0|max:255',
            'duration' => 'nullable|string|max:255',
            'casting_time' => 'nullable|string|max:255',
        ], $this->effectsRules());
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function effectsRules(): array
    {
        return [
            'effects' => 'sometimes|array',
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
