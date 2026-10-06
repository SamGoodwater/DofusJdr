<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\HasCharacteristicValidation;
use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Models\Entity\Spell;
use App\Policies\Entity\SpellPolicy;
use App\Rules\ValidAreaNotation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Spell.
 *
 * Autorisation déléguée à {@see SpellPolicy::update} (auteur|admin).
 * Les min/max des champs liés aux caractéristiques (area, element, powerful, etc.)
 * sont dérivés de CharacteristicGetterService (entity spell).
 */
class UpdateSpellRequest extends FormRequest
{
    use GuardsPlayableState;
    use HasCharacteristicValidation;
    use RestrictsAccessLevelMutation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $spell = $this->route('spell');

        return $spell instanceof Spell && ($this->user()?->can('update', $spell) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'po_min' => ['nullable', 'string', 'max:64'],
            'po_max' => ['nullable', 'string', 'max:64'],
            'area' => ['nullable', 'string', 'max:64', new ValidAreaNotation],
            'po_editable' => ['nullable', 'boolean'],
            'pa' => ['nullable', 'string', 'max:255'],
            'casting_time' => ['nullable', 'string', 'max:255'],
            'ritual_available' => ['nullable', 'boolean'],
            'cast_per_turn' => ['nullable', 'string', 'max:255'],
            'cast_per_target' => ['nullable', 'string', 'max:255'],
            'sight_line' => ['nullable', 'boolean'],
            'cast_in_line' => $this->characteristicRules('cast_in_line', 'spell') ?: ['nullable', 'boolean'],
            'cast_in_diagonal' => $this->characteristicRules('cast_in_diagonal', 'spell') ?: ['nullable', 'boolean'],
            'target_type' => ['nullable', 'string', 'in:direct,trap,glyph'],
            'max_stack' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('max_stack', 'spell') ?: ['min:0', 'max:10']
            ),
            'global_cooldown' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('global_cooldown', 'spell') ?: ['min:0', 'max:10']
            ),
            'number_between_two_cast' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'element' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('element', 'spell') ?: ['min:0', 'max:127']
            ),
            'spellTypes' => ['nullable', 'array'],
            'spellTypes.*' => ['integer', 'exists:spell_types,id'],
            'category' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('category', 'spell')
            ),
            'is_magic' => ['nullable', 'boolean'],
            'powerful' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('powerful', 'spell')
            ),
            'resolution_mode' => ['nullable', 'string', 'in:attack_roll,saving_throw,auto_success'],
            'attack_characteristic_key' => ['nullable', 'string', 'max:64'],
            'save_characteristic_key' => ['nullable', 'string', 'max:64'],
            'save_dc_formula' => ['nullable', 'string', 'max:255'],
            'save_success_note' => ['nullable', 'string'],
            'auto_success_if_willing_target' => ['nullable', 'boolean'],
            'allows_reaction' => ['nullable', 'boolean'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
            /**
             * `stay` : retour HTTP sans changer d’URL (ex. modal liste) ; `index` : liste ;
             * `edit` : éditeur ; `show` : fiche lecture (défaut si absent).
             */
            'redirect_after_update' => ['nullable', 'string', 'in:stay,index,show,edit'],
        ];
    }

    protected function playableModelClass(): string
    {
        return Spell::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();

        // Formules PA / niveau : accepter un entier JSON (ex. 3) comme chaîne « 3 ».
        foreach (['level', 'pa', 'po_min', 'po_max', 'cast_per_turn', 'cast_per_target', 'number_between_two_cast', 'duration', 'casting_time'] as $key) {
            if (! $this->exists($key)) {
                continue;
            }
            $value = $this->input($key);
            if (is_int($value) || is_float($value)) {
                $this->merge([$key => (string) $value]);
            }
        }
    }
}
