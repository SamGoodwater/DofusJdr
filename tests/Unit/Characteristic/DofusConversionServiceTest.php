<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests unitaires pour DofusConversionService.
 *
 * @see App\Services\Characteristic\Conversion\DofusConversionService
 */
class DofusConversionServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    private DofusConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->service = $this->app->make(DofusConversionService::class);
    }

    public function test_convert_level_creature_returns_integer(): void
    {
        $key = $this->service->getLevelCharacteristicKey('monster');
        $result = $this->service->convert($key, ['d' => 100.0], 'monster', 10.0);
        $this->assertSame(10, $result);
    }

    public function test_convert_monster_level_divides_by_ten_and_clamps_between_one_and_thirty(): void
    {
        $key = $this->service->getLevelCharacteristicKey('monster');
        $cases = [1 => 1, 9 => 1, 10 => 1, 50 => 5, 200 => 20, 300 => 30, 500 => 30];

        foreach ($cases as $dofusLevel => $expectedKrosmozLevel) {
            $this->assertSame(
                $expectedKrosmozLevel,
                $this->service->convert($key, ['d' => (float) $dofusLevel], 'monster', (float) $dofusLevel),
                "Niveau Dofus {$dofusLevel}"
            );
        }
    }

    public function test_convert_level_handles_null(): void
    {
        $key = $this->service->getLevelCharacteristicKey('monster');
        $result = $this->service->convert($key, ['d' => 0.0], 'monster', 0.0);
        $this->assertSame(1, $result);
    }

    public function test_convert_level_object(): void
    {
        $key = $this->service->getLevelCharacteristicKey('item');
        $result = $this->service->convert($key, ['d' => 50.0], 'item', 5.0);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_spell_level_uses_the_spell_scale_from_one_to_twenty(): void
    {
        $key = $this->service->getLevelCharacteristicKey('spell');

        $this->assertSame('level_spell', $key);
        $this->assertSame(1, $this->service->convert($key, ['d' => 1.0], 'spell', 1.0));
        $this->assertSame(10, $this->service->convert($key, ['d' => 100.0], 'spell', 10.0));
        $this->assertSame(20, $this->service->convert($key, ['d' => 247.0], 'spell', 24.0));
    }

    public function test_get_rarity_by_level_returns_zero_for_creature_entity(): void
    {
        $this->assertNotContains('monster', DofusConversionService::RARITY_ENTITIES);
    }

    public function test_get_rarity_by_level_returns_integer_for_object_entity(): void
    {
        $level = 10;
        $fallback = $this->service->getRarityFallbackForLevel($level);
        $result = $this->service->convert('rarity_object', ['level' => $level], 'item', (float) $fallback);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_object_main_attributes_preserves_signed_bonuses_and_maluses(): void
    {
        $this->assertSame(3, $this->service->convertObjectAttribute('strength_object', 100, 'item'));
        $this->assertSame(-3, $this->service->convertObjectAttribute('strength_object', -100, 'item'));
        $this->assertSame(4, $this->service->convertObjectAttribute('strength_object', 600, 'item'));
        $this->assertSame(-4, $this->service->convertObjectAttribute('strength_object', -600, 'item'));
    }

    public function test_convert_object_ap_and_mp_use_equipment_only_limits(): void
    {
        $this->assertSame(5, $this->service->convertObjectAttribute('action_points_object', 9, 'item'));
        $this->assertSame(-5, $this->service->convertObjectAttribute('action_points_object', -9, 'item'));
        $this->assertSame(2, $this->service->convertObjectAttribute('movement_points_object', 4, 'item'));
        $this->assertSame(-2, $this->service->convertObjectAttribute('movement_points_object', -4, 'item'));
    }

    public function test_convert_object_critical_and_tactical_bonuses_are_symmetric(): void
    {
        $this->assertSame(3, $this->service->convertObjectAttribute('critical_hit_object', 15, 'item'));
        $this->assertSame(-3, $this->service->convertObjectAttribute('critical_hit_object', -15, 'item'));
        $this->assertSame(4, $this->service->convertObjectAttribute('heal_bonus_object', 22, 'item'));
        $this->assertSame(-4, $this->service->convertObjectAttribute('heal_bonus_object', -22, 'item'));
        $this->assertSame(10, $this->service->convertObjectAttribute('tackle_object', 50, 'item'));
        $this->assertSame(-10, $this->service->convertObjectAttribute('dodge_object', -50, 'item'));
    }

    public function test_convert_panoply_percent_resistance_uses_five_bands(): void
    {
        $key = 'resistance_percent_tier_fire_object';

        $this->assertSame(-2, $this->service->convertObjectAttribute($key, -51, 'panoply'));
        $this->assertSame(-1, $this->service->convertObjectAttribute($key, -50, 'panoply'));
        $this->assertSame(0, $this->service->convertObjectAttribute($key, 7, 'panoply'));
        $this->assertSame(1, $this->service->convertObjectAttribute($key, 8, 'panoply'));
        $this->assertSame(2, $this->service->convertObjectAttribute($key, 13, 'panoply'));
    }

    public function test_convert_life_returns_integer(): void
    {
        $result = $this->service->convert('life_points_creature', ['d' => 1000.0, 'level' => 10], 'monster', 55.0);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_life_handles_null_dofus_value(): void
    {
        $result = $this->service->convert('life_points_creature', ['d' => 0.0, 'level' => 5], 'monster', 25.0);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function test_convert_attribute_returns_integer(): void
    {
        $result = $this->service->convertAttribute('vitality', 100, 'monster');
        $this->assertGreaterThanOrEqual(6, $result);
        $this->assertLessThanOrEqual(30, $result);
    }

    public function test_convert_monster_attributes_use_wider_limits(): void
    {
        $this->assertSame(6, $this->service->convert('strength_creature', ['d' => 50], 'monster'));
        $this->assertSame(30, $this->service->convert('strength_creature', ['d' => 1200], 'monster'));
        $this->assertSame(30, $this->service->convert('strength_creature', ['d' => 5000], 'monster'));
        $this->assertSame(
            $this->service->convert('wisdom_creature', ['d' => 255], 'monster'),
            $this->service->convert('vitality_creature', ['d' => 255], 'monster')
        );

        $this->assertSame(24, $this->service->convert('strength_creature', ['d' => 5000], 'class'));
    }

    public function test_monster_modifiers_reach_ten_without_changing_character_modifiers(): void
    {
        $getter = $this->app->make(CharacteristicGetterService::class);
        $formulas = $this->app->make(CharacteristicFormulaService::class);
        $monster = $getter->getDefinition('modifier_strength_creature', 'monster');
        $character = $getter->getDefinition('modifier_strength_creature', 'class');

        $this->assertSame('10', $monster['max'] ?? null);
        $this->assertSame(
            10.0,
            $formulas->evaluate($monster['formula'] ?? null, ['strength_creature' => 30, 'level_creature' => 30])
        );
        $this->assertSame('min(floor([level_creature]/4)+2, 7)', $character['max'] ?? null);
        $this->assertTrue($character['limit_absolute'] ?? false);
        $this->assertSame(
            10.0,
            $formulas->evaluate($character['formula'] ?? null, ['strength_creature' => 30, 'level_creature' => 20])
        );
        $this->assertSame(
            ['min' => -2, 'max' => 7],
            $getter->getLimits('modifier_strength_creature', 'class', ['level_creature' => 20])
        );
        $this->assertSame(
            ['min' => -2, 'max' => 3],
            $getter->getLimits('modifier_strength_creature', 'class', ['level_creature' => 4])
        );
    }

    public function test_convert_monster_combat_bonuses_use_monster_limits(): void
    {
        $this->assertSame(3, $this->service->convert('action_points_creature', ['d' => 1], 'monster'));
        $this->assertSame(14, $this->service->convert('action_points_creature', ['d' => 20], 'monster'));
        $this->assertSame(2, $this->service->convert('movement_points_creature', ['d' => 1], 'monster'));
        $this->assertSame(10, $this->service->convert('movement_points_creature', ['d' => 12], 'monster'));
        $this->assertSame(10, $this->service->convert('range_creature', ['d' => 10], 'monster'));
        $this->assertSame(20, $this->service->convert('tackle_creature', ['d' => 30], 'monster'));

        $this->assertSame(6, $this->service->convert('action_points_creature', ['d' => 1], 'class'));
        $this->assertSame(6, $this->service->convert('range_creature', ['d' => 10], 'class'));
    }

    public function test_convert_monster_critical_heal_and_resistance_bands(): void
    {
        foreach ([29 => 0, 30 => 1, 49 => 1, 50 => 2, 69 => 2, 70 => 3] as $raw => $expected) {
            $this->assertSame($expected, $this->service->convert('critical_hit_creature', ['d' => $raw], 'monster'));
        }
        $this->assertSame(0, $this->service->convert('heal_bonus_creature', ['d' => 5], 'monster'));
        $this->assertSame(5, $this->service->convert('heal_bonus_creature', ['d' => 30], 'monster'));
        $this->assertSame(7, $this->service->convert('heal_bonus_creature', ['d' => 40], 'monster'));

        foreach ([-75 => -100, -74 => -50, -24 => 0, 24 => 0, 25 => 50, 75 => 100] as $raw => $expected) {
            $this->assertSame($expected, $this->service->convert('resistance_fire_creature', ['d' => $raw], 'monster'));
        }
    }

    public function test_convert_initiative_returns_integer(): void
    {
        $this->assertSame(
            5000,
            $this->service->convert('initiative_creature', ['d' => 5000.0], 'monster', 5000.0)
        );
    }

    public function test_clamp_to_limits_returns_value_in_limits(): void
    {
        $getter = $this->app->make(CharacteristicGetterService::class);
        $limits = $getter->getLimits('level_creature', 'monster');
        $this->assertNotNull($limits);
        $mid = (int) (($limits['min'] + $limits['max']) / 2);
        $result = $this->service->clampToLimits('level_creature', $mid, 'monster');
        $this->assertSame($mid, $result);
    }
}
