<?php

namespace Tests\Feature\Api;

use App\Models\Characteristic;
use App\Models\CharacteristicConsumable;
use App\Models\CharacteristicSpell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacteristicNormsCatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_catalog_for_spell_group(): void
    {
        $char = Characteristic::create([
            'key' => 'test_damage_spell',
            'name' => 'Test dégâts',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicSpell::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
        ]);

        $response = $this->getJson('/api/characteristics/norms-catalog/spell');

        $response->assertOk();
        $response->assertJsonPath('group', 'spell');
        $response->assertJsonPath('entity', '*');
        $data = $response->json('items');
        $this->assertNotEmpty($data);
        $keys = array_column($data, 'key');
        $this->assertContains('test_damage_spell', $keys);
    }

    public function test_returns_404_for_invalid_group(): void
    {
        $response = $this->getJson('/api/characteristics/norms-catalog/invalid');

        $response->assertNotFound();
    }

    public function test_returns_catalog_for_all_groups(): void
    {
        $char = Characteristic::create([
            'key' => 'all_groups_spell',
            'name' => 'All groups',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicSpell::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
        ]);

        $response = $this->getJson('/api/characteristics/norms-catalog/all');
        $response->assertOk();
        $this->assertSame('all', $response->json('group'));
        $keys = array_column($response->json('items'), 'key');
        $this->assertContains('all_groups_spell', $keys);
        $this->assertSame('spell', $response->json('items.0.group'));
    }

    public function test_filters_by_keys_query(): void
    {
        $a = Characteristic::create([
            'key' => 'alpha_spell',
            'name' => 'Alpha',
            'type' => 'int',
            'sort_order' => 1,
        ]);
        $b = Characteristic::create([
            'key' => 'beta_spell',
            'name' => 'Beta',
            'type' => 'int',
            'sort_order' => 2,
        ]);

        $grid = [
            'very_weak' => array_fill(0, 20, 1),
            'weak' => array_fill(0, 20, 2),
            'neutral' => array_fill(0, 20, 3),
            'strong' => array_fill(0, 20, 4),
            'very_strong' => array_fill(0, 20, 5),
        ];

        CharacteristicSpell::create([
            'characteristic_id' => $a->id,
            'entity' => '*',
            'norms_grid' => $grid,
        ]);
        CharacteristicSpell::create([
            'characteristic_id' => $b->id,
            'entity' => '*',
            'norms_grid' => $grid,
        ]);

        $response = $this->getJson('/api/characteristics/norms-catalog/spell?keys=beta_spell');

        $response->assertOk();
        $this->assertCount(1, $response->json('items'));
        $this->assertSame('beta_spell', $response->json('items.0.key'));
    }

    public function test_returns_catalog_for_consumable_group(): void
    {
        $char = Characteristic::create([
            'key' => 'soin_consumable',
            'name' => 'Soin conso',
            'type' => 'int',
            'group' => 'consumable',
            'sort_order' => 1,
        ]);

        CharacteristicConsumable::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
        ]);

        $response = $this->getJson('/api/characteristics/norms-catalog/consumable');
        $response->assertOk();
        $this->assertSame('consumable', $response->json('group'));
        $keys = array_column($response->json('items'), 'key');
        $this->assertContains('soin_consumable', $keys);
    }
}
