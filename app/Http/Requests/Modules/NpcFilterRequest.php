<?php

namespace App\Http\Requests\Modules;

use App\Models\Modules\Npc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;

class NpcFilterRequest extends FormRequest
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
            parent::rules(),
            [
                'classe_id' => ["integer", "nullable", "exists:classes,id"],
                'story' => ["string", "min:1", "max:5000", "nullable"],
                'historical' => ["string", "min:1", "max:5000", "nullable"],
                'age' => ["integer", "nullable"],
                'specialization_id' => ["integer", "nullable", "exists:specializations,id"],
                'size' => ["string", "min:1", "max:255", "nullable"]
            ]
        );
    }

    protected function prepareForValidation(): void
    {
        // Appeler la méthode parente
        parent::prepareForValidation();

        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "is_visible" => $this->input("is_visible") ?: true,
            'created_by' => Auth::user()->id,
            'size' => $this->size ?? "",
        ]);
    }
}
