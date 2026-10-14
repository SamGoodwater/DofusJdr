<?php

declare(strict_types=1);

namespace App\Support\Characteristics;

use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicProfile;
use App\Models\CharacteristicSpell;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Liste canonique des groupes de caractéristiques et accès aux pivots associés.
 */
final class CharacteristicGroupRegistry
{
    /** @var list<string> */
    public const GROUPS = ['creature', 'object', 'spell', 'consumable', 'profile'];

    /**
     * @return list<string>
     */
    public static function groups(): array
    {
        return self::GROUPS;
    }

    public static function isValid(string $group): bool
    {
        return in_array($group, self::GROUPS, true);
    }

    /**
     * @return class-string<Model>
     */
    public static function modelClass(string $group): string
    {
        return match ($group) {
            'creature' => CharacteristicCreature::class,
            'object' => CharacteristicObject::class,
            'spell' => CharacteristicSpell::class,
            'consumable' => CharacteristicConsumable::class,
            'profile' => CharacteristicProfile::class,
            default => throw new \InvalidArgumentException("Groupe de caractéristique invalide : {$group}"),
        };
    }

    /**
     * @return Builder<Model>
     */
    public static function query(string $group): Builder
    {
        $class = self::modelClass($group);

        return $class::query();
    }

    /**
     * Cherche une ligne pivot pour une caractéristique, avec repli vers entity='*'.
     *
     * @return CharacteristicCreature|CharacteristicObject|CharacteristicSpell|CharacteristicConsumable|CharacteristicProfile|null
     */
    public static function findPivotRow(int $characteristicId, string $group, string $entity): mixed
    {
        $query = self::query($group)->where('characteristic_id', $characteristicId);
        $row = (clone $query)->where('entity', $entity)->first();
        if ($row !== null || $entity === '*') {
            return $row;
        }

        return (clone $query)->where('entity', '*')->first();
    }

    /**
     * Sans groupe explicite : premier pivot trouvé dans l'ordre des groupes.
     *
     * @return array{0: string, 1: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|CharacteristicConsumable|CharacteristicProfile}|null
     */
    public static function findFirstPivot(int $characteristicId, string $entity): ?array
    {
        foreach (self::GROUPS as $group) {
            $row = self::findPivotRow($characteristicId, $group, $entity);
            if ($row !== null) {
                return [$group, $row];
            }
        }

        return null;
    }
}
