<?php

declare(strict_types=1);

namespace App\Http\Requests\Spell\Concerns;

use App\Models\Entity\Spell;
use App\Policies\Entity\SpellPolicy;

/**
 * Mutations de degrés : même droit que {@see SpellPolicy::update}.
 */
trait AuthorizesSpellDegreeMutation
{
    public function authorize(): bool
    {
        $spell = $this->route('spell');

        return $spell instanceof Spell && ($this->user()?->can('update', $spell) ?? false);
    }
}
