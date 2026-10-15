<?php

declare(strict_types=1);

namespace Tests\Feature\Characteristic;

use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristics\CharacteristicDefinitionQualityService;
use App\Services\Creature\Runtime\CreatureRuntimeStatsService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\ConsumableCharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\ProfileCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Limites absolues (partie permanente bornée) et bonus éphémères (buff_debuff) au runtime créature.
 */
final class CreatureRuntimeEphemeralLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
        $this->seed(ConsumableCharacteristicSeeder::class);
        $this->seed(ProfileCharacteristicSeeder::class);
        app(CharacteristicGetterService::class)->clearCache();
    }

    public function test_monster_modifier_and_skill_are_capped_but_buffs_pass(): void
    {
        $monster = Monster::factory()->create(['acrobatics_mastery' => 2]);
        $creature = $monster->creature;
        $creature->forceFill(['level' => '20', 'agi' => '40'])->save();

        $service = app(CreatureRuntimeStatsService::class);

        $rows = $service->resolve($creature->fresh(), 'monster')['levels'][0]['characteristics'];
        $this->assertSame(10.0, (float) $rows['modifier_agility_creature']['total']);
        $this->assertTrue($rows['modifier_agility_creature']['capped']);
        $this->assertSame(20.0, (float) $rows['acrobatics_creature']['total']);
        $this->assertTrue($rows['acrobatics_creature']['capped']);
        $this->assertStringNotContainsString('_spell', (string) $rows['acrobatics_creature']['substituted']);

        $buffed = $service->resolve($creature->fresh(), 'monster', ['acrobatics_spell' => 3])['levels'][0]['characteristics'];
        $this->assertSame(20.0, (float) $buffed['acrobatics_creature']['permanent']);
        $this->assertSame(3.0, (float) $buffed['acrobatics_creature']['ephemeral']);
        $this->assertSame(23.0, (float) $buffed['acrobatics_creature']['total']);
    }

    public function test_npc_skill_is_capped_at_fifteen(): void
    {
        $npc = Npc::factory()->create();
        $creature = $npc->creature;
        $creature->forceFill(['level' => '20', 'agi' => '40', 'acrobatie_bonus' => '10'])->save();

        $rows = app(CreatureRuntimeStatsService::class)
            ->resolve($creature->fresh(), 'npc', ['agi_spell' => 10])['levels'][0]['characteristics'];

        $this->assertSame(7.0, (float) $rows['modifier_agility_creature']['permanent']);
        $this->assertSame(5.0, (float) $rows['modifier_agility_creature']['ephemeral']);
        $this->assertLessThanOrEqual(15.0, (float) $rows['acrobatics_creature']['permanent']);
    }

    public function test_level_dependent_limits_without_level_use_widest_bounds(): void
    {
        $getter = app(CharacteristicGetterService::class);

        $this->assertSame(['min' => -2, 'max' => 7], $getter->getLimits('modifier_agility_creature', 'npc'));
        $this->assertSame(['min' => -2, 'max' => 3], $getter->getLimits('modifier_agility_creature', 'npc', ['level' => 4]));
        $this->assertSame(['min' => -15, 'max' => 15], $getter->getLimits('acrobatics_creature', 'npc'));
        $this->assertSame(['min' => -20, 'max' => 20], $getter->getLimits('save_agility_creature', 'monster'));
    }

    public function test_formula_display_rejects_raw_keys(): void
    {
        $this->assertNull(CharacteristicDefinitionQualityService::formulaDisplayIssue('mod. Int + équip.'));
        $this->assertNotNull(CharacteristicDefinitionQualityService::formulaDisplayIssue('[modifier_intelligence_creature]+[initiative_spell]'));
        $this->assertNull(CharacteristicDefinitionQualityService::formulaDisplayIssue('{"characteristic":"level_object","1":"0"}'));
    }

    public function test_base_placeholder_uses_default_value_when_column_is_empty(): void
    {
        $monster = Monster::factory()->create();
        $creature = $monster->creature;
        $creature->forceFill(['level' => '5', 'pa' => null, 'agi' => null])->save();

        $rows = app(CreatureRuntimeStatsService::class)
            ->resolve($creature->fresh(), 'monster')['levels'][0]['characteristics'];

        $this->assertSame(6.0, (float) $rows['action_points_creature']['total']);
        $this->assertSame(8.0, (float) $rows['agility_creature']['total']);
    }

    public function test_composed_totals_include_item_object_bonuses(): void
    {
        $monster = Monster::factory()->create(['acrobatics_mastery' => 0]);
        $creature = $monster->creature;
        $creature->forceFill([
            'level' => '10',
            'agi' => '10',
            'intel' => '10',
            'strong' => null,
            'ini' => null,
        ])->save();

        $service = app(CreatureRuntimeStatsService::class);
        $before = $service->resolve($creature->fresh()->load('items'), 'monster')['levels'][0]['characteristics'];

        $item = Item::factory()->create([
            'effect' => '{"acrobatics":4,"strength":4,"initiative":3,"wakfu_recharge":2}',
            'bonus' => null,
            'state' => Item::STATE_PLAYABLE,
        ]);
        $creature->items()->attach([$item->id => ['quantity' => 1]]);

        $resolved = $service->resolve($creature->fresh()->load('items'), 'monster');
        $rows = $resolved['levels'][0]['characteristics'];
        $vars = $resolved['levels'][0]['variables'];

        $this->assertSame(4.0, (float) $rows['acrobatics_creature']['object']);
        $this->assertSame(
            (float) $before['acrobatics_creature']['total'] + 4.0,
            (float) $rows['acrobatics_creature']['total']
        );
        $this->assertSame(4.0, (float) ($vars['acrobatics_object'] ?? 0));
        $this->assertArrayNotHasKey('acrobatics_creature_object', $vars);

        $this->assertSame(4.0, (float) $rows['strength_creature']['object']);
        $this->assertSame(
            (float) $before['strength_creature']['total'] + 4.0,
            (float) $rows['strength_creature']['total']
        );
        $this->assertSame(4.0, (float) ($vars['strength_object'] ?? 0));

        $this->assertSame(3.0, (float) $rows['initiative_creature']['object']);
        $this->assertSame(
            (float) $before['initiative_creature']['total'] + 3.0,
            (float) $rows['initiative_creature']['total']
        );
        $this->assertSame(3.0, (float) ($vars['initiative_object'] ?? 0));

        $this->assertSame(2.0, (float) ($vars['wakfu_recharge_object'] ?? 0));
        $this->assertSame(
            (float) $before['wakfu_reserve_creature']['total'] + 2.0,
            (float) $rows['wakfu_reserve_creature']['total']
        );
    }

    public function test_wakfu_reserve_is_clamped_via_min_max_definition(): void
    {
        $getter = app(CharacteristicGetterService::class);
        $this->assertSame(['min' => 0, 'max' => 9], $getter->getLimits('wakfu_reserve_creature', 'npc'));

        $npc = Npc::factory()->create();
        $creature = $npc->creature;
        $creature->forceFill(['level' => '20'])->save();

        $rows = app(CreatureRuntimeStatsService::class)
            ->resolve($creature->fresh(), 'npc', ['wakfu_reserve_spell' => 20])['levels'][0]['characteristics'];

        $this->assertArrayHasKey('wakfu_reserve_creature', $rows);
        $this->assertSame(9.0, (float) $rows['wakfu_reserve_creature']['total']);
    }

    public function test_save_dc_is_ten_plus_modifier_and_ephemeral_buffs_add(): void
    {
        $getter = app(CharacteristicGetterService::class);
        $this->assertSame(['min' => -22, 'max' => 22], $getter->getLimits('save_dc_agility_creature', 'npc'));
        $this->assertSame(['min' => -30, 'max' => 30], $getter->getLimits('save_dc_agility_creature', 'monster'));

        $npc = Npc::factory()->create();
        $creature = $npc->creature;
        $creature->forceFill(['level' => '8', 'agi' => '14'])->save();

        $service = app(CreatureRuntimeStatsService::class);
        $rows = $service->resolve($creature->fresh(), 'npc')['levels'][0]['characteristics'];

        $this->assertArrayHasKey('save_dc_agility_creature', $rows);
        $this->assertSame(2.0, (float) $rows['modifier_agility_creature']['total']);
        $this->assertSame(12.0, (float) $rows['save_dc_agility_creature']['total']);
        $this->assertSame(12.0, (float) $rows['save_dc_agility_creature']['permanent']);

        $buffed = $service->resolve($creature->fresh(), 'npc', [
            'save_dc_agility_spell' => 3,
            'save_dc_agility_consumable' => 1,
        ])['levels'][0]['characteristics'];

        $this->assertSame(12.0, (float) $buffed['save_dc_agility_creature']['permanent']);
        $this->assertSame(4.0, (float) $buffed['save_dc_agility_creature']['ephemeral']);
        $this->assertSame(16.0, (float) $buffed['save_dc_agility_creature']['total']);
    }
}
