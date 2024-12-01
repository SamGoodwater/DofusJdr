<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Mob;
use Illuminate\Validation\Rule;

class MobFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            parent::validated(),
            [
                'official_id' => ["integer", "min:1", "max:255", "nullable"],
                'dofusdb_id' => ["integer", "min:1", "max:255", "nullable"],
                'size' => ["integer", Rule::in(Mob::SIZE), "nullable"],
                'dofus_version' => ["string", "min:1", "max:10", "nullable"],
            ]
        );
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'official_id' => $this->official_id ?? null,
            'dofusdb_id' => $this->dofusdb_id ?? null,
            'size' => $this->size ?? Mob::SIZE["moyenne"],
            'dofus_version' => $this->dofus_version ?? "3",
        ]);
    }
}
