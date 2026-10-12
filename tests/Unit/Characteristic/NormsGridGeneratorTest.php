<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use App\Services\Characteristic\Norms\NormsGridGenerator;
use PHPUnit\Framework\TestCase;

final class NormsGridGeneratorTest extends TestCase
{
    private NormsGridGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new NormsGridGenerator(
            new FormulaResolutionService(new SafeExpressionEvaluator)
        );
    }

    public function test_preview_evaluates_level_and_power(): void
    {
        $grid = $this->generator->preview('round(10+[level]+[power])');

        $this->assertSame(11.0, $grid['neutral'][0]); // L1 power 0
        $this->assertSame(30.0, $grid['neutral'][19]); // L20
        $this->assertSame(12.0, $grid['strong'][0]); // L1 power 1
        $this->assertSame(9.0, $grid['very_weak'][0]); // L1 power -2
    }

    public function test_preview_reads_dependency_grids(): void
    {
        $deps = [
            'creature|*|life_points_creature' => [
                'neutral' => array_fill(0, 20, 100),
                'strong' => array_fill(0, 20, 200),
            ],
            'creature|*|action_points_creature' => [
                'neutral' => array_fill(0, 20, 6),
                'strong' => array_fill(0, 20, 6),
            ],
        ];

        $grid = $this->generator->preview(
            'round(0.15*[life_points_creature]*3/[action_points_creature])',
            $deps
        );

        $this->assertSame(8.0, $grid['neutral'][0]); // round(0.15*100*3/6)=7.5→8
        $this->assertSame(15.0, $grid['strong'][0]); // round(0.15*200*3/6)=15
    }

    public function test_preview_level_table_with_sub_formulas(): void
    {
        $formula = json_encode([
            'characteristic' => 'level',
            '1' => 'round(0.15*[life_points_creature])',
            '10' => 'round(0.30*[life_points_creature])',
        ], JSON_THROW_ON_ERROR);

        $deps = [
            'creature|*|life_points_creature' => [
                'neutral' => array_fill(0, 20, 100),
            ],
        ];

        $grid = $this->generator->preview($formula, $deps);

        $this->assertSame(15.0, $grid['neutral'][0]);
        $this->assertSame(15.0, $grid['neutral'][8]); // still tranche 1
        $this->assertSame(30.0, $grid['neutral'][9]); // L10
    }
}
