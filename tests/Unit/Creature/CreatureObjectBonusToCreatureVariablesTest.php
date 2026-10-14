<?php

declare(strict_types=1);

namespace Tests\Unit\Creature;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Services\Creature\Runtime\CreatureObjectBonusToCreatureVariables;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreatureObjectBonusToCreatureVariables::class)]
final class CreatureObjectBonusToCreatureVariablesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_map_keeps_object_totals_separate_from_base(): void
    {
        $getter = Mockery::mock(CharacteristicDefinitionLookup::class);
        $getter->shouldReceive('getDefinition')->andReturnUsing(static function (string $key, string $entity): ?array {
            return match ($key) {
                'action_points_creature' => ['db_column' => 'pa'],
                default => null,
            };
        });

        $merger = new CreatureObjectBonusToCreatureVariables($getter);
        $mapped = $merger->mapToCharacteristicKeys('monster', [
            'action_points' => 2,
            'acrobatics' => 3,
        ]);

        $this->assertSame(2, $mapped['action_points_creature']);
        $this->assertSame(3, $mapped['acrobatics_creature']);
    }

    public function test_french_skill_bonus_names_list_is_non_empty(): void
    {
        $names = CreatureObjectBonusToCreatureVariables::frenchSkillBonusVariableNames();
        $this->assertNotEmpty($names);
        $this->assertContains('acrobatie_bonus', $names);
    }

    public function test_map_fixed_damage_multiple_even_without_db_column(): void
    {
        $getter = Mockery::mock(CharacteristicDefinitionLookup::class);
        $getter->shouldReceive('getDefinition')->andReturnUsing(static function (string $key, string $entity): ?array {
            return match ($key) {
                'fixed_damage_multiple_creature' => [
                    'key' => 'fixed_damage_multiple_creature',
                    'db_column' => null,
                ],
                default => null,
            };
        });

        $merger = new CreatureObjectBonusToCreatureVariables($getter);
        $mapped = $merger->mapToCharacteristicKeys('monster', [
            'fixed_damage_multiple' => 3,
        ]);

        $this->assertSame(3, $mapped['fixed_damage_multiple_creature']);
    }

    public function test_map_life_points_max_and_wakfu_recharge_aliases(): void
    {
        $getter = Mockery::mock(CharacteristicDefinitionLookup::class);
        $getter->shouldReceive('getDefinition')->andReturnUsing(static function (string $key, string $entity): ?array {
            return match ($key) {
                'life_points_creature', 'wakfu_reserve_creature' => ['key' => $key],
                default => null,
            };
        });

        $merger = new CreatureObjectBonusToCreatureVariables($getter);
        $mapped = $merger->mapToCharacteristicKeys('monster', [
            'life_points_max' => 4,
            'wakfu_recharge' => 2,
            'wakfu_reserve' => 1,
        ]);

        $this->assertSame(4, $mapped['life_points_creature']);
        $this->assertSame(3, $mapped['wakfu_reserve_creature']);
    }
}
