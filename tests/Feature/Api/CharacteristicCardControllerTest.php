<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CharacteristicCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_returns_card_for_creature_characteristic(): void
    {
        $char = Characteristic::create([
            'key' => 'initiative_creature',
            'name' => 'Initiative',
            'helper' => 'Ordre de jeu',
            'descriptions' => 'Plus la valeur est haute, plus on joue tôt.',
            'type' => 'int',
            'group' => 'creature',
            'nature' => CharacteristicNature::Intrinsic->value,
            'icon' => 'fa-bolt',
            'color' => '#abcdef',
            'unit' => null,
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '-10',
            'max' => '20',
            'default_value' => '0',
            'formula' => '[modifier_intelligence_creature]+[initiative_object]',
            'formula_display' => 'mod. Int + équip.',
            'limit_absolute' => false,
        ]);

        $response = $this->getJson('/api/characteristics/initiative_creature/card?group=creature');

        $response->assertOk();
        $card = $response->json('card');
        $this->assertSame('initiative_creature', $card['key']);
        $this->assertSame('creature', $card['group']);
        $this->assertSame('Initiative', $card['name']);
        $this->assertSame('Ordre de jeu', $card['helper']);
        $this->assertSame('Plus la valeur est haute, plus on joue tôt.', $card['descriptions']);
        $this->assertSame('mod. Int + équip.', $card['formula_display']);
        $this->assertSame('0', $card['default_value']);
        $this->assertSame(-10, $card['limit_min']);
        $this->assertSame(20, $card['limit_max']);
        $this->assertFalse($card['limit_absolute']);
        $this->assertFalse($card['limits_depend_on_level']);
        $this->assertNull($card['limits_by_level']);
    }

    public function test_returns_limits_by_level_when_bounds_depend_on_level(): void
    {
        $char = Characteristic::create([
            'key' => 'modifier_strength_creature',
            'name' => 'Mod. Force',
            'type' => 'int',
            'group' => 'creature',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '-2',
            'max' => 'min(floor([level_creature]/4)+2,7)',
            'default_value' => '0',
            'formula' => '0',
            'formula_display' => 'mod. Force',
            'limit_absolute' => true,
        ]);

        $response = $this->getJson('/api/characteristics/modifier_strength_creature/card?group=creature');
        $response->assertOk();
        $card = $response->json('card');
        $this->assertTrue($card['limits_depend_on_level']);
        $this->assertTrue($card['limit_absolute']);
        $this->assertCount(20, $card['limits_by_level']);
        $this->assertSame(1, $card['limits_by_level'][0]['level']);
        $this->assertSame(2, $card['limits_by_level'][0]['max']);
        $this->assertSame(20, $card['limits_by_level'][19]['level']);
        $this->assertSame(7, $card['limits_by_level'][19]['max']);
    }

    public function test_supports_consumable_group(): void
    {
        $char = Characteristic::create([
            'key' => 'initiative_consumable',
            'name' => 'Initiative (conso)',
            'type' => 'int',
            'group' => 'consumable',
            'sort_order' => 1,
        ]);

        CharacteristicConsumable::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '-5',
            'max' => '5',
            'default_value' => '0',
            'formula' => '0',
            'formula_display' => '0',
        ]);

        $response = $this->getJson('/api/characteristics/initiative_consumable/card?group=consumable');
        $response->assertOk();
        $this->assertSame('consumable', $response->json('card.group'));
        $this->assertSame('0', $response->json('card.default_value'));
    }

    public function test_guesses_group_when_omitted(): void
    {
        $char = Characteristic::create([
            'key' => 'hit_bonus_object',
            'name' => 'Touche',
            'type' => 'int',
            'group' => 'object',
            'sort_order' => 1,
        ]);

        CharacteristicObject::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '0',
            'max' => '5',
            'formula' => '{"1":"0","characteristic":"level"}',
            'formula_display' => '{"1":"0","characteristic":"level"}',
        ]);

        $response = $this->getJson('/api/characteristics/hit_bonus_object/card');
        $response->assertOk();
        $this->assertSame('object', $response->json('card.group'));
    }

    public function test_rejects_invalid_group(): void
    {
        $char = Characteristic::create([
            'key' => 'x_creature',
            'name' => 'X',
            'type' => 'int',
            'group' => 'creature',
            'sort_order' => 1,
        ]);
        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
        ]);

        $response = $this->getJson('/api/characteristics/x_creature/card?group=invalid');
        $response->assertStatus(422);
    }

    public function test_returns_404_for_unknown_key(): void
    {
        $this->getJson('/api/characteristics/missing_key/card')->assertNotFound();
    }
}
