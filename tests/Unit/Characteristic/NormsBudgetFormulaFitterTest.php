<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use App\Services\Characteristic\Norms\NormsBudgetFormulaFitter;
use App\Services\Characteristic\Norms\NormsGridGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Ajustement de budget ± → formule neutre + coefficients.
 */
final class NormsBudgetFormulaFitterTest extends TestCase
{
    private NormsBudgetFormulaFitter $fitter;

    private NormsGridGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fitter = new NormsBudgetFormulaFitter;
        $this->generator = new NormsGridGenerator(
            new FormulaResolutionService(new SafeExpressionEvaluator)
        );
    }

    public function test_fit_grid_produces_formula_without_power_and_positive_coefficients(): void
    {
        $neutral = [];
        for ($level = 1; $level <= 20; $level++) {
            $neutral[] = (float) max(1, (int) round(0.8 * pow($level, 0.5)));
        }
        $grid = [
            'very_weak' => array_map(static fn (float $v): float => (float) (int) round($v * 0.5), $neutral),
            'weak' => array_map(static fn (float $v): float => (float) (int) round($v * 0.75), $neutral),
            'neutral' => $neutral,
            'strong' => array_map(static fn (float $v): float => (float) (int) round($v * 1.3), $neutral),
            'very_strong' => array_map(static fn (float $v): float => (float) (int) round($v * 1.7), $neutral),
        ];

        $fit = $this->fitter->fitGrid($grid);
        $this->assertNotNull($fit);
        $this->assertStringContainsString('pow([level]', $fit['formula']);
        $this->assertStringNotContainsString('[power]', $fit['formula']);
        foreach (NormsGridGenerator::POWER_COEFFICIENT_KEYS as $key) {
            $this->assertArrayHasKey($key, $fit['coefficients']);
            $this->assertGreaterThan(0, $fit['coefficients'][$key]);
        }
        $this->assertGreaterThanOrEqual(
            $fit['coefficients']['weak'],
            $fit['coefficients']['strong'],
            'Le coefficient fort doit être ≥ faible'
        );
    }

    public function test_fit_grid_skips_nearly_empty_neutral_row(): void
    {
        $zeros = array_fill(0, 20, 0.0);
        $veryStrong = $zeros;
        $veryStrong[19] = 50.0;
        $grid = [
            'very_weak' => $zeros,
            'weak' => $zeros,
            'neutral' => $zeros,
            'strong' => $zeros,
            'very_strong' => $veryStrong,
        ];

        $this->assertNull($this->fitter->fitGrid($grid));
    }

    public function test_should_overwrite_power_formulas_only_by_default(): void
    {
        $this->assertTrue($this->fitter->shouldOverwriteExisting(null));
        $this->assertTrue($this->fitter->shouldOverwriteExisting('max(0,2+[power])'));
        $this->assertFalse($this->fitter->shouldOverwriteExisting('round(3*pow([level],0.633))'));
    }

    public function test_regenerated_grid_stays_within_signed_bounds(): void
    {
        $fit = $this->fitter->fitGrid([
            'very_weak' => array_fill(0, 20, 1.0),
            'weak' => array_fill(0, 20, 2.0),
            'neutral' => array_fill(0, 20, 3.0),
            'strong' => array_fill(0, 20, 4.0),
            'very_strong' => array_fill(0, 20, 5.0),
        ]);
        $this->assertNotNull($fit);

        $grid = $this->generator->preview(
            $fit['formula'],
            [],
            false,
            $fit['coefficients'],
            true,
            -5,
            5
        );

        foreach ($grid as $row) {
            foreach ($row as $value) {
                $this->assertGreaterThanOrEqual(-5, $value);
                $this->assertLessThanOrEqual(5, $value);
            }
        }
        $this->assertStringNotContainsString('[power]', $fit['formula']);
    }
}
