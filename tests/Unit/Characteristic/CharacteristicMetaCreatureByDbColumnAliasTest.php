<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Services\Characteristic\CharacteristicMetaByDbColumnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Les krefs « characteristic » passent la clé métier (ex. {@code strength_creature}) ;
 * le pivot créature indexe surtout la colonne SQL ({@code strong}). Le meta doit exposer les deux.
 */
class CharacteristicMetaCreatureByDbColumnAliasTest extends TestCase
{
    use RefreshDatabase;

    public function test_creature_meta_indexes_by_canonical_characteristic_key(): void
    {
        $characteristic = Characteristic::create([
            'key' => 'strength_creature',
            'name' => 'Force',
            'short_name' => 'For',
            'type' => 'int',
            'status' => Characteristic::STATUS_A_VALIDER,
            'sort_order' => 1,
            'group' => 'creature',
            'icon' => 'earth.webp',
            'color' => 'brown',
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicCreature::ENTITY_ALL,
            'db_column' => 'strong',
            'default_value' => '8',
            'min' => '1',
            'max' => '20',
        ]);

        $service = new CharacteristicMetaByDbColumnService;
        $byDb = $service->buildCreatureByDbColumn();

        $this->assertArrayHasKey('strong', $byDb);
        $this->assertArrayHasKey('strength_creature', $byDb);
        $this->assertSame($byDb['strong'], $byDb['strength_creature']);
        $this->assertSame('strength_creature', $byDb['strength_creature']['key']);
        $this->assertSame('1', $byDb['strong']['limit_min']);
        $this->assertSame('20', $byDb['strong']['limit_max']);
    }

    public function test_creature_meta_resolves_level_limits_to_widest_bound(): void
    {
        $characteristic = Characteristic::create([
            'key' => 'vitality_creature',
            'name' => 'Vitalité',
            'short_name' => 'Vit',
            'type' => 'int',
            'status' => Characteristic::STATUS_A_VALIDER,
            'sort_order' => 2,
            'group' => 'creature',
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicCreature::ENTITY_ALL,
            'db_column' => 'vit',
            'min' => '[level]*2',
            'max' => '99',
        ]);

        $byDb = (new CharacteristicMetaByDbColumnService)->buildCreatureByDbColumn();

        $this->assertSame('2', $byDb['vit']['limit_min']);
        $this->assertSame('99', $byDb['vit']['limit_max']);
    }

    public function test_default_creature_meta_ignores_monster_overlay_limits(): void
    {
        $characteristic = Characteristic::create([
            'key' => 'agility_creature',
            'name' => 'Agilité',
            'short_name' => 'Agi',
            'type' => 'int',
            'status' => Characteristic::STATUS_VALIDEE,
            'sort_order' => 3,
            'group' => 'creature',
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicCreature::ENTITY_ALL,
            'db_column' => 'agi',
            'min' => '6',
            'max' => '24',
        ]);
        CharacteristicCreature::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicCreature::ENTITY_MONSTER,
            'db_column' => 'agi',
            'min' => '-3',
            'max' => '33',
        ]);

        $service = new CharacteristicMetaByDbColumnService;
        $byDb = $service->buildCreatureByDbColumn();
        $monsterByDb = $service->buildMonsterCreatureByDbColumn();

        $this->assertSame('6', $byDb['agi']['limit_min']);
        $this->assertSame('24', $byDb['agi']['limit_max']);
        $this->assertSame('-3', $monsterByDb['agi']['limit_min']);
        $this->assertSame('33', $monsterByDb['agi']['limit_max']);
    }

    public function test_computed_meta_discovers_new_definition_without_hardcoded_whitelist(): void
    {
        $characteristic = Characteristic::create([
            'key' => 'future_computed_creature',
            'name' => 'Calcul futur',
            'short_name' => 'Futur',
            'type' => 'int',
            'status' => Characteristic::STATUS_VALIDEE,
            'sort_order' => 999,
            'group' => 'creature',
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $characteristic->id,
            'entity' => CharacteristicCreature::ENTITY_ALL,
            'db_column' => null,
            'formula' => '[level_creature]',
            'formula_display' => 'niv.',
        ]);

        $computed = (new CharacteristicMetaByDbColumnService)->buildCreatureComputedByKey();

        $this->assertArrayHasKey('future_computed_creature', $computed);
        $this->assertSame('Calcul futur', $computed['future_computed_creature']['name']);
    }
}
