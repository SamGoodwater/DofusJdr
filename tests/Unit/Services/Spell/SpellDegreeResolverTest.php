<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Spell;

use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Models\SpellDegreeEffect;
use App\Models\SubEffect;
use App\Services\Spell\SpellDegreeResolver;
use App\Services\Spell\SpellDegreeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SpellDegreeResolverTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubEffect(): SubEffect
    {
        return SubEffect::query()->create([
            'slug' => 'frapper-resolver-'.uniqid(),
            'type_slug' => 'frapper',
            'template_text' => 'Dégâts [value].',
            'variables_allowed' => ['value'],
            'param_schema' => ['action' => 'frapper', 'params' => []],
        ]);
    }

    public function test_falls_back_to_spell_properties_without_degrees(): void
    {
        $spell = Spell::factory()->create([
            'pa' => '4',
            'po_min' => '2',
            'po_max' => '8',
            'area' => 'circle-0-2',
            'sight_line' => false,
            'allows_reaction' => true,
            'resolution_mode' => 'saving_throw',
        ]);
        $resolver = app(SpellDegreeResolver::class);

        $props = $resolver->resolveProperties($spell, null);

        $this->assertSame('4', $props['pa']);
        $this->assertSame('spell', $props['pa_source']);
        $this->assertSame('circle-0-2', $props['area']);
        $this->assertSame('spell', $props['area_source']);
        $this->assertArrayNotHasKey('allows_reaction', $props);
        $this->assertArrayNotHasKey('resolution_mode', $props);
        $this->assertNull($resolver->selectDegree($spell));
    }

    public function test_selects_degree_by_creature_level_and_inherits_effects(): void
    {
        $spell = Spell::factory()->create(['pa' => '3']);
        $sub = $this->makeSubEffect();

        $d1 = SpellDegree::factory()->create([
            'spell_id' => $spell->id,
            'position' => 1,
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
            'area' => 'point',
        ]);
        SpellDegreeEffect::factory()->create([
            'spell_degree_id' => $d1->id,
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => ['value_formula' => '2d6'],
        ]);
        $d2 = SpellDegree::factory()->inheriting()->create([
            'spell_id' => $spell->id,
            'position' => 2,
            'required_level' => 10,
            'pa' => '4',
            'area' => 'circle-1-1',
        ]);

        $spell->refresh()->load('degrees.effects.subEffect');
        $resolver = app(SpellDegreeResolver::class);

        $picked = $resolver->selectDegree($spell, null, 12);
        $this->assertNotNull($picked);
        $this->assertSame($d2->id, $picked->id);

        $props = $resolver->resolveProperties($spell, $picked);
        $this->assertSame('4', $props['pa']);
        $this->assertSame('circle-1-1', $props['area']);

        $effects = $resolver->resolveEffects($spell, $picked);
        $this->assertCount(1, $effects);
        $this->assertSame('2d6', $effects->first()->params['value_formula'] ?? null);
        $this->assertSame($d1->id, $resolver->effectsSourceDegree($spell, $picked)?->id);
    }

    public function test_create_degree_copies_previous_properties_and_materializes_effects(): void
    {
        $spell = Spell::factory()->create(['pa' => '5', 'po_min' => '0', 'po_max' => '3']);
        $service = app(SpellDegreeService::class);

        $first = $service->createDegree($spell, [
            'required_level' => 1,
            'inherits_effects' => false,
            'area' => 'point',
            'pa' => '5',
        ]);
        $sub = $this->makeSubEffect();
        $service->syncEffects($first, [[
            'sub_effect_id' => $sub->id,
            'order' => 0,
            'params' => ['value_formula' => '1d6'],
        ]]);

        $second = $service->createDegree($spell->fresh(), ['required_level' => 5]);

        $this->assertSame(2, $second->position);
        $this->assertFalse($second->inherits_effects);
        $this->assertSame(SpellDegree::PROPERTIES_SOURCE_OWN, $second->properties_source);
        $this->assertSame('5', $second->pa);
        $this->assertSame('point', $second->area);
        $this->assertCount(1, $second->effects);
        $this->assertSame('1d6', $second->effects->first()->params['value_formula'] ?? null);
    }

    public function test_properties_source_previous_and_spell(): void
    {
        $spell = Spell::factory()->create(['pa' => '2', 'po_min' => '1', 'po_max' => '1']);
        $d1 = SpellDegree::factory()->create([
            'spell_id' => $spell->id,
            'position' => 1,
            'required_level' => 1,
            'properties_source' => SpellDegree::PROPERTIES_SOURCE_OWN,
            'pa' => '4',
            'area' => 'point',
        ]);
        $d2 = SpellDegree::factory()->create([
            'spell_id' => $spell->id,
            'position' => 2,
            'required_level' => 10,
            'properties_source' => SpellDegree::PROPERTIES_SOURCE_PREVIOUS,
            'pa' => '9',
            'area' => 'circle-0-2',
        ]);
        $d3 = SpellDegree::factory()->create([
            'spell_id' => $spell->id,
            'position' => 3,
            'required_level' => 20,
            'properties_source' => SpellDegree::PROPERTIES_SOURCE_SPELL,
            'pa' => '7',
        ]);

        $spell->refresh()->load('degrees');
        $resolver = app(SpellDegreeResolver::class);

        $fromPrevious = $resolver->resolveProperties($spell, $d2->fresh());
        $this->assertSame('4', $fromPrevious['pa']);
        $this->assertSame('previous', $fromPrevious['pa_source']);
        $this->assertSame('point', $fromPrevious['area']);

        $fromSpell = $resolver->resolveProperties($spell, $d3->fresh());
        $this->assertSame('2', $fromSpell['pa']);
        $this->assertSame('spell', $fromSpell['pa_source']);

        $this->assertSame($d1->id, $resolver->previousDegree($spell, $d2)?->id);
    }

    public function test_materialize_effects_copies_inherited_rows(): void
    {
        $spell = Spell::factory()->create();
        $service = app(SpellDegreeService::class);
        $sub = $this->makeSubEffect();

        $first = $service->createDegree($spell, [
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
        ]);
        $service->syncEffects($first, [[
            'sub_effect_id' => $sub->id,
            'params' => ['value_formula' => '3d6'],
        ]]);
        $second = $service->createDegree($spell->fresh(), [
            'required_level' => 8,
            'inherits_effects' => true,
            'skip_copy_effects' => true,
        ]);

        $this->assertTrue($second->inherits_effects);
        $this->assertCount(0, $second->effects);

        $materialized = $service->materializeEffects($second->fresh());
        $this->assertFalse($materialized->inherits_effects);
        $this->assertCount(1, $materialized->effects);
        $this->assertSame('3d6', $materialized->effects->first()->params['value_formula'] ?? null);
    }

    public function test_deleting_source_degree_materializes_effects_on_inheriting_successor(): void
    {
        $spell = Spell::factory()->create();
        $service = app(SpellDegreeService::class);
        $sub = $this->makeSubEffect();

        $first = $service->createDegree($spell, [
            'required_level' => 1,
            'inherits_effects' => false,
            'pa' => '3',
        ]);
        $service->syncEffects($first, [[
            'sub_effect_id' => $sub->id,
            'params' => ['value_formula' => '2d6'],
        ]]);
        $second = $service->createDegree($spell->fresh(), ['required_level' => 10]);
        $third = $service->createDegree($spell->fresh(), ['required_level' => 16]);

        $this->assertTrue($second->fresh()->inherits_effects);
        $this->assertTrue($third->fresh()->inherits_effects);

        $service->deleteDegree($first->fresh());

        $remaining = SpellDegree::query()
            ->where('spell_id', $spell->id)
            ->orderBy('position')
            ->get();
        $this->assertCount(2, $remaining);
        $this->assertFalse($remaining[0]->inherits_effects);
        $this->assertCount(1, $remaining[0]->effects);
        $this->assertSame('2d6', $remaining[0]->effects->first()->params['value_formula'] ?? null);
        $this->assertTrue($remaining[1]->inherits_effects);

        $spell->refresh()->load('degrees.effects');
        $resolver = app(SpellDegreeResolver::class);
        $this->assertCount(1, $resolver->resolveEffects($spell, $remaining[1]));
        $this->assertSame($remaining[0]->id, $resolver->effectsSourceDegree($spell, $remaining[1])?->id);
    }

    public function test_deleting_inheriting_middle_degree_keeps_source_effects(): void
    {
        $spell = Spell::factory()->create();
        $service = app(SpellDegreeService::class);
        $sub = $this->makeSubEffect();

        $first = $service->createDegree($spell, [
            'required_level' => 1,
            'inherits_effects' => false,
        ]);
        $service->syncEffects($first, [[
            'sub_effect_id' => $sub->id,
            'params' => ['value_formula' => '1d8'],
        ]]);
        $second = $service->createDegree($spell->fresh(), ['required_level' => 8]);
        $third = $service->createDegree($spell->fresh(), ['required_level' => 13]);

        $service->deleteDegree($second->fresh());

        $remaining = SpellDegree::query()
            ->where('spell_id', $spell->id)
            ->orderBy('position')
            ->get();
        $this->assertCount(2, $remaining);
        $this->assertFalse($remaining[0]->inherits_effects);
        $this->assertTrue($remaining[1]->inherits_effects);
        $this->assertCount(1, $remaining[0]->effects);

        $spell->refresh()->load('degrees.effects');
        $this->assertCount(1, app(SpellDegreeResolver::class)->resolveEffects($spell, $remaining[1]));
    }
}
