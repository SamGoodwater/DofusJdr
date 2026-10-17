<?php

namespace Tests\Feature\Api\Table;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\CreatureTrait;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour CreatureTraitTableController
 *
 * @description
 * Vérifie que :
 * - Le format `entities` retourne les données brutes
 * - Le format par défaut (`cells`) retourne les cellules formatées
 * - Les permissions sont respectées
 */
class CreatureTraitTableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    /**
     * Test : Le format `entities` retourne les données brutes
     */
    public function test_format_entities_returns_raw_data(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'name' => 'Test CreatureTrait',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/creature-traits?format=entities&limit=10');

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'entityType',
                    'query',
                    'capabilities',
                    'format',
                ],
                'entities' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertEquals('entities', $data['meta']['format']);
        $this->assertArrayHasKey('entities', $data);
        $this->assertArrayNotHasKey('rows', $data);
        $this->assertCount(1, $data['entities']);
        $this->assertEquals('Test CreatureTrait', $data['entities'][0]['name']);
    }

    /**
     * Test : Le format par défaut (`cells`) retourne les cellules formatées
     */
    public function test_format_cells_returns_formatted_cells(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'name' => 'Test CreatureTrait',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/creature-traits?limit=10');

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'entityType',
                    'query',
                    'capabilities',
                ],
                'rows' => [
                    '*' => [
                        'id',
                        'cells' => [
                            'name',
                        ],
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('rows', $data);
        $this->assertArrayNotHasKey('entities', $data);
        $this->assertArrayHasKey('cells', $data['rows'][0]);
        $this->assertEquals('route', $data['rows'][0]['cells']['name']['type']);
    }

    /**
     * Test : Le format `entities` inclut les relations
     */
    public function test_entities_format_includes_relations(): void
    {
        $user = User::factory()->create();
        $creatureTrait = CreatureTrait::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/creature-traits?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $entity = $data['entities'][0];
        $this->assertArrayHasKey('createdBy', $entity);
        $this->assertNotNull($entity['createdBy']);
        $this->assertEquals($user->id, $entity['createdBy']['id']);
    }

    /**
     * Test : Le format `entities` respecte les permissions
     */
    public function test_entities_format_respects_permissions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        CreatureTrait::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/creature-traits?format=entities&limit=10');

        $response->assertOk();

        $data = $response->json();
        $this->assertArrayHasKey('capabilities', $data['meta']);
        $this->assertIsBool($data['meta']['capabilities']['viewAny']);
        $this->assertIsBool($data['meta']['capabilities']['updateAny']);
    }

    /**
     * Le filtre Jouable arrive en tableau (`filters[state][]`) et ne doit pas casser la liste.
     */
    public function test_playable_state_filter_as_array_returns_only_playable(): void
    {
        $user = User::factory()->create();
        CreatureTrait::factory()->create([
            'name' => 'Trait jouable',
            'state' => CreatureTrait::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'created_by' => $user->id,
        ]);
        CreatureTrait::factory()->create([
            'name' => 'Trait brouillon',
            'state' => CreatureTrait::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/creature-traits?format=entities&limit=25&filters[state][]=playable');

        $response->assertOk();
        $names = array_column($response->json('entities'), 'name');
        $this->assertContains('Trait jouable', $names);
        $this->assertNotContains('Trait brouillon', $names);
    }

    /**
     * Test : Le format `entities` gère la pagination/limite
     */
    public function test_entities_format_respects_limit(): void
    {
        $user = User::factory()->create();
        // La factory CreatureTrait utilise unique()->randomElement() avec seulement 6 valeurs possibles
        // Créer seulement 5 entités pour éviter les collisions
        CreatureTrait::factory()->count(5)->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/tables/creature-traits?format=entities&limit=3');

        $response->assertOk();

        $data = $response->json();
        $this->assertLessThanOrEqual(3, count($data['entities']));
        $this->assertEquals(3, $data['meta']['query']['limit']);
    }
}
