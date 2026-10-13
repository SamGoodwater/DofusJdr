<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Http\Requests\Concerns\ValidatesCreatureComposableCharacteristics;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Models\Entity\Npc;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Mise à jour d’un PNJ (champs coquille + identité Creature).
 */
class UpdateNpcRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;
    use ValidatesCreatureComposableCharacteristics;

    public function authorize(): bool
    {
        $model = $this->route('npc');

        return $model instanceof Npc && ($this->user()?->can('update', $model) === true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'creature_id' => ['sometimes', 'nullable', 'integer', 'exists:creatures,id'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'hostility' => ['nullable', 'integer', 'min:0', 'max:4'],
            'story' => ['nullable', 'string'],
            'historical' => ['nullable', 'string'],
            'age' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'integer', CreatureSize::rule()],
            'npc_role' => ['nullable', 'string', NpcRole::rule()],
            'breed_id' => ['nullable', 'integer', 'exists:breeds,id'],
            'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:4'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:4'],
        ], $this->creatureComposableFieldRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $this->rejectUnauthorizedPlayableState($validator);
        });
        $validator->after(function (Validator $validator): void {
            $this->validateCreatureLevelField($validator);
            $this->validateCreatureComposableFields($validator);
        });
    }

    protected function playableModelClass(): string
    {
        return Npc::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
