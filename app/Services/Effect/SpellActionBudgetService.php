<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Support\Effect\SpellReferenceScaleDefaults;

/**
 * Budgets d'action de sorts issus des normes `characteristic_spell`.
 *
 * Les grilles expriment un lancement de référence à 3 PA (mod inclus).
 * Le total par tour = cast × PA_max(niveau) ÷ 3.
 * Le coût en PA d'un lancement décale le niveau effectif (pas un prorata)
 * pour éviter de compter deux fois le coût déjà encodé dans les conditions.
 *
 * @example
 * $budget = (new SpellActionBudgetService)->budgetForCast('damage', 10, 3);
 */
final class SpellActionBudgetService
{
    public const ACTION_DAMAGE = SpellReferenceScaleDefaults::ACTION_DAMAGE;

    public const ACTION_HEAL = SpellReferenceScaleDefaults::ACTION_HEAL;

    public const ACTION_SHIELD = SpellReferenceScaleDefaults::ACTION_SHIELD;

    public const ACTION_TEMP_HP = SpellReferenceScaleDefaults::ACTION_TEMP_HP;

    public const ACTION_LIFE_STEAL = SpellReferenceScaleDefaults::ACTION_LIFE_STEAL;

    public const POWER_LEVELS = SpellReferenceScaleDefaults::POWER_LEVELS;

    public function __construct(
        private readonly ?CharacteristicGetterService $getter = null,
    ) {}

    /**
     * Retourne le budget total par tour pour une action, un niveau et un palier.
     */
    public function turnBudget(string $action, int $level, int $powerIndex = 2): int
    {
        $cast = $this->budgetForCast($action, $level, 3, $powerIndex);

        return max(1, (int) round($cast * $this->maxActionPoints($level) / 3));
    }

    /**
     * Budget par lancement : lit la grille au niveau effectif selon le coût en PA.
     *
     * Référence = 3 PA (pas de décalage). ≤2 PA → −3 niveaux ; 4 PA → +1 ; ≥5 PA → +3.
     */
    public function budgetForCast(
        string $action,
        int $level,
        int $actionPointCost,
        int $powerIndex = 2
    ): int {
        $effectiveLevel = $this->levelWithActionPointOffset($level, $actionPointCost);
        $grid = $this->resolveGrid($action);
        $powerKey = self::POWER_LEVELS[$this->powerIndex($powerIndex)];
        $row = $grid[$powerKey] ?? $grid['neutral'] ?? [];
        $index = $this->levelIndex($effectiveLevel);
        $value = $row[$index] ?? null;

        return max(1, (int) ($value ?? 1));
    }

    /**
     * Distribue un budget entier entre plusieurs lignes en conservant leurs poids relatifs.
     *
     * @param  list<int|float>  $weights
     * @return list<int>
     */
    public function distribute(int $budget, array $weights): array
    {
        if ($weights === []) {
            return [];
        }
        $budget = max(count($weights), $budget);
        $positiveWeights = array_map(static fn (int|float $value): float => max(0.0, (float) $value), $weights);
        $weightTotal = array_sum($positiveWeights);
        if ($weightTotal <= 0) {
            $positiveWeights = array_fill(0, count($weights), 1.0);
            $weightTotal = (float) count($weights);
        }

        $raw = array_map(
            static fn (float $weight): float => $budget * $weight / $weightTotal,
            $positiveWeights
        );
        $values = array_map(static fn (float $value): int => max(1, (int) floor($value)), $raw);

        while (array_sum($values) < $budget) {
            $bestIndex = 0;
            $bestRemainder = -INF;
            foreach ($raw as $index => $value) {
                $remainder = $value - $values[$index];
                if ($remainder > $bestRemainder) {
                    $bestIndex = $index;
                    $bestRemainder = $remainder;
                }
            }
            $values[$bestIndex]++;
        }
        while (array_sum($values) > $budget) {
            $largestIndex = array_keys($values, max($values), true)[0];
            if ($values[$largestIndex] <= 1) {
                break;
            }
            $values[$largestIndex]--;
        }

        return array_values($values);
    }

    public function maxActionPoints(int $level): int
    {
        return SpellReferenceScaleDefaults::MAX_ACTION_POINTS[$this->levelIndex($level)];
    }

    private function levelWithActionPointOffset(int $level, int $actionPointCost): int
    {
        $cost = max(1, $actionPointCost);
        $offset = 0;
        if ($cost <= 2) {
            $offset = -3;
        } elseif ($cost === 4) {
            $offset = 1;
        } elseif ($cost >= 5) {
            $offset = 3;
        }

        return max(1, min(20, $level + $offset));
    }

    /**
     * @return array<string, list<int|float>>
     */
    private function resolveGrid(string $action): array
    {
        if ($this->getter !== null) {
            $key = SpellReferenceScaleDefaults::characteristicKey($action);
            $definition = $this->getter->getDefinition($key, 'spell');
            $grid = is_array($definition['norms_grid'] ?? null) ? $definition['norms_grid'] : null;
            if ($grid !== null && $grid !== []) {
                return $grid;
            }
        }

        return SpellReferenceScaleDefaults::grid($action);
    }

    private function levelIndex(int $level): int
    {
        return max(0, min(19, $level - 1));
    }

    private function powerIndex(int $powerIndex): int
    {
        return max(0, min(4, $powerIndex));
    }
}
