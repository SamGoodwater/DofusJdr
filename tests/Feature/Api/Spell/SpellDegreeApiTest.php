<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Spell;

use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class SpellDegreeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_game_master_can_create_update_and_materialize_degrees(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0, 'pa' => '3']);
        $creatureId = DB::table('creatures')->insertGetId([
            'name' => 'Invocation test',
            'res_fixe_neutre' => '0',
            'res_fixe_terre' => '0',
            'res_fixe_feu' => '0',
            'res_fixe_air' => '0',
            'res_fixe_eau' => '0',
            'created_by' => $gm->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $creature = Creature::query()->findOrFail($creatureId);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-api-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);

        $create = $this->actingAs($gm)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
            'area' => 'point',
            'effects' => [
                [
                    'sub_effect_id' => $sub->id,
                    'params' => [
                        'value_formula' => '2d6',
                        'creature_id' => $creature->id,
                    ],
                ],
            ],
        ]);
        $create->assertCreated();
        $degreeId = (int) $create->json('degree_id');
        $this->assertGreaterThan(0, $degreeId);
        $this->assertCount(1, $create->json('data.degrees'));
        $this->assertSame(
            $creature->id,
            $create->json('data.degrees.0.rows.0.summon_monster.creature_id')
        );

        $second = $this->actingAs($gm)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 10,
            'pa' => '4',
            'area' => 'circle-1-1',
        ]);
        $second->assertCreated();
        $secondId = (int) $second->json('degree_id');
        $degrees = $second->json('data.degrees');
        $this->assertCount(2, $degrees);
        $this->assertFalse($degrees[1]['inherits_effects']);
        $this->assertSame('own', $degrees[1]['properties_source']);
        $this->assertCount(1, $degrees[1]['rows']);

        $this->actingAs($gm)->putJson("/api/spells/{$spell->id}/degrees/{$secondId}/effects", [
            'effects' => [
                [
                    'sub_effect_id' => $sub->id,
                    'params' => ['value_formula' => '4d6'],
                ],
            ],
        ])->assertOk();

        $index = $this->actingAs($gm)->getJson("/api/spells/{$spell->id}/degrees");
        $index->assertOk();
        $this->assertSame('4d6', $index->json('data.degrees.1.rows.0.params.value_formula'));
    }

    public function test_game_master_can_sync_degrees_bulk(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0, 'pa' => '3']);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-bulk-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);

        $first = $this->actingAs($gm)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
            'area' => 'point',
            'effects' => [[
                'sub_effect_id' => $sub->id,
                'params' => ['value_formula' => '1d6'],
            ]],
        ]);
        $first->assertCreated();
        $degreeId = (int) $first->json('degree_id');

        $bulk = $this->actingAs($gm)->putJson("/api/spells/{$spell->id}/degrees", [
            'degrees' => [[
                'id' => $degreeId,
                'required_level' => 2,
                'properties_source' => 'spell',
                'pa' => '5',
                'inherits_effects' => false,
                'effects' => [[
                    'sub_effect_id' => $sub->id,
                    'params' => [
                        'value_formula' => '2d6',
                        'value_formula_crit' => '3d6',
                    ],
                ]],
            ]],
        ]);
        $bulk->assertOk();
        $this->assertSame(2, $bulk->json('data.degrees.0.required_level'));
        $this->assertSame('spell', $bulk->json('data.degrees.0.properties_source'));
        $this->assertSame('2d6', $bulk->json('data.degrees.0.rows.0.params.value_formula'));
        $this->assertSame('3d6', $bulk->json('data.degrees.0.rows.0.params.value_formula_crit'));
    }

    /**
     * SpellPolicy::update autorise l’auteur (rôle user) : l’API degrés doit suivre,
     * sinon l’enregistrement unique de l’éditeur échoue en 403.
     */
    public function test_author_user_can_create_and_sync_degrees(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create(['created_by' => $author->id, 'write_level' => 0, 'pa' => '3']);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-author-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [],
        ]);

        $create = $this->actingAs($author)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
            'area' => 'point',
            'effects' => [[
                'sub_effect_id' => $sub->id,
                'params' => ['value_formula' => '1d6'],
            ]],
        ]);
        $create->assertCreated();
        $degreeId = (int) $create->json('degree_id');

        $bulk = $this->actingAs($author)->putJson("/api/spells/{$spell->id}/degrees", [
            'degrees' => [[
                'id' => $degreeId,
                'required_level' => 2,
                'inherits_effects' => false,
                'effects' => [[
                    'sub_effect_id' => $sub->id,
                    'params' => ['value_formula' => '2d6'],
                ]],
            ]],
        ]);
        $bulk->assertOk();
        $this->assertSame(2, $bulk->json('data.degrees.0.required_level'));
        $this->assertSame('2d6', $bulk->json('data.degrees.0.rows.0.params.value_formula'));
    }

    public function test_non_author_user_cannot_mutate_degrees(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create(['created_by' => $owner->id, 'write_level' => 0, 'pa' => '3']);

        $this->actingAs($other)->postJson("/api/spells/{$spell->id}/degrees", [
            'required_level' => 1,
            'pa' => '3',
        ])->assertForbidden();
    }
}
