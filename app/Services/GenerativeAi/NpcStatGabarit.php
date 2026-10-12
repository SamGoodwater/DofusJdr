<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Norms\NormsResolver;
use App\Support\ElementBitmask;
use App\Support\Npc\NpcRole;

/**
 * Gabarit PNJ / monstre selon le niveau et le rôle, lu depuis les normes caractéristiques.
 *
 * @example
 * $stats = app(NpcStatGabarit::class)->forLevelAndRole(4, NpcRole::SOCIAL);
 */
final class NpcStatGabarit
{
    public function __construct(
        private readonly ?CharacteristicDefinitionLookup $getter = null,
        private readonly ?NormsResolver $norms = null,
    ) {}

    /**
     * @return array{
     *     life: string,
     *     pa: string,
     *     pm: string,
     *     po: string,
     *     ca: string,
     *     ini: string,
     *     touch: string,
     *     vitality: string,
     *     sagesse: string,
     *     strong: string,
     *     intel: string,
     *     agi: string,
     *     chance: string,
     *     damage_dice: string,
     *     band: string
     * }
     */
    public function forLevelAndRole(int $level, string $role): array
    {
        $level = max(1, min(20, $level));
        $powerIndex = $this->powerIndexForRole($role);
        $resolver = $this->norms ?? new NormsResolver;

        $life = $this->normValue('life_points_creature', $level, $powerIndex, $resolver) ?? 10;
        $pa = $this->normValue('action_points_creature', $level, $powerIndex, $resolver) ?? 6;
        $pm = $this->normValue('movement_points_creature', $level, $powerIndex, $resolver) ?? 3;
        $ca = $this->normValue('armor_class_creature', $level, $powerIndex, $resolver) ?? 10;
        $hitDice = $this->normValue('hit_dice_creature', $level, $powerIndex, $resolver) ?? 1;
        $modVit = $this->normValue('modifier_vitality_creature', $level, $powerIndex, $resolver) ?? 0;

        $social = in_array($role, [NpcRole::SOCIAL, NpcRole::MERCHANT], true);
        $vitalityScore = 10 + (int) round((float) $modVit) * 2;
        if ($social) {
            $vitalityScore = max(8, $vitalityScore - 2);
        } elseif ($role === NpcRole::GUARD) {
            $vitalityScore = min(14, $vitalityScore + 2);
        }

        $base = 8;
        $primary = $social ? 12 : 14;
        $stats = [
            'vitality' => (string) $vitalityScore,
            'sagesse' => (string) $base,
            'strong' => (string) $base,
            'intel' => (string) $base,
            'agi' => (string) $base,
            'chance' => (string) $base,
        ];
        match ($role) {
            NpcRole::SOCIAL => $stats['chance'] = (string) $primary,
            NpcRole::MERCHANT => $stats['intel'] = (string) $primary,
            NpcRole::GUARD, NpcRole::ENEMY => $stats['strong'] = (string) $primary,
            NpcRole::ALLY => $stats['intel'] = (string) 12,
            default => $stats['strong'] = (string) 10,
        };

        $diceCount = max(1, (int) round((float) $hitDice));

        return array_merge($stats, [
            'life' => (string) max(1, (int) round((float) $life)),
            'pa' => (string) max(1, (int) round((float) $pa)),
            'pm' => (string) max(1, (int) round((float) $pm)),
            'po' => '1',
            'ca' => (string) max(1, (int) round((float) $ca)),
            'ini' => (string) max(1, (int) round((float) $ca)),
            'touch' => (string) max(1, (int) round((float) $ca)),
            'damage_dice' => $diceCount.'d6',
            'band' => NormsResolver::POWER_LEVELS[$powerIndex],
        ]);
    }

    /**
     * Écarts d’un jet de stats par rapport aux normes (PV ±25 %, PA ±1).
     *
     * @param  array<string, mixed>  $proposed
     * @return list<string>
     */
    public function validateStats(array $proposed, int $level, string $role): array
    {
        $errors = [];
        $expected = $this->forLevelAndRole($level, $role);
        $wantLife = (int) $expected['life'];
        $minLife = (int) max(1, floor($wantLife * 0.75));
        $maxLife = (int) ceil($wantLife * 1.25);

        if (array_key_exists('life', $proposed) && is_numeric($proposed['life'])) {
            $life = (int) $proposed['life'];
            if ($life < $minLife || $life > $maxLife) {
                $errors[] = "PV hors gabarit {$expected['band']} ({$minLife}–{$maxLife}, reçu {$life}).";
            }
        }

        if (array_key_exists('pa', $proposed) && is_numeric($proposed['pa'])) {
            $pa = (int) $proposed['pa'];
            $want = (int) $expected['pa'];
            if (abs($pa - $want) > 1) {
                $errors[] = "PA hors gabarit (attendu {$want} ±1, reçu {$pa}).";
            }
        }

        return $errors;
    }

    /**
     * Carac d’attaque dominante d’une liste de sorts (Terre → Force, etc.).
     *
     * @param  iterable<int, object|array<string, mixed>>  $spells
     *
     * @example $key = (new NpcStatGabarit)->dominantPrimaryStatKey($spells);
     */
    public function dominantPrimaryStatKey(iterable $spells): ?string
    {
        $counts = [];
        foreach ($spells as $spell) {
            $raw = is_object($spell) ? ($spell->element ?? null) : ($spell['element'] ?? null);
            if (! is_numeric($raw)) {
                continue;
            }
            foreach (ElementBitmask::toPrimaries((int) $raw) as $primary) {
                $key = match ($primary) {
                    1 => 'strong',
                    2 => 'intel',
                    3 => 'agi',
                    4 => 'chance',
                    default => null,
                };
                if ($key === null) {
                    continue;
                }
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }
        }
        if ($counts === []) {
            return null;
        }
        arsort($counts);
        $first = array_key_first($counts);

        return is_string($first) ? $first : null;
    }

    /**
     * Si le LLM omet la carac de voie, la pose à la valeur haute du gabarit.
     *
     * @param  array<string, string>  $expected
     * @param  array<string, mixed>  $proposed
     * @return array<string, string>
     */
    public function fillOmittedPrimaryFromElement(array $expected, array $proposed, ?string $primaryKey): array
    {
        $caracs = ['strong', 'intel', 'agi', 'chance'];
        if ($primaryKey === null || ! in_array($primaryKey, $caracs, true) || array_key_exists($primaryKey, $proposed)) {
            return $expected;
        }

        $values = array_map(static fn (string $key): int => (int) ($expected[$key] ?? 8), $caracs);
        $high = max($values);
        $base = min($values);
        foreach ($caracs as $key) {
            if ($key === $primaryKey) {
                $expected[$key] = (string) $high;

                continue;
            }
            if (! array_key_exists($key, $proposed) && (int) ($expected[$key] ?? 0) === $high) {
                $expected[$key] = (string) $base;
            }
        }

        return $expected;
    }

    /**
     * @return list<string>
     */
    public function creatureStatKeys(): array
    {
        return ['life', 'pa', 'pm', 'po', 'ca', 'ini', 'touch', 'vitality', 'sagesse', 'strong', 'intel', 'agi', 'chance'];
    }

    private function powerIndexForRole(string $role): int
    {
        return match ($role) {
            NpcRole::SOCIAL, NpcRole::MERCHANT => 1, // weak
            NpcRole::ALLY => 2, // neutral
            NpcRole::GUARD => 3, // strong
            NpcRole::ENEMY => 4, // very_strong
            default => 2,
        };
    }

    private function normValue(string $key, int $level, int $powerIndex, NormsResolver $resolver): int|float|null
    {
        $getter = $this->getter ?? (app()->bound(CharacteristicGetterService::class) ? app(CharacteristicGetterService::class) : null);
        if ($getter === null) {
            return null;
        }
        $definition = $getter->getDefinition($key, '*');
        $grid = is_array($definition['norms_grid'] ?? null) ? $definition['norms_grid'] : null;

        return $resolver->resolve($grid, $level, $powerIndex);
    }
}
