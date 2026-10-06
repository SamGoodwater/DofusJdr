<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Models\Effect;
use App\Rules\ValidAreaNotation;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Création d’une définition d’effet déjà liée à un sort (fiche d’édition).
 */
class CreateSpellEffectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->verifyRole('game_master') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'spell_id' => 'required|integer|exists:spells,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'target_type' => 'nullable|string|in:direct,trap,glyph',
            'initial_area' => ['nullable', 'string', 'max:64', new ValidAreaNotation],
            'initial_degree_slug' => 'nullable|string|max:64',
            'initial_required_creature_level' => 'nullable|integer|min:0',

            // Sous-effets du premier degré (même forme que UpdateEffectGroupRequest).
            'initial_sub_effects' => 'nullable|array',
            'initial_sub_effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'initial_sub_effects.*.order' => 'integer|min:0',
            'initial_sub_effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'initial_sub_effects.*.value_min' => 'nullable|integer',
            'initial_sub_effects.*.value_max' => 'nullable|integer',
            'initial_sub_effects.*.dice_num' => 'nullable|integer|min:0',
            'initial_sub_effects.*.dice_side' => 'nullable|integer|min:0',
            'initial_sub_effects.*.duration_formula' => 'nullable|string|max:255',
            'initial_sub_effects.*.logic_group' => 'nullable|string|max:64',
            'initial_sub_effects.*.logic_operator' => 'nullable|string|in:AND,OR',
            'initial_sub_effects.*.logic_condition' => 'nullable|string|max:255',
            'initial_sub_effects.*.params' => 'nullable|array',
            'initial_sub_effects.*.params.characteristic' => 'nullable|string|max:64',
            'initial_sub_effects.*.params.value_formula' => 'nullable|string|max:500',
            'initial_sub_effects.*.params.value_formula_crit' => 'nullable|string|max:500',
            'initial_sub_effects.*.params.life_steal_formula' => 'nullable|string|max:500',
            'initial_sub_effects.*.params.cells_formula' => 'nullable|string|max:500',
            'initial_sub_effects.*.params.movement_kind' => 'nullable|string|in:movement,jump,teleport,push,pull',
            'initial_sub_effects.*.params.effect_direction' => 'nullable|string|in:bonus,malus,steal,action',
            'initial_sub_effects.*.params.element' => 'nullable|integer|min:0|max:6',
            'initial_sub_effects.*.params.dofus_element_id' => 'nullable|integer|min:0|max:6',
            'initial_sub_effects.*.params.value_converted' => 'nullable',
            'initial_sub_effects.*.params.life_steal_value_converted' => 'nullable',
            'initial_sub_effects.*.params.dice_formula' => 'nullable|string|max:64',
            'initial_sub_effects.*.params.monster_id' => 'nullable|integer|exists:monsters,id',
            'initial_sub_effects.*.params.condition_id' => 'nullable|integer|exists:conditions,id',
            'initial_sub_effects.*.params.condition_dofusdb_id' => 'nullable|integer|min:0',
            'initial_sub_effects.*.params.condition_name' => 'nullable|string|max:255',
            'initial_sub_effects.*.params.teleport' => 'nullable|boolean',
            'initial_sub_effects.*.params.dispellable' => 'nullable|boolean',
            'initial_sub_effects.*.crit_only' => 'nullable|boolean',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('description')) {
            $this->merge(['description' => (new EffectTextSanitizer)->sanitize((string) $this->description)]);
        }
        if (! $this->filled('target_type')) {
            $this->merge(['target_type' => Effect::TARGET_DIRECT]);
        }
    }
}
