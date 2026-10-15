<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\User;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests Feature pour Admin\CharacteristicController.
 *
 * Prérequis : tables characteristics + characteristic_creature/object/spell (nouvelle structure).
 * RefreshDatabase exécute les migrations ; les seeders peuplent les caractéristiques.
 *
 * Vérifie que :
 * - Un invité est redirigé vers la connexion
 * - Un utilisateur (non admin) reçoit 403
 * - Un admin peut accéder à la liste, au détail, à la mise à jour et à l'API formula-preview
 * - Un super_admin a les mêmes accès
 */
class CharacteristicControllerTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCharacteristics();
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->app->make(CharacteristicGetterService::class)->clearCache();
    }

    private function seedCharacteristics(): void
    {
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
    }

    public function test_guest_redirected_to_login_on_index(): void
    {
        $response = $this->get(route('admin.characteristics.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_on_show(): void
    {
        $response = $this->get(route('admin.characteristics.show', 'life_points_creature'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_on_create(): void
    {
        $response = $this->get(route('admin.characteristics.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_receives_403_on_index(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.characteristics.index'));

        $response->assertForbidden();
    }

    public function test_game_master_without_password_is_redirected_from_index(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $response = $this->actingAs($gm)->get(route('admin.characteristics.index'));

        $response->assertRedirect(route('password.confirm'));
    }

    public function test_game_master_with_password_can_access_index(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $response = $this->actingAsConfirmed($gm)->get(route('admin.characteristics.index'));

        $response->assertOk();
    }

    public function test_user_receives_403_on_show(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.characteristics.show', 'life_points_creature'));

        $response->assertForbidden();
    }

    public function test_user_receives_403_on_create(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.characteristics.create'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_create(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->get(route('admin.characteristics.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
            ->where('createMode', true)
            ->has('groups')
            ->has('entitiesByGroup')
        );
    }

    public function test_admin_can_store_characteristic(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $uniqueKey = 'test_store_pa_creature';

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.characteristics.store'), [
                'key' => $uniqueKey,
                'name' => 'Points d\'action (test)',
                'short_name' => 'PA',
                'type' => 'int',
                'group' => 'creature',
                'entities' => [
                    ['entity' => '*', 'min' => '0', 'max' => '20', 'db_column' => 'pa', 'conversion_formula' => null, 'sort_order' => 0],
                ],
            ]);

        $response->assertRedirect(route('admin.characteristics.show', $uniqueKey));
        $this->assertDatabaseHas('characteristics', ['key' => $uniqueKey, 'name' => 'Points d\'action (test)']);
        $this->assertDatabaseHas('characteristic_creature', ['entity' => '*']);
    }

    public function test_store_automatically_adds_group_suffix_to_key(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $keyWithoutSuffix = 'test_life_dice';

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.characteristics.store'), [
                'key' => $keyWithoutSuffix,
                'name' => 'Dé de vie (test)',
                'type' => 'int',
                'group' => 'creature',
                'entities' => [
                    ['entity' => '*', 'min' => '4', 'max' => '20', 'db_column' => 'life_dice', 'conversion_formula' => null, 'sort_order' => 0],
                ],
            ]);

        $expectedKey = 'test_life_dice_creature';
        $response->assertRedirect(route('admin.characteristics.show', $expectedKey));
        $this->assertDatabaseHas('characteristics', ['key' => $expectedKey, 'name' => 'Dé de vie (test)']);
        $this->assertDatabaseMissing('characteristics', ['key' => $keyWithoutSuffix]);
    }

    public function test_store_validation_fails_duplicate_key(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.characteristics.store'), [
                'key' => 'life_points_creature',
                'name' => 'Vie',
                'type' => 'int',
                'group' => 'creature',
                'entities' => [],
            ]);

        $response->assertSessionHasErrors('key');
    }

    public function test_admin_can_access_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->get(route('admin.characteristics.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
            ->where('selected', null)
        );
    }

    public function test_admin_can_access_show(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->get(route('admin.characteristics.show', 'life_points_creature'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
            ->has('selected')
            ->where('selected.id', 'life_points_creature')
            ->has('scrappingMappingsUsingThis')
            ->has('characteristicsForConvertToLinked')
        );
    }

    public function test_admin_can_update_characteristic(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = Characteristic::where('key', 'life_points_creature')->first();
        $this->assertNotNull($char);

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->patch(route('admin.characteristics.update', 'life_points_creature'), [
                'name' => 'Points de vie (modifié)',
                'short_name' => $char->short_name,
                'description' => $char->descriptions,
                'icon' => $char->icon,
                'color' => $char->color,
                'type' => 'int',
                'unit' => $char->unit,
                'sort_order' => $char->sort_order,
                'entities' => [],
            ]);

        $response->assertRedirect(route('admin.characteristics.show', 'life_points_creature'));
        $this->assertDatabaseHas('characteristics', [
            'key' => 'life_points_creature',
            'name' => 'Points de vie (modifié)',
        ]);
    }

    public function test_admin_can_call_formula_preview(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $url = route('admin.characteristics.formula-preview').'?'.http_build_query([
            'characteristic_id' => 'life_points_creature',
            'entity' => 'monster',
            'variable' => 'level',
        ]);
        $response = $this->actingAsConfirmed($admin)->getJson($url);

        $response->assertOk();
        $response->assertJsonStructure(['points']);
        $data = $response->json();
        $this->assertIsArray($data['points']);
    }

    public function test_formula_preview_accepts_optional_formula_param(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->getJson(route('admin.characteristics.formula-preview').'?'.http_build_query([
            'characteristic_id' => 'life_points_creature',
            'entity' => 'monster',
            'variable' => 'level',
            'formula' => '[level] * 2',
        ]));

        $response->assertOk();
        $data = $response->json();
        $this->assertIsArray($data['points']);
        $this->assertGreaterThan(0, count($data['points']));
        $first = $data['points'][0];
        $this->assertArrayHasKey('x', $first);
        $this->assertArrayHasKey('y', $first);
        $this->assertEquals(2, $data['points'][0]['y']); // x=1 (min) -> [level]*2 = 2 (int ou float en JSON)
    }

    public function test_formula_preview_returns_empty_points_when_entity_invalid(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $url = route('admin.characteristics.formula-preview').'?'.http_build_query([
            'characteristic_id' => 'life_points_creature',
            'entity' => 'invalid_entity',
        ]);
        $response = $this->actingAsConfirmed($admin)->getJson($url);

        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('points', $data);
        $this->assertSame([], $data['points']);
    }

    public function test_super_admin_can_access_index(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAsConfirmed($superAdmin)->get(route('admin.characteristics.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
        );
    }

    public function test_super_admin_can_update_characteristic(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $char = Characteristic::where('key', 'level_creature')->first();
        $this->assertNotNull($char);

        $response = $this->actingAsConfirmed($superAdmin)
            ->withSession($this->passwordConfirmedSession())
            ->patch(route('admin.characteristics.update', 'level_creature'), [
                'name' => 'Niveau (modifié)',
                'short_name' => $char->short_name,
                'description' => $char->descriptions,
                'icon' => $char->icon,
                'color' => $char->color,
                'type' => 'int',
                'unit' => $char->unit,
                'sort_order' => $char->sort_order,
                'entities' => [],
            ]);

        $response->assertRedirect(route('admin.characteristics.show', 'level_creature'));
        $this->assertDatabaseHas('characteristics', [
            'key' => 'level_creature',
            'name' => 'Niveau (modifié)',
        ]);
    }

    public function test_admin_show_returns_error_when_characteristic_not_found(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->get(route('admin.characteristics.show', 'nonexistent_key_creature'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->where('selected', null));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_destroy_characteristic(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = Characteristic::create([
            'key' => 'test_destroy_me_creature',
            'name' => 'À supprimer',
            'type' => 'int',
            'sort_order' => 999,
            'group' => 'creature',
        ]);
        CharacteristicCreature::create([
            'characteristic_id' => $char->id,
            'entity' => '*',
            'db_column' => 'test_destroy',
            'min' => '0',
            'max' => '100',
        ]);
        $key = $char->key;

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->delete(route('admin.characteristics.destroy', $key));

        $response->assertRedirect(route('admin.characteristics.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('characteristics', ['key' => $key]);
    }

    public function test_admin_cannot_destroy_characteristic_with_linked(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $master = Characteristic::where('key', 'life_points_creature')->first();
        $this->assertNotNull($master);
        // Créer une caractéristique liée (level_object par ex. si level_creature existe)
        $levelCreature = Characteristic::where('key', 'level_creature')->first();
        if ($levelCreature === null) {
            $this->markTestSkipped('level_creature requis pour le test des liées.');
        }
        $key = 'level_object_linked_test';
        Characteristic::query()->where('key', $key)->delete();
        Characteristic::create([
            'key' => $key,
            'name' => 'Niveau objet (test liée)',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'object',
            'linked_to_characteristic_id' => $levelCreature->id,
        ]);

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->delete(route('admin.characteristics.destroy', 'level_creature'));

        $response->assertRedirect(route('admin.characteristics.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('characteristics', ['key' => 'level_creature']);
    }

    public function test_admin_can_call_suggest_conversion_formula(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $payload = [
            'pairs' => [['d' => 10, 'k' => 1], ['d' => 200, 'k' => 20]],
            'curve_type' => 'table',
        ];
        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->postJson(route('admin.characteristics.suggest-conversion-formula'), $payload);

        $response->assertOk();
        $response->assertJsonStructure(['formula']);
        $data = $response->json();
        $this->assertIsString($data['formula']);
    }

    /**
     * Test : Un admin peut uploader une icône pour une caractéristique (Media Library).
     */
    public function test_admin_can_upload_characteristic_icon(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $characteristic = Characteristic::first();
        $this->assertNotNull($characteristic);
        $file = UploadedFile::fake()->image('icon.png', 64, 64);

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->postJson(route('admin.characteristics.upload-icon'), [
                'file' => $file,
                'characteristic_id' => $characteristic->id,
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('icon', fn ($url) => is_string($url) && $url !== '');
        $characteristic->refresh();
        $this->assertCount(1, $characteristic->getMedia('icons'));
        $this->assertNotEmpty($characteristic->icon);
    }

    /**
     * Options de mapping scrapping : invité non autorisé (401 pour JSON, ou redirection login pour GET).
     */
    public function test_guest_redirected_on_scrapping_mapping_options(): void
    {
        $url = route('admin.characteristics.scrapping-mapping-options', ['characteristic_key' => 'life_points_creature']).'?entity=monster';
        $response = $this->getJson($url);
        $response->assertStatus(401);
    }

    /**
     * Sans entity : retourne la liste des entités pour le groupe (modal étape 1).
     */
    public function test_admin_can_get_scrapping_mapping_entities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $url = route('admin.characteristics.scrapping-mapping-options', ['characteristic_key' => 'life_points_creature']);
        $response = $this->actingAsConfirmed($admin)->getJson($url);
        $response->assertOk();
        $response->assertJsonStructure(['entities']);
        $entities = $response->json('entities');
        $this->assertIsArray($entities);
        $this->assertGreaterThan(0, count($entities));
    }

    /**
     * Avec entity : retourne les chemins (paths) depuis le JSON entité.
     */
    public function test_admin_can_get_scrapping_mapping_paths(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $url = route('admin.characteristics.scrapping-mapping-options', ['characteristic_key' => 'life_points_creature']).'?entity=monster';
        $response = $this->actingAsConfirmed($admin)->getJson($url);
        $response->assertOk();
        $response->assertJsonStructure(['paths']);
        $this->assertIsArray($response->json('paths'));
    }

    /**
     * Un admin peut créer une règle de mapping depuis un chemin (store) et la lier à la caractéristique.
     */
    public function test_admin_can_store_scrapping_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = Characteristic::where('key', 'life_points_creature')->first();
        $this->assertNotNull($char);
        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->postJson(
                route('admin.characteristics.store-scrapping-mapping', ['characteristic_key' => 'life_points_creature']),
                ['entity' => 'monster', 'from_path' => 'grades.0.lifePoints']
            );
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('scrapping_entity_mappings', [
            'characteristic_id' => $char->id,
            'entity' => 'monster',
            'from_path' => 'grades.0.lifePoints',
        ]);
    }

    /**
     * Un admin peut délier une règle de mapping d'une caractéristique.
     */
    public function test_admin_can_unlink_scrapping_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = Characteristic::where('key', 'life_points_creature')->first();
        $this->assertNotNull($char);
        $mapping = ScrappingEntityMapping::firstOrCreate(
            [
                'source' => 'dofusdb',
                'entity' => 'monster',
                'mapping_key' => 'life',
            ],
            [
                'from_path' => 'grades.0.lifePoints',
                'characteristic_id' => $char->id,
                'sort_order' => 0,
            ]
        );
        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->postJson(
                route('admin.characteristics.unlink-scrapping-mapping', ['characteristic_key' => 'life_points_creature']),
                ['mapping_id' => $mapping->id]
            );
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $mapping->refresh();
        $this->assertNull($mapping->characteristic_id);
    }

    public function test_admin_can_preview_norms_with_power_coefficients(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->postJson(
            route('admin.characteristics.norms-preview', 'dommages_spell'),
            [
                'norms_formula' => 'round(3*pow([level],0.633))',
                'entity' => '*',
                'norms_power_coefficients' => [
                    'very_weak' => 0.5,
                    'weak' => 0.75,
                    'strong' => 1.3,
                    'very_strong' => 1.7,
                ],
            ]
        );

        $response->assertOk();
        $grid = $response->json('grid');
        $this->assertIsArray($grid);
        $this->assertEquals(3, $grid['neutral'][0]);
        $this->assertEquals(20, $grid['neutral'][19]);
        $this->assertEquals(2, $grid['very_weak'][0]);
        $this->assertEquals(34, $grid['very_strong'][19]);
    }

    public function test_norms_preview_rejects_power_with_coefficients(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAsConfirmed($admin)->postJson(
            route('admin.characteristics.norms-preview', 'dommages_spell'),
            [
                'norms_formula' => 'round(10+[level]+[power])',
                'norms_power_coefficients' => [
                    'very_weak' => 0.5,
                    'weak' => 0.75,
                    'strong' => 1.3,
                    'very_strong' => 1.7,
                ],
            ]
        );

        $response->assertStatus(422);
        $this->assertStringContainsString('incompatible', (string) $response->json('message'));
    }

    public function test_admin_update_persists_norms_power_coefficients_and_regenerates_grid(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = Characteristic::where('key', 'dommages_spell')->first();
        $this->assertNotNull($char);
        $existing = \App\Models\CharacteristicSpell::query()
            ->where('characteristic_id', $char->id)
            ->where('entity', '*')
            ->first();
        $this->assertNotNull($existing);

        $payload = [
            'name' => $char->name,
            'short_name' => $char->short_name,
            'description' => $char->descriptions,
            'helper' => $char->helper,
            'icon' => $char->icon,
            'color' => $char->color,
            'type' => $char->type,
            'status' => $char->status,
            'unit' => $char->unit,
            'sort_order' => $char->sort_order,
            'nature' => $char->nature instanceof \BackedEnum ? $char->nature->value : $char->nature,
            'hide_when_empty' => false,
            'hide_when_false' => false,
            'value_overrides' => [],
            'entity_override_keys' => [],
            'entities' => [
                [
                    'entity' => '*',
                    'db_column' => $existing->db_column,
                    'min' => $existing->min,
                    'max' => $existing->max,
                    'formula' => $existing->formula,
                    'formula_display' => $existing->formula_display,
                    'default_value' => $existing->default_value,
                    'conversion_formula' => $existing->conversion_formula,
                    'conversion_function' => $existing->conversion_function,
                    'norms_formula' => 'round(3*pow([level],0.633))',
                    'norms_power_coefficients' => [
                        'very_weak' => 0.5,
                        'weak' => 0.75,
                        'strong' => 1.3,
                        'very_strong' => 1.7,
                    ],
                    'norms_conditions' => [[
                        'characteristic_key' => 'resolution_mode_spell',
                        'operator' => '=',
                        'value' => 'auto_success',
                        'target' => 'power',
                        'modifier' => -1,
                        'comment' => 'Touche automatique',
                    ]],
                    'norms_description' => 'Test coefs',
                    'norms_grid' => null,
                ],
            ],
        ];

        $response = $this->actingAsConfirmed($admin)
            ->withSession($this->passwordConfirmedSession())
            ->patch(route('admin.characteristics.update', 'dommages_spell'), $payload);

        $response->assertRedirect(route('admin.characteristics.show', 'dommages_spell'));

        $existing->refresh();
        $this->assertSame('round(3*pow([level],0.633))', $existing->norms_formula);
        $this->assertEquals([
            'very_weak' => 0.5,
            'weak' => 0.75,
            'strong' => 1.3,
            'very_strong' => 1.7,
        ], $existing->norms_power_coefficients);
        $this->assertEquals(3, $existing->norms_grid['neutral'][0]);
        $this->assertEquals(20, $existing->norms_grid['neutral'][19]);
        $this->assertSame('auto_success', $existing->norms_conditions[0]['value']);
    }
}
