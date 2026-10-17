<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Models\Entity\Condition;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConditionRequest extends FormRequest
{
    use GuardsPlayableState;
    use RestrictsAccessLevelMutation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $model = $this->route('condition');

        return $model instanceof Condition && ($this->user()?->can('update', $model) === true);
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
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'dissipable' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
            ...array_fill_keys(array_keys(Condition::MECHANICAL_FLAG_LABELS), ['sometimes', 'boolean']),
        ];
    }

    protected function playableModelClass(): string
    {
        return Condition::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
