<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Support\Profile\ProfileMasteryColumns;

/**
 * Validation souple des colonnes de maîtrise profil (breed, specialization, monster).
 */
trait ValidatesProfileMasteryFields
{
    /**
     * @return array<string, list<string|int>>
     */
    protected function profileMasteryFieldRules(): array
    {
        $rules = [];
        foreach (ProfileMasteryColumns::all() as $column) {
            $rules[$column] = [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:'.ProfileMasteryColumns::maxFor($column),
            ];
        }

        return $rules;
    }
}
