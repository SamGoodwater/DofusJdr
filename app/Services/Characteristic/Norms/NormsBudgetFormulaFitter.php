<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Norms;

use App\Enums\CharacteristicNature;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use Illuminate\Database\Eloquent\Model;

/**
 * Ajuste une formule `round(a*pow([level],b))` + coefficients de palier
 * sur les grilles de budget (≥ 0) des bonus spell / consumable / object.
 *
 * @example
 *   $fit = app(NormsBudgetFormulaFitter::class)->fitGrid($grid);
 *   // ['formula' => 'round(1.2*pow([level],0.45))', 'coefficients' => [...], 'error' => 0.12]
 */
final class NormsBudgetFormulaFitter
{
    private const MIN_POSITIVE_NEUTRAL = 3;

    /**
     * @param  array<string, list<int|float|null>>  $grid
     * @return array{
     *   formula: string,
     *   coefficients: array{very_weak: float, weak: float, strong: float, very_strong: float},
     *   mae: float
     * }|null
     */
    public function fitGrid(array $grid): ?array
    {
        $neutral = $this->numericRow($grid['neutral'] ?? null);
        if ($neutral === null) {
            return null;
        }
        $positiveCount = 0;
        foreach ($neutral as $v) {
            if ($v > 0) {
                $positiveCount++;
            }
        }
        if ($positiveCount < self::MIN_POSITIVE_NEUTRAL || max($neutral) <= 0) {
            return null;
        }

        $params = $this->fitPowerLaw($neutral);
        if ($params === null) {
            return null;
        }
        [$a, $b] = $params;
        $formula = $this->formatFormula($a, $b);
        $predicted = $this->predictNeutral($a, $b);
        $coefficients = $this->deriveCoefficients($grid, $neutral);
        if ($coefficients === null) {
            $coefficients = NormsGridGenerator::DEFAULT_POWER_COEFFICIENTS;
        }
        $mae = $this->meanAbsoluteError($neutral, $predicted);

        return [
            'formula' => $formula,
            'coefficients' => $coefficients,
            'mae' => $mae,
        ];
    }

    /**
     * La ligne est-elle un budget bonus à ajuster (nature + grille ≥ 0) ?
     */
    public function isEligiblePivot(Model $pivot): bool
    {
        $char = $pivot->getRelationValue('characteristic') ?? $pivot->characteristic;
        if ($char === null) {
            return false;
        }
        $nature = $char->natureEnum();
        $okNature = match (true) {
            $pivot instanceof CharacteristicSpell,
            $pivot instanceof CharacteristicConsumable => $nature === CharacteristicNature::BuffDebuff,
            $pivot instanceof CharacteristicObject => $nature === CharacteristicNature::BonusMalus,
            default => false,
        };
        if (! $okNature) {
            return false;
        }

        $grid = $pivot->getAttribute('norms_grid');
        if (! is_array($grid)) {
            return false;
        }

        return $this->gridIsNonNegative($grid);
    }

    /**
     * Remplace une formule [power] existante ou une grille fixe.
     */
    public function shouldOverwriteExisting(?string $formula): bool
    {
        if ($formula === null || trim($formula) === '') {
            return true;
        }

        return str_contains($formula, '[power]');
    }

    /**
     * @param  array<string, mixed>  $grid
     */
    public function gridIsNonNegative(array $grid): bool
    {
        foreach (NormsResolver::POWER_LEVELS as $power) {
            $row = $grid[$power] ?? null;
            if (! is_array($row)) {
                continue;
            }
            foreach ($row as $v) {
                if ($v === null || $v === '') {
                    continue;
                }
                if (! is_numeric($v)) {
                    return false;
                }
                if ((float) $v < 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  list<float>  $neutral
     * @return array{0: float, 1: float}|null
     */
    private function fitPowerLaw(array $neutral): ?array
    {
        $sumX = 0.0;
        $sumY = 0.0;
        $sumXX = 0.0;
        $sumXY = 0.0;
        $n = 0;
        foreach ($neutral as $index => $y) {
            if ($y <= 0) {
                continue;
            }
            $level = $index + 1;
            $x = log((float) $level);
            $ly = log((float) $y);
            $sumX += $x;
            $sumY += $ly;
            $sumXX += $x * $x;
            $sumXY += $x * $ly;
            $n++;
        }
        if ($n < self::MIN_POSITIVE_NEUTRAL) {
            return null;
        }
        $den = ($n * $sumXX) - ($sumX * $sumX);
        if (abs($den) < 1e-12) {
            // Courbe plate : a ≈ moyenne, b ≈ 0
            $avg = exp($sumY / $n);

            return [round(max(0.01, $avg), 2), 0.0];
        }
        $b = (($n * $sumXY) - ($sumX * $sumY)) / $den;
        $logA = ($sumY - ($b * $sumX)) / $n;
        $a = exp($logA);
        if (! is_finite($a) || $a <= 0 || ! is_finite($b)) {
            return null;
        }
        // Borne douce pour éviter des exposants absurdes
        $b = max(-0.5, min(2.5, $b));
        $a = max(0.01, min(100.0, $a));

        return [round($a, 3), round($b, 3)];
    }

    /**
     * @return list<float>
     */
    private function predictNeutral(float $a, float $b): array
    {
        $out = [];
        for ($level = 1; $level <= NormsResolver::MAX_LEVEL; $level++) {
            $out[] = (float) (int) round($a * ($b === 0.0 ? 1.0 : pow($level, $b)));
        }

        return $out;
    }

    private function formatFormula(float $a, float $b): string
    {
        $aStr = fmod($a, 1.0) === 0.0 ? (string) (int) $a : rtrim(rtrim(number_format($a, 3, '.', ''), '0'), '.');
        $bStr = fmod($b, 1.0) === 0.0 ? (string) (int) $b : rtrim(rtrim(number_format($b, 3, '.', ''), '0'), '.');
        if ($b == 0.0 || $bStr === '0') {
            return 'round('.$aStr.')';
        }

        return 'round('.$aStr.'*pow([level],'.$bStr.'))';
    }

    /**
     * @param  array<string, mixed>  $grid
     * @param  list<float>  $neutral
     * @return array{very_weak: float, weak: float, strong: float, very_strong: float}|null
     */
    private function deriveCoefficients(array $grid, array $neutral): ?array
    {
        $out = [];
        foreach (NormsGridGenerator::POWER_COEFFICIENT_KEYS as $power) {
            $row = $this->numericRow($grid[$power] ?? null);
            if ($row === null) {
                return null;
            }
            $ratios = [];
            foreach ($neutral as $i => $base) {
                if ($base <= 0) {
                    continue;
                }
                $v = $row[$i] ?? null;
                if ($v === null) {
                    continue;
                }
                $ratios[] = $v / $base;
            }
            $default = NormsGridGenerator::DEFAULT_POWER_COEFFICIENTS[$power];
            if ($ratios === []) {
                $out[$power] = $default;

                continue;
            }
            $zeroShare = count(array_filter($ratios, static fn (float $r): bool => $r <= 0.0)) / count($ratios);
            // Grille souvent à 0 en bas de courbe → garder les défauts plutôt qu’un coef ~0.
            if ($zeroShare >= 0.5) {
                $out[$power] = $default;

                continue;
            }
            sort($ratios);
            $mid = intdiv(count($ratios), 2);
            $median = count($ratios) % 2 === 1
                ? $ratios[$mid]
                : (($ratios[$mid - 1] + $ratios[$mid]) / 2);
            $out[$power] = round(max(0.25, min(5.0, $median)), 2);
        }

        // Garde l’ordre très faible ≤ faible ≤ fort ≤ très fort
        $out['very_weak'] = min($out['very_weak'], $out['weak']);
        $out['weak'] = max($out['weak'], $out['very_weak']);
        $out['strong'] = max($out['strong'], 1.0);
        $out['very_strong'] = max($out['very_strong'], $out['strong']);

        return $out;
    }

    /**
     * @param  list<float>  $actual
     * @param  list<float>  $predicted
     */
    private function meanAbsoluteError(array $actual, array $predicted): float
    {
        $sum = 0.0;
        $n = min(count($actual), count($predicted));
        if ($n === 0) {
            return 0.0;
        }
        for ($i = 0; $i < $n; $i++) {
            $sum += abs($actual[$i] - $predicted[$i]);
        }

        return round($sum / $n, 3);
    }

    /**
     * @return list<float>|null
     */
    private function numericRow(mixed $row): ?array
    {
        if (! is_array($row) || count($row) !== NormsResolver::MAX_LEVEL) {
            return null;
        }
        $out = [];
        foreach ($row as $v) {
            if ($v === null || $v === '') {
                $out[] = 0.0;

                continue;
            }
            if (! is_numeric($v)) {
                return null;
            }
            $out[] = (float) $v;
        }

        return $out;
    }
}
