<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Http\Requests\Concerns\ValidatesCreatureComposableCharacteristics;
use App\Http\Requests\Concerns\ValidatesProfileMasteryFields;
use App\Models\Entity\Monster;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateMonsterRequest extends FormRequest
{
    use GuardsPlayableState;
    use RestrictsAccessLevelMutation;
    use ValidatesCreatureComposableCharacteristics;
    use ValidatesProfileMasteryFields;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $model = $this->route('monster');

        return $model instanceof Monster && ($this->user()?->can('update', $model) === true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'size' => ['nullable', 'integer', 'min:0'],
            'is_boss' => ['nullable', 'boolean'],
            'boss_pa' => ['nullable', 'integer', 'min:0'],
            'monster_race_id' => ['nullable', 'integer', 'exists:monster_races,id'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['sometimes', 'integer', 'min:0', 'max:4'],
            'write_level' => ['sometimes', 'integer', 'min:0', 'max:4'],
            'level' => ['sometimes', 'nullable', 'string'],
        ], $this->creatureComposableFieldRules(), $this->profileMasteryFieldRules());
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
        return Monster::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
