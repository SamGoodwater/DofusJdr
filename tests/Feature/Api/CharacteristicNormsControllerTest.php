<?php

namespace Tests\Feature\Api;

use App\Enums\SectionType;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour l'endpoint GET /api/characteristics/{key}/norms/{entity?}
 */
class CharacteristicNormsControllerTest extends TestCase
{
    use RefreshDatabase;

    private function seedCharacteristic(string $key = 'strength_creature', array $overrides = []): Characteristic
    {
        $char = Characteristic::create(array_merge([
            'key' => $key,
            'name' => 'Force',
            'type' => 'int',
            'sort_order' => 1,
        ], $overrides));

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '1',
            'max' => '20',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 5),
                'neutral' => array_fill(0, 20, 10),
                'strong' => array_fill(0, 20, 15),
                'very_strong' => array_fill(0, 20, 20),
            ],
            'norms_conditions' => [
                [
                    'characteristic_key' => 'level_creature',
                    'operator' => '>=',
                    'value' => 10,
                    'target' => 'power',
                    'modifier' => 1,
                    'comment' => 'Haut niveau',
                ],
            ],
            'norms_description' => 'Norme de test pour la force.',
        ]);

        return $char;
    }

    public function test_returns_norms_for_valid_characteristic(): void
    {
        $this->seedCharacteristic();

        $response = $this->getJson('/api/characteristics/strength_creature/norms');

        $response->assertOk();
        $response->assertJsonStructure([
            'characteristic' => ['key', 'name', 'icon', 'color'],
            'norms' => ['grid', 'formula', 'power_coefficients', 'conditions', 'description', 'limits' => ['min', 'max'], 'help_section'],
            'power_levels',
            'max_level',
        ]);

        $data = $response->json();
        $this->assertEquals('strength_creature', $data['characteristic']['key']);
        $this->assertCount(5, $data['norms']['grid']);
        $this->assertCount(1, $data['norms']['conditions']);
        $this->assertEquals('Norme de test pour la force.', $data['norms']['description']);
        $this->assertSame('1', $data['norms']['limits']['min']);
        $this->assertSame('20', $data['norms']['limits']['max']);
        $this->assertEquals(20, $data['max_level']);
        $this->assertNull($data['norms']['help_section']);
        $this->assertNull($data['norms']['power_coefficients']);
    }

    public function test_returns_power_coefficients_when_set(): void
    {
        $char = Characteristic::create([
            'key' => 'damage_spell',
            'name' => 'Dégâts',
            'type' => 'int',
            'sort_order' => 1,
            'group' => 'spell',
        ]);
        $coefs = [
            'very_weak' => 0.5,
            'weak' => 0.75,
            'strong' => 1.3,
            'very_strong' => 1.7,
        ];
        \App\Models\CharacteristicSpell::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'min' => '0',
            'max' => '80',
            'norms_formula' => 'round(3*pow([level],0.633))',
            'norms_power_coefficients' => $coefs,
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 2),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
        ]);

        $response = $this->getJson('/api/characteristics/damage_spell/norms?group=spell');
        $response->assertOk();
        $this->assertEquals($coefs, $response->json('norms.power_coefficients'));
        $this->assertSame('round(3*pow([level],0.633))', $response->json('norms.formula'));
    }

    public function test_returns_help_section_html_when_norms_help_section_id_set(): void
    {
        $page = Page::factory()->create();
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'template' => SectionType::TEXT,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'data' => ['content' => '<p>Règle liée aux normes</p>'],
        ]);

        $char = Characteristic::create([
            'key' => 'with_help_creature',
            'name' => 'Avec aide',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
            'norms_help_section_id' => $section->id,
        ]);

        $response = $this->getJson('/api/characteristics/with_help_creature/norms');
        $response->assertOk();
        $help = $response->json('norms.help_section');
        $this->assertIsArray($help);
        $this->assertSame($section->id, $help['id']);
        $this->assertStringContainsString('Règle liée aux normes', $help['html']);
    }

    public function test_help_section_hidden_when_read_level_too_high_for_guest(): void
    {
        $page = Page::factory()->create();
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'template' => SectionType::TEXT,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'data' => ['content' => '<p>Secret MJ</p>'],
        ]);

        $char = Characteristic::create([
            'key' => 'help_restricted_creature',
            'name' => 'Restreint',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
            'norms_help_section_id' => $section->id,
        ]);

        $response = $this->getJson('/api/characteristics/help_restricted_creature/norms');
        $response->assertOk();
        $this->assertNull($response->json('norms.help_section'));
    }

    public function test_help_section_visible_when_user_meets_read_level(): void
    {
        $page = Page::factory()->create();
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'template' => SectionType::TEXT,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'data' => ['content' => '<p>Contenu MJ</p>'],
        ]);

        $char = Characteristic::create([
            'key' => 'help_mj_creature',
            'name' => 'MJ',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 1),
                'weak' => array_fill(0, 20, 2),
                'neutral' => array_fill(0, 20, 3),
                'strong' => array_fill(0, 20, 4),
                'very_strong' => array_fill(0, 20, 5),
            ],
            'norms_help_section_id' => $section->id,
        ]);

        $mj = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $response = $this->actingAs($mj)->getJson('/api/characteristics/help_mj_creature/norms');
        $response->assertOk();
        $help = $response->json('norms.help_section');
        $this->assertIsArray($help);
        $this->assertStringContainsString('Contenu MJ', $help['html']);
    }

    public function test_returns_404_for_unknown_characteristic(): void
    {
        $response = $this->getJson('/api/characteristics/unknown_key/norms');
        $response->assertNotFound();
    }

    public function test_returns_null_norms_when_no_grid(): void
    {
        $char = Characteristic::create([
            'key' => 'empty_creature',
            'name' => 'Vide',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
        ]);

        $response = $this->getJson('/api/characteristics/empty_creature/norms');
        $response->assertOk();
        $this->assertNull($response->json('norms'));
    }

    public function test_norms_with_specific_entity(): void
    {
        $char = Characteristic::create([
            'key' => 'str_creature',
            'name' => 'Force',
            'type' => 'int',
            'sort_order' => 1,
        ]);

        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => 'monster',
            'norms_grid' => [
                'very_weak' => array_fill(0, 20, 2),
                'weak' => array_fill(0, 20, 4),
                'neutral' => array_fill(0, 20, 8),
                'strong' => array_fill(0, 20, 12),
                'very_strong' => array_fill(0, 20, 16),
            ],
            'norms_description' => 'Normes monstres',
        ]);

        $response = $this->getJson('/api/characteristics/str_creature/norms/monster');
        $response->assertOk();
        $this->assertEquals('Normes monstres', $response->json('norms.description'));
    }

    public function test_power_levels_are_returned(): void
    {
        $this->seedCharacteristic();

        $response = $this->getJson('/api/characteristics/strength_creature/norms');
        $response->assertOk();

        $powerLevels = $response->json('power_levels');
        $this->assertCount(5, $powerLevels);
        $this->assertArrayHasKey('very_weak', $powerLevels);
        $this->assertArrayHasKey('neutral', $powerLevels);
        $this->assertArrayHasKey('very_strong', $powerLevels);
    }

    public function test_rejects_invalid_group_query_parameter(): void
    {
        $this->seedCharacteristic();

        $response = $this->getJson('/api/characteristics/strength_creature/norms?group=invalid');
        $response->assertStatus(422);
        $this->assertStringContainsString('Groupe invalide', (string) $response->json('error'));
    }

    public function test_group_query_parameter_filters_lookup_to_requested_group(): void
    {
        $this->seedCharacteristic();

        // La caractéristique existe en creature ; en forçant group=spell, on ne doit rien trouver.
        $response = $this->getJson('/api/characteristics/strength_creature/norms?group=spell');
        $response->assertOk();
        $this->assertNull($response->json('norms'));
    }
}
