<?php

declare(strict_types=1);

namespace App\Support\Characteristics;

/**
 * Convention : fichier `{stem}-{group}-definition.json` sous
 * `database/seeders/data/characteristic-definitions/{group}/`
 * avec `stem` = clé sans suffixe `_creature` / `_object` / `_spell` / `_consumable` / `_profile`.
 */
final class CharacteristicDefinitionNaming
{
    public const RELATIVE_ROOT = 'database/seeders/data/characteristic-definitions';

    public const SUFFIX_CREATURE = '_creature';

    public const SUFFIX_OBJECT = '_object';

    public const SUFFIX_SPELL = '_spell';

    public const SUFFIX_CONSUMABLE = '_consumable';

    public const SUFFIX_PROFILE = '_profile';

    public const GROUP_CREATURE = 'creature';

    public const GROUP_OBJECT = 'object';

    public const GROUP_SPELL = 'spell';

    public const GROUP_CONSUMABLE = 'consumable';

    public const GROUP_PROFILE = 'profile';

    /**
     * @return list<string>
     */
    public static function groups(): array
    {
        return [
            self::GROUP_CREATURE,
            self::GROUP_OBJECT,
            self::GROUP_SPELL,
            self::GROUP_CONSUMABLE,
            self::GROUP_PROFILE,
        ];
    }

    /**
     * @return array{stem: string, group: string}|null
     */
    public static function parseCharacteristicKey(string $key): ?array
    {
        // Suffixes les plus longs d'abord (ex. _consumable avant un éventuel conflit).
        $suffixes = [
            self::SUFFIX_CONSUMABLE => self::GROUP_CONSUMABLE,
            self::SUFFIX_PROFILE => self::GROUP_PROFILE,
            self::SUFFIX_CREATURE => self::GROUP_CREATURE,
            self::SUFFIX_OBJECT => self::GROUP_OBJECT,
            self::SUFFIX_SPELL => self::GROUP_SPELL,
        ];
        foreach ($suffixes as $suffix => $group) {
            if (str_ends_with($key, $suffix)) {
                return [
                    'stem' => substr($key, 0, -strlen($suffix)),
                    'group' => $group,
                ];
            }
        }

        return null;
    }

    public static function characteristicKeyFromStemAndGroup(string $stem, string $group): string
    {
        return match ($group) {
            self::GROUP_CREATURE => $stem.self::SUFFIX_CREATURE,
            self::GROUP_OBJECT => $stem.self::SUFFIX_OBJECT,
            self::GROUP_SPELL => $stem.self::SUFFIX_SPELL,
            self::GROUP_CONSUMABLE => $stem.self::SUFFIX_CONSUMABLE,
            self::GROUP_PROFILE => $stem.self::SUFFIX_PROFILE,
            default => throw new \InvalidArgumentException('Groupe inconnu : '.$group),
        };
    }

    public static function definitionFilename(string $stem, string $group): string
    {
        return $stem.'-'.$group.'-definition.json';
    }

    public static function definitionRelativePath(string $stem, string $group): string
    {
        return self::RELATIVE_ROOT.'/'.$group.'/'.self::definitionFilename($stem, $group);
    }

    public static function definitionAbsolutePath(string $stem, string $group): string
    {
        return base_path(self::definitionRelativePath($stem, $group));
    }
}
