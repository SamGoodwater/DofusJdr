<?php

declare(strict_types=1);

namespace App\Services\Characteristics;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;

/**
 * Règles de cohérence par nature (descriptif, intrinsèque, bonus/malus, buff/débuff, valeur d'action).
 */
final class CharacteristicNatureQualityService
{
    /**
     * @return list<string>
     */
    public function issuesFor(Characteristic $characteristic): array
    {
        $nature = $characteristic->natureEnum();
        if ($nature === null) {
            return ['nature manquante'];
        }

        $issues = [];
        if ($nature->requiresIntrinsicLink() && $characteristic->intrinsic_characteristic_id === null) {
            $issues[] = 'lien intrinsic_characteristic_id requis pour '.$nature->value;
        }
        if (
            in_array($nature, [
                CharacteristicNature::Descriptif,
                CharacteristicNature::Intrinsic,
                CharacteristicNature::ActionValue,
            ], true)
            && $characteristic->intrinsic_characteristic_id !== null
        ) {
            $issues[] = 'intrinsic_characteristic_id interdit pour '.$nature->value;
        }

        if ($characteristic->intrinsic_characteristic_id !== null) {
            $target = $characteristic->relationLoaded('intrinsicCharacteristic')
                ? $characteristic->intrinsicCharacteristic
                : $characteristic->intrinsicCharacteristic()->first();
            if ($target === null) {
                $issues[] = 'intrinsèque introuvable';
            } elseif (($target->group ?? '') !== 'creature') {
                $issues[] = 'l\'intrinsèque liée doit être du groupe creature';
            } elseif ($target->natureEnum() !== CharacteristicNature::Intrinsic) {
                $issues[] = 'la cible du lien doit être de nature intrinsic';
            }
        }

        return $issues;
    }
}
