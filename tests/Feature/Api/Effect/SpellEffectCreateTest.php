<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Effect;

use App\Models\Effect;
use App\Models\EffectSubEffect;
use App\Models\Entity\Spell;
use App\Models\SubEffect;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Création d’une définition d’effet depuis la fiche sort.
 */
final class SpellEffectCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_game_master_creates_an_effect_linked_to_the_spell(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0]);

        $response = $this->actingAs($gm)->postJson('/api/effects/spell-effects', [
            'spell_id' => $spell->id,
            'name' => 'Souffle',
            'target_type' => 'glyph',
            'initial_area' => 'circle-1-2',
        ]);

        $response->assertCreated();
        $effectId = (int) $response->json('data.id');
        $this->assertGreaterThan(0, $effectId);
        $this->assertDatabaseHas('effects', [
            'id' => $effectId,
            'name' => 'Souffle',
            'target_type' => Effect::TARGET_GLYPH,
        ]);
        $this->assertDatabaseHas('effect_degrees', [
            'effect_id' => $effectId,
            'degree' => 1,
            'area' => 'circle-1-2',
        ]);
        $this->assertDatabaseHas('effect_spell', [
            'spell_id' => $spell->id,
            'effect_id' => $effectId,
        ]);
    }

    public function test_game_master_can_create_an_effect_with_initial_sub_effects(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0]);
        $sub = SubEffect::query()->create([
            'slug' => 'frapper-test-create',
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => [
                'action' => 'frapper',
                'params' => [
                    ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur'],
                ],
            ],
        ]);

        $response = $this->actingAs($gm)->postJson('/api/effects/spell-effects', [
            'spell_id' => $spell->id,
            'name' => 'Boule de feu',
            'target_type' => 'direct',
            'initial_sub_effects' => [
                [
                    'sub_effect_id' => $sub->id,
                    'order' => 0,
                    'scope' => 'general',
                    'params' => [
                        'characteristic' => 'fire',
                        'value_formula' => '2d6+[intel]',
                    ],
                ],
            ],
        ]);

        $response->assertCreated();
        $effectId = (int) $response->json('data.id');
        $degreeId = (int) \App\Models\EffectDegree::query()
            ->where('effect_id', $effectId)
            ->where('degree', 1)
            ->value('id');
        $this->assertGreaterThan(0, $degreeId);

        $this->assertDatabaseHas('effect_sub_effect', [
            'effect_degree_id' => $degreeId,
            'sub_effect_id' => $sub->id,
            'order' => 0,
        ]);

        $pivot = EffectSubEffect::query()
            ->where('effect_degree_id', $degreeId)
            ->where('sub_effect_id', $sub->id)
            ->first();
        $this->assertNotNull($pivot);
        $this->assertSame('2d6+[intel]', $pivot->params['value_formula'] ?? null);
        $this->assertSame('fire', $pivot->params['characteristic'] ?? null);
    }

    public function test_player_cannot_create_an_effect_from_the_spell_page(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $spell = Spell::factory()->create(['created_by' => $player->id]);

        $this->actingAs($player)->postJson('/api/effects/spell-effects', [
            'spell_id' => $spell->id,
            'name' => 'Interdit',
        ])->assertForbidden();
    }

    public function test_invalid_area_is_rejected(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $gm->id, 'write_level' => 0]);

        $this->actingAs($gm)->postJson('/api/effects/spell-effects', [
            'spell_id' => $spell->id,
            'name' => 'Zone fausse',
            'initial_area' => 'pas-une-zone',
        ])->assertUnprocessable();
    }
}
