<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\ValidatesProfileMasteryFields;
use App\Models\Entity\Breed;
use App\Models\Entity\BreedElementOrientation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la mise à jour d'une Breed (affichée « Classe »).
 */
class UpdateBreedRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;
    use ValidatesProfileMasteryFields;

    public function authorize(): bool
    {
        $model = $this->route('breed');

        return $model instanceof Breed && ($this->user()?->can('update', $model) === true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description_fast' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'mechanics' => ['nullable', 'string'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'symbol_full' => ['nullable', 'string', 'max:255'],
            'symbol_bw' => ['nullable', 'string', 'max:255'],
            'logo_male' => ['nullable', 'string', 'max:255'],
            'logo_female' => ['nullable', 'string', 'max:255'],
            'image_full_male' => ['nullable', 'string', 'max:255'],
            'image_full_female' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
        ], $this->elementOrientationRules(), $this->profileMasteryFieldRules());
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    private function elementOrientationRules(): array
    {
        $allowedKeys = config('breed_element_orientations.allowed_orientation_keys', []);
        $rules = [
            'element_orientations' => ['nullable', 'array'],
        ];
        foreach (BreedElementOrientation::ELEMENTS as $el) {
            $rules["element_orientations.{$el}"] = ['nullable', 'string', 'max:64', Rule::in($allowedKeys)];
        }

        return $rules;
    }

    protected function playableModelClass(): string
    {
        return Breed::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
