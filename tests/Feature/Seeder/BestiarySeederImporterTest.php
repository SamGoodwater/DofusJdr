<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Services\Seeder\Monster\BestiarySeederImporter;
use App\Services\Seeder\Monster\IncarnamBestiaryCatalog;
use Database\Seeders\Entity\ConditionSeeder;
use Database\Seeders\Entity\CreatureTraitSeeder;
use Database\Seeders\SubEffectSeeder;
use Database\Seeders\Type\MonsterRaceSeeder;
use Tests\TestCase;

final class BestiarySeederImporterTest extends TestCase
{
    public function test_imports_incarnam_bestiary_and_is_idempotent(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seed(ConditionSeeder::class);
        $this->seed(CreatureTraitSeeder::class);
        $this->seed(MonsterRaceSeeder::class);
        $this->seedSpellTypes();

        $importer = app(BestiarySeederImporter::class);
        $first = $importer->import(IncarnamBestiaryCatalog::load());

        $this->assertCount(14, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $tofu = Monster::query()->where('official_id', 'jdr:bestiary:tofu-chimerique')->first();
        $this->assertNotNull($tofu);
        $this->assertSame('auto', $tofu->state);
        $this->assertFalse($tofu->auto_update);
        $this->assertNull($tofu->dofusdb_id);
        $this->assertFalse((bool) $tofu->is_boss);
        $this->assertSame('', $tofu->boss_pa);
        $this->assertSame(1, $tofu->size);
        $creature = $tofu->creature;
        $this->assertNotNull($creature);
        $this->assertSame('Tofu Chimérique', $creature->name);
        $this->assertSame('22', $creature->life);
        $this->assertSame('4', $creature->pa);
        $this->assertSame('6', $creature->pm);
        $this->assertSame(3, $creature->hostility);
        $this->assertSame('Incarnam', $creature->location);
        $this->assertSame('https://api.dofusdb.fr/img/monsters/540.png', $creature->image);
        $this->assertSame(Creature::STATE_AUTO, $creature->state);
        $this->assertSame(1, $creature->spells()->count());
        $this->assertTrue($creature->creatureTraits()->where('name', 'Petite taille')->exists());
        $this->assertTrue($creature->creatureTraits()->where('name', 'Vif / Vive')->exists());

        $beco = $creature->spells()->first();
        $this->assertSame(Spell::CATEGORY_CREATURE, $beco?->category);
        $this->assertSame('jdr:bestiary:tofu-chimerique:beco', $beco?->official_id);
        $this->assertSame('3', $beco?->pa);
        $this->assertSame(Spell::STATE_AUTO, $beco?->state);

        $arakne = Monster::query()->where('official_id', 'jdr:bestiary:arakne')->with([
            'creature.spells.effects.degrees.effectSubEffects.subEffect',
        ])->first();
        $this->assertNotNull($arakne);
        $this->assertSame(4, $arakne->creature?->hostility);
        $frapperie = $arakne->creature?->spells->first();
        $subs = $frapperie?->effects->first()?->degrees->first()?->effectSubEffects;
        $this->assertNotNull($subs);
        $this->assertCount(2, $subs);
        $this->assertSame(
            ['frapper', 'appliquer-etat'],
            $subs->pluck('subEffect.slug')->all()
        );

        $moskito = Monster::query()->where('official_id', 'jdr:bestiary:moskito')->with([
            'creature.spells.effects.degrees.effectSubEffects',
            'creature.creatureTraits',
        ])->first();
        $this->assertTrue($moskito?->creature?->creatureTraits->contains('name', 'Agile'));
        $piqureParams = $moskito?->creature?->spells->first()
            ?->effects->first()?->degrees->first()?->effectSubEffects->first()?->params;
        $this->assertSame('1d4', $piqureParams['life_steal_formula'] ?? null);

        $boufton = Monster::query()->where('official_id', 'jdr:bestiary:boufton-palichon')->first();
        $this->assertSame(2, $boufton?->creature?->spells()->count());

        $second = $importer->import(IncarnamBestiaryCatalog::load());
        $this->assertSame([], $second['created']);
        $this->assertCount(14, $second['updated']);
        $this->assertSame(
            14,
            Monster::query()->where('official_id', 'like', 'jdr:bestiary:%')->count()
        );
        $this->assertSame('22', $tofu->creature?->fresh()->life);
        $this->assertCount(14, IncarnamBestiaryCatalog::load()->entries());
    }

    public function test_imports_all_bestiary_catalogs_and_is_idempotent(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seed(ConditionSeeder::class);
        $this->seed(CreatureTraitSeeder::class);
        $this->seed(MonsterRaceSeeder::class);
        $this->seedSpellTypes();

        $importer = app(BestiarySeederImporter::class);
        $first = $importer->import();

        $this->assertCount(28, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $tofu = Monster::query()->where('official_id', 'jdr:bestiary:tofu')->with('creature.spells')->first();
        $this->assertNotNull($tofu);
        $this->assertSame('auto', $tofu->state);
        $this->assertNull($tofu->dofusdb_id);
        $this->assertSame('Tofu', $tofu->creature?->name);
        $this->assertSame('4', $tofu->creature?->pa);
        $this->assertSame('Astrub', $tofu->creature?->location);
        $this->assertSame(1, $tofu->creature?->spells()->count());
        $this->assertSame('Béco-béco', $tofu->creature?->spells()->first()?->name);

        $bouftou = Monster::query()->where('official_id', 'jdr:bestiary:bouftou')->first();
        $this->assertSame(2, $bouftou?->creature?->spells()->count());

        $gelee = Monster::query()->where('official_id', 'jdr:bestiary:gelee-bleuet')->first();
        $this->assertSame('9', $gelee?->creature?->pa);
        $this->assertSame('Tainela', $gelee?->creature?->location);

        $chafer = Monster::query()->where('official_id', 'jdr:bestiary:chafer')->first();
        $this->assertSame(4, $chafer?->creature?->hostility);
        $this->assertSame('Cimetière d’Astrub', $chafer?->creature?->location);

        $this->assertNotNull(
            Monster::query()->where('official_id', 'jdr:bestiary:tofu-chimerique')->first()
        );

        $second = $importer->import();
        $this->assertSame([], $second['created']);
        $this->assertCount(28, $second['updated']);
        $this->assertSame(
            28,
            Monster::query()->where('official_id', 'like', 'jdr:bestiary:%')->count()
        );
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
