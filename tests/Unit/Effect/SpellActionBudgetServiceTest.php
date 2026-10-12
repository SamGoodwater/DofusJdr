<?php

declare(strict_types=1);

namespace Tests\Unit\Effect;

use App\Services\Effect\SpellActionBudgetService;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie les budgets de sorts alignés sur les règles 5.3.1 (grilles par défaut).
 */
final class SpellActionBudgetServiceTest extends TestCase
{
    private SpellActionBudgetService $service;

    protected function setUp(): void
    {
        $this->service = new SpellActionBudgetService;
    }

    public function test_damage_cast_and_turn_follow_reference_curve(): void
    {
        // L1 neutre : cast 4 (≈ 1d6+1), tour 4 × 6 / 3 = 8
        $this->assertSame(4, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 1, 3));
        $this->assertSame(8, $this->service->turnBudget(SpellActionBudgetService::ACTION_DAMAGE, 1));

        // L20 neutre : cast 12, tour 12 × 12 / 3 = 48
        $this->assertSame(12, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 20, 3));
        $this->assertSame(48, $this->service->turnBudget(SpellActionBudgetService::ACTION_DAMAGE, 20));
    }

    public function test_action_point_cost_shifts_level_not_prorate(): void
    {
        // 5 PA à niveau 1 → niveau effectif 4 → cast neutre 6
        $this->assertSame(6, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 1, 5));
        // 2 PA à niveau 10 → niveau effectif 7 → cast neutre 8
        $this->assertSame(8, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 10, 2));
        // 4 PA à niveau 10 → niveau effectif 11 → cast neutre 10
        $this->assertSame(10, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 10, 4));
    }

    public function test_heal_and_life_steal_stay_below_pure_damage(): void
    {
        $damage = $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 20, 3);
        $heal = $this->service->budgetForCast(SpellActionBudgetService::ACTION_HEAL, 20, 3);
        $shield = $this->service->budgetForCast(SpellActionBudgetService::ACTION_SHIELD, 20, 3);
        $tempHp = $this->service->budgetForCast(SpellActionBudgetService::ACTION_TEMP_HP, 20, 3);
        $lifeSteal = $this->service->budgetForCast(SpellActionBudgetService::ACTION_LIFE_STEAL, 20, 3);

        $this->assertSame(12, $damage);
        $this->assertSame(10, $heal);
        $this->assertSame(10, $shield);
        $this->assertSame(10, $tempHp);
        $this->assertSame(6, $lifeSteal);
        $this->assertLessThan($damage, $heal);
        $this->assertSame($heal, $shield);
        $this->assertSame($heal, $tempHp);
        $this->assertLessThan($damage, $lifeSteal);
    }

    public function test_distribution_preserves_budget_and_relative_weight(): void
    {
        $distributed = $this->service->distribute(20, [3, 1]);

        $this->assertSame(20, array_sum($distributed));
        $this->assertSame([15, 5], $distributed);
    }
}
