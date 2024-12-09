<?php

namespace App\Http\Requests\Modules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileRules;
use App\Models\Modules\Spell;

class CapabilityFilterRequest extends FormRequest
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
        return [
            'effect' => ["string", "max:2000", "nullable"],
            'level' => ["integer", "min:0", "max:20"],
            'pa' => ["string", "max:255", "nullable"],
            'po' => ["string", "max:255", "nullable"],
            'po_editable' => ["boolean"],
            'time_before_use_again' => ["string", "max:255", "nullable"],
            'casting_time' => ["string", "max:255", "nullable"],
            'duration' => ["string", "max:255", "nullable"],
            'element' => ["integer", Spell::ELEMENTS],
            'is_magic' => ["boolean"],
            'ritual_available' => ["boolean"],
            'powerful' => ["integer", "min:0", "max:10"],
            'usable' => ['boolean'],
            "name" => ["string", "min:4", "max:255", "required"],
            "description" => ["string", "min:4", "max:1000", "nullable"],
            "is_visible" => ["boolean"],
            "uniqid" => ["string", "min:1", "max:255", "required", Rule::unique("capabilitys", "uniqid")->ignore($this->route()->parameter('capability'))],
            'created_by' => ["integer", "nullable", "exists:users,id"],
            "image" => FileRules::rules([FileRules::TYPE_IMAGE]),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "uniqid" => $this->input("uniqid") ?: uniqid(),
            "is_visible" => $this->input("is_visible") ?: true,
            'created_by' => Auth::user()->id,
            'element' => $this->input("element") ?: Spell::ELEMENT_NEUTRE,
            'is_magic' => $this->input("is_magic") ?: false,
            'ritual_available' => $this->input("ritual_available") ?: false,
            'powerful' => $this->input("powerful") ?: 0,
            'usable' => $this->input("usable") ?: false,
            'pa' => $this->input("pa") ?: 3,
            'po' => $this->input("po") ?: 0,
            'po_editable' => $this->input("po_editable") ?: false,
            'time_before_use_again' => $this->input("time_before_use_again") ?: 0,
            'casting_time' => $this->input("casting_time") ?: 0,
            'duration' => $this->input("duration") ?: 0,
            "level" => $this->input("level") ?: 1,
        ]);
    }
}
