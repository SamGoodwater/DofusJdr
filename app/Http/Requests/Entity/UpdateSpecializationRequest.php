<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\ValidatesProfileMasteryFields;
use App\Models\Entity\Specialization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'une Specialization.
 *
 * Valide les champs principaux d'une spécialisation.
 */
class UpdateSpecializationRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;
    use ValidatesProfileMasteryFields;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $model = $this->route('specialization');

        return $model instanceof Specialization && ($this->user()?->can('update', $model) === true);
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
            'short_description' => ['sometimes', 'nullable', 'string'],
            'description' => ['nullable', 'string'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            ...$this->profileMasteryFieldRules(),
        ];
    }

    protected function playableModelClass(): string
    {
        return Specialization::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
