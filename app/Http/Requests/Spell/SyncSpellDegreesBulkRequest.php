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
 * Enregistrement groupé de plusieurs degrés (UI enregistrement unique).
 */
class SyncSpellDegreesBulkRequest extends FormRequest
{
    use AuthorizesSpellDegreeMutation;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'degrees' => 'required|array',
            'degrees.*.id' => 'required|integer|exists:spell_degrees,id',
            'degrees.*.required_level' => 'nullable|integer|min:0|max:65535',
            'degrees.*.inherits_effects' => 'nullable|boolean',
            'degrees.*.properties_source' => ['nullable', 'string', Rule::in(SpellDegree::PROPERTIES_SOURCES)],
            'degrees.*.area' => ['nullable', 'string', 'max:64', new ValidAreaNotation],
            'degrees.*.pa' => 'nullable|string|max:64',
            'degrees.*.po_min' => 'nullable|string|max:64',
            'degrees.*.po_max' => 'nullable|string|max:64',
            'degrees.*.po_editable' => 'nullable|boolean',
            'degrees.*.sight_line' => 'nullable|boolean',
            'degrees.*.cast_in_line' => 'nullable|boolean',
            'degrees.*.cast_in_diagonal' => 'nullable|boolean',
            'degrees.*.cast_per_turn' => 'nullable|string|max:64',
            'degrees.*.cast_per_target' => 'nullable|string|max:64',
            'degrees.*.number_between_two_cast' => 'nullable|string|max:64',
            'degrees.*.global_cooldown' => 'nullable|integer|min:0|max:255',
            'degrees.*.max_stack' => 'nullable|integer|min:0|max:255',
            'degrees.*.duration' => 'nullable|string|max:255',
            'degrees.*.casting_time' => 'nullable|string|max:255',
            'degrees.*.effects' => 'sometimes|array',
            'degrees.*.effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'degrees.*.effects.*.order' => 'integer|min:0',
            'degrees.*.effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'degrees.*.effects.*.value_min' => 'nullable|integer',
            'degrees.*.effects.*.value_max' => 'nullable|integer',
            'degrees.*.effects.*.dice_num' => 'nullable|integer|min:0',
            'degrees.*.effects.*.dice_side' => 'nullable|integer|min:0',
            'degrees.*.effects.*.duration_formula' => 'nullable|string|max:255',
            'degrees.*.effects.*.logic_group' => 'nullable|string|max:64',
            'degrees.*.effects.*.logic_operator' => 'nullable|string|in:AND,OR',
            'degrees.*.effects.*.logic_condition' => 'nullable|string|max:255',
            'degrees.*.effects.*.params' => 'nullable|array',
            'degrees.*.effects.*.params.characteristic' => 'nullable|string|max:64',
            'degrees.*.effects.*.params.value_formula' => 'nullable|string|max:500',
            'degrees.*.effects.*.params.value_formula_crit' => 'nullable|string|max:500',
            'degrees.*.effects.*.params.life_steal_formula' => 'nullable|string|max:500',
            'degrees.*.effects.*.params.cells_formula' => 'nullable|string|max:500',
            'degrees.*.effects.*.params.movement_kind' => 'nullable|string|in:movement,jump,teleport,push,pull',
            'degrees.*.effects.*.params.creature_id' => 'nullable|integer|exists:creatures,id',
            'degrees.*.effects.*.params.monster_id' => 'nullable|integer|exists:monsters,id',
            'degrees.*.effects.*.params.condition_id' => 'nullable|integer|exists:conditions,id',
            'degrees.*.effects.*.params.condition_dofusdb_id' => 'nullable|integer|min:0',
            'degrees.*.effects.*.params.condition_name' => 'nullable|string|max:255',
            'degrees.*.effects.*.params.teleport' => 'nullable|boolean',
            'degrees.*.effects.*.params.dispellable' => 'nullable|boolean',
            'degrees.*.effects.*.crit_only' => 'nullable|boolean',
        ];
    }
}
