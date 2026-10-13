<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Support\Profile\ProfileMasteryColumns;

/**
 * Résout les variables `[X_mastery_profile]` pour les formules créature.
 *
 * Monstre : valeur de la fiche Monster.
 * PNJ : max(classe, spécialisation).
 */
final class ProfileMasteryResolver
{
    /**
     * @return array<string, int> clé caractéristique (ex. acrobatics_mastery_profile) → palier
     */
    public function resolveForCreature(Creature $creature): array
    {
        $out = [];
        foreach (ProfileMasteryColumns::all() as $column) {
            $out[$column.'_profile'] = 0;
        }

        $monster = $creature->relationLoaded('monster')
            ? $creature->monster
            : $creature->monster()->first();
        if ($monster instanceof Monster) {
            foreach (ProfileMasteryColumns::all() as $column) {
                $out[$column.'_profile'] = $this->clamp($column, (int) ($monster->{$column} ?? 0));
            }

            return $out;
        }

        $npc = $creature->relationLoaded('npc')
            ? $creature->npc
            : $creature->npc()->first();
        if (! $npc instanceof Npc) {
            return $out;
        }

        $npc->loadMissing(['breed', 'specialization']);
        foreach (ProfileMasteryColumns::all() as $column) {
            $breedVal = $npc->breed !== null ? (int) ($npc->breed->{$column} ?? 0) : 0;
            $specVal = $npc->specialization !== null ? (int) ($npc->specialization->{$column} ?? 0) : 0;
            $out[$column.'_profile'] = $this->clamp($column, max($breedVal, $specVal));
        }

        return $out;
    }

    private function clamp(string $column, int $value): int
    {
        return max(0, min(ProfileMasteryColumns::maxFor($column), $value));
    }
}
