<?php

declare(strict_types=1);

namespace Tests\Unit\Effect;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Services\Effect\SpellActionBudgetService;
use PHPUnit\Framework\TestCase;

/**
 * Budgets de sorts avec un lookup stub (pas de base).
 */
final class SpellActionBudgetServiceTest extends TestCase
{
    private SpellActionBudgetService $service;

    protected function setUp(): void
    {
        $grids = [
            'dommages_spell' => [
                'weak' => array_fill(0, 20, 2),
                'neutral' => [4, 5, 6, 6, 7, 8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12],
                'strong' => array_fill(0, 20, 8),
            ],
            'soin_spell' => [
                'neutral' => array_fill(0, 20, 5),
            ],
            'bouclier_spell' => [
                'neutral' => array_fill(0, 20, 7),
            ],
            'pv_temporaires_spell' => [
                'neutral' => array_fill(0, 20, 6),
            ],
            'vol_vie_spell' => [
                'neutral' => array_map(
                    static fn (int $v): int => max(1, (int) round(0.3 * $v)),
                    [4, 5, 6, 6, 7, 8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12]
                ),
            ],
            'action_points_creature' => [
                'neutral' => [6, 6, 6, 7, 7, 7, 8, 8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 12, 12],
            ],
        ];

        $getter = new class($grids) implements CharacteristicDefinitionLookup
        {
            /** @param  array<string, array<string, list<int>>>  $grids */
            public function __construct(private readonly array $grids) {}

            public function getDefinition(string $key, string $entity): ?array
            {
                return ['norms_grid' => $this->grids[$key] ?? null];
            }
        };

        $this->service = new SpellActionBudgetService($getter);
    }

    public function test_damage_cast_and_turn_follow_norms_grid(): void
    {
        $this->assertSame(4, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 1, 3));
        $this->assertSame(8, $this->service->turnBudget(SpellActionBudgetService::ACTION_DAMAGE, 1));
        $this->assertSame(12, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 20, 3));
        $this->assertSame(48, $this->service->turnBudget(SpellActionBudgetService::ACTION_DAMAGE, 20));
    }

    public function test_action_point_cost_shifts_level_not_prorate(): void
    {
        $this->assertSame(6, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 1, 5));
        $this->assertSame(8, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 10, 2));
        $this->assertSame(10, $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 10, 4));
    }

    public function test_heal_shield_and_life_steal_from_grids(): void
    {
        $damage = $this->service->budgetForCast(SpellActionBudgetService::ACTION_DAMAGE, 20, 3);
        $heal = $this->service->budgetForCast(SpellActionBudgetService::ACTION_HEAL, 20, 3);
        $shield = $this->service->budgetForCast(SpellActionBudgetService::ACTION_SHIELD, 20, 3);
        $lifeSteal = $this->service->budgetForCast(SpellActionBudgetService::ACTION_LIFE_STEAL, 20, 3);

        $this->assertSame(12, $damage);
        $this->assertSame(5, $heal);
        $this->assertSame(7, $shield);
        $this->assertSame(4, $lifeSteal);
    }

    public function test_distribution_preserves_budget_and_relative_weight(): void
    {
        $distributed = $this->service->distribute(20, [3, 1]);

        $this->assertSame(20, array_sum($distributed));
        $this->assertSame([15, 5], $distributed);
    }

    public function test_max_action_points_comes_from_creature_norms(): void
    {
        $this->assertSame(6, $this->service->maxActionPoints(1));
        $this->assertSame(12, $this->service->maxActionPoints(20));
    }
}
