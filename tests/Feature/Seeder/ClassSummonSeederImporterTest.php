<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Services\Seeder\Monster\ClassSummonCatalog;
use App\Services\Seeder\Monster\ClassSummonSeederImporter;
use Database\Seeders\SubEffectSeeder;
use Tests\TestCase;

final class ClassSummonSeederImporterTest extends TestCase
{
    public function test_imports_nineteen_summons_and_is_idempotent(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $importer = app(ClassSummonSeederImporter::class);
        $first = $importer->import();

        $this->assertCount(19, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $tofu = Monster::query()->where('official_id', 'jdr:summon:tofu')->first();
        $this->assertNotNull($tofu);
        $this->assertSame('auto', $tofu->state);
        $this->assertFalse($tofu->auto_update);
        $this->assertSame(1, $tofu->size);
        $creature = $tofu->creature;
        $this->assertNotNull($creature);
        $this->assertSame('Tofu', $creature->name);
        $this->assertSame('8', $creature->life);
        $this->assertSame('1', $creature->pm);
        $this->assertSame('3', $creature->pa);
        $this->assertSame(0, $creature->hostility);
        $this->assertSame('https://api.dofusdb.fr/img/monsters/9.png', $creature->image);
        $this->assertSame(Creature::STATE_AUTO, $creature->state);
        $this->assertSame(1, $creature->spells()->count());

        $bec = $creature->spells()->first();
        $this->assertSame(Spell::CATEGORY_CREATURE, $bec?->category);
        $this->assertSame('jdr:summon:tofu:action', $bec?->official_id);
        $this->assertSame(Spell::STATE_AUTO, $bec?->state);
        $this->assertTrue($bec?->spellTypes()->where('name', 'Dégâts')->exists());

        $gardienne = Monster::query()->where('official_id', 'jdr:summon:gardienne')->with([
            'creature.spells.effects.degrees.effectSubEffects.subEffect',
        ])->first();
        $this->assertNotNull($gardienne);
        $soin = $gardienne->creature?->spells->first();
        $this->assertSame(
            'soigner',
            $soin?->effects->first()?->degrees->first()?->effectSubEffects->first()?->subEffect?->slug
        );

        $second = $importer->import();
        $this->assertSame([], $second['created']);
        $this->assertCount(19, $second['updated']);
        $this->assertSame(
            19,
            Monster::query()->where('official_id', 'like', 'jdr:summon:%')->count()
        );
        $this->assertSame(
            19,
            Spell::query()->where('category', Spell::CATEGORY_CREATURE)->where('state', Spell::STATE_AUTO)->count()
        );
        $this->assertSame('8', $tofu->creature?->fresh()->life);
        $this->assertCount(19, ClassSummonCatalog::load()->entries());
    }

    private function seedSpellTypes(): void
    {
        foreach (['Dégâts', 'Amélioration', 'Entrave', 'Placement', 'Soin', 'Protection', 'Invocation'] as $name) {
            SpellType::factory()->create([
                'name' => $name,
                'state' => SpellType::STATE_PLAYABLE,
                'read_level' => 0,
                'write_level' => 3,
                'created_by' => null,
                'show_in_catalog' => true,
            ]);
        }
    }
}
