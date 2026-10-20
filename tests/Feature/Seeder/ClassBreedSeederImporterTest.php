<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Breed;
use App\Models\Entity\BreedElementOrientation;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Services\Seeder\Breed\ClassBreedCatalog;
use App\Services\Seeder\Breed\ClassBreedSeederImporter;
use App\Services\Seeder\Spell\ClassLevel1SpellCatalog;
use App\Services\Seeder\Spell\ClassLevel1SpellSeederImporter;
use Database\Seeders\Entity\ClassBreedSeeder;
use Database\Seeders\SubEffectSeeder;
use Tests\TestCase;

final class ClassBreedSeederImporterTest extends TestCase
{
    public function test_imports_nineteen_classes_and_is_idempotent(): void
    {
        $importer = app(ClassBreedSeederImporter::class);
        $first = $importer->import();

        $this->assertSame(ClassBreedCatalog::BASE_CLASS_NAMES, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $sacrieur = Breed::query()->where('dofusdb_id', '11')->first();
        $this->assertNotNull($sacrieur);
        $this->assertSame('Sacrieur', $sacrieur->name);
        $this->assertSame(Breed::STATE_DRAFT, $sacrieur->state);
        $this->assertFalse($sacrieur->auto_update);
        $this->assertSame(0, $sacrieur->read_level);
        $this->assertSame('Berserker', $sacrieur->description_fast);
        $this->assertLessThanOrEqual(255, mb_strlen((string) $sacrieur->description));

        $panda = Breed::query()->where('dofusdb_id', '12')->first();
        $this->assertNotNull($panda);
        $this->assertSame('Pandawa', $panda->name);
        $this->assertSame('Bagarreur assoiffé', $panda->description_fast);

        $feca = Breed::query()->where('dofusdb_id', '1')->first();
        $this->assertNotNull($feca);
        $this->assertSame('Féca', $feca->name);
        $this->assertStringContainsString('Pile ou Face', (string) $feca->mechanics);

        $iop = Breed::query()->where('dofusdb_id', '8')->first();
        $this->assertNotNull($iop);
        $this->assertSame('Iop', $iop->name);
        $this->assertNull($iop->mechanics);
        $this->assertFalse($iop->auto_update);
        $this->assertSame('/storage/images/breeds/iop/full_m.png', $iop->image_full_male);
        $this->assertSame('/storage/images/breeds/iop/logo_m.png', $iop->logo_male);
        $this->assertSame('/storage/images/breeds/iop/symbol-bw.png', $iop->icon);

        $this->assertSame(
            ['air' => 'placement', 'earth' => 'tank', 'fire' => 'entrave', 'water' => 'degats'],
            $this->orientationsOf($sacrieur)
        );
        $this->assertSame(
            ['air' => 'placement', 'earth' => 'degats', 'fire' => 'tank', 'water' => 'entrave'],
            $this->orientationsOf($panda)
        );
        $this->assertSame(
            ['air' => 'placement', 'earth' => 'degats', 'fire' => 'amelioration', 'water' => 'protection'],
            $this->orientationsOf($iop)
        );

        $second = $importer->import();
        $this->assertSame([], $second['created']);
        $this->assertSame(ClassBreedCatalog::BASE_CLASS_NAMES, $second['updated']);
        $this->assertSame(19, Breed::query()->whereIn('name', ClassBreedCatalog::BASE_CLASS_NAMES)->count());
    }

    public function test_class_breed_seeder_then_spell_kit_attaches_slots(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();
        $this->seed(ClassBreedSeeder::class);

        $sacrieur = Breed::query()->where('name', 'Sacrieur')->first();
        $this->assertNotNull($sacrieur);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sacrieurPath());
        $result = app(ClassLevel1SpellSeederImporter::class)->import($catalog);

        $this->assertCount(6, $result['created']);
        $this->assertSame([], $result['skipped']);

        $punition = Spell::query()->where('dofusdb_id', '32373')->first();
        $this->assertNotNull($punition);
        $this->assertSame(1, (int) $sacrieur->spells()->where('spells.id', $punition->id)->first()?->pivot->slot_index);
        $this->assertSame(1, (int) $sacrieur->spells()->where('spells.id', $punition->id)->first()?->pivot->character_level);
        $this->assertSame(6, $sacrieur->spells()->count());
    }

    public function test_upserts_existing_breed_found_by_name_without_changing_state(): void
    {
        $existing = Breed::factory()->create([
            'name' => 'Sacrieur',
            'dofusdb_id' => null,
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => 2,
            'write_level' => 3,
            'auto_update' => true,
            'created_by' => null,
        ]);

        $catalog = ClassBreedCatalog::load(ClassBreedCatalog::sacrieurPath());
        $result = app(ClassBreedSeederImporter::class)->import($catalog);

        $this->assertSame(['Sacrieur'], $result['updated']);
        $existing->refresh();
        $this->assertSame('11', $existing->dofusdb_id);
        $this->assertSame(Breed::STATE_PLAYABLE, $existing->state);
        $this->assertSame(2, $existing->read_level);
        $this->assertFalse($existing->auto_update);
        $this->assertSame('tank', $existing->elementOrientations()->where('element', BreedElementOrientation::ELEMENT_EARTH)->value('orientation_key'));
    }

    public function test_updates_scraped_breeds_without_changing_state(): void
    {
        $feca = Breed::factory()->create([
            'name' => 'Féca',
            'dofusdb_id' => '1',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'auto_update' => true,
            'created_by' => null,
        ]);
        $cra = Breed::factory()->create([
            'name' => 'Crâ',
            'dofusdb_id' => '9',
            'state' => Breed::STATE_RAW,
            'read_level' => 0,
            'write_level' => 3,
            'auto_update' => true,
            'created_by' => null,
        ]);

        $result = app(ClassBreedSeederImporter::class)->import();

        $this->assertContains('Féca', $result['updated']);
        $this->assertContains('Crâ', $result['updated']);
        $this->assertContains('Iop', $result['created']);

        $feca->refresh();
        $cra->refresh();
        $this->assertSame(Breed::STATE_DRAFT, $feca->state);
        $this->assertFalse($feca->auto_update);
        $this->assertSame('Protecteur', $feca->description_fast);
        $this->assertSame(
            ['air' => 'protection', 'earth' => 'tank', 'fire' => 'degats', 'water' => 'entrave'],
            $this->orientationsOf($feca)
        );

        $this->assertSame(Breed::STATE_RAW, $cra->state);
        $this->assertFalse($cra->auto_update);
        $this->assertSame(
            ['air' => 'placement', 'earth' => 'degats', 'fire' => 'amelioration', 'water' => 'entrave'],
            $this->orientationsOf($cra)
        );
    }

    /**
     * @return array<string, string>
     */
    private function orientationsOf(Breed $breed): array
    {
        $pairs = $breed->elementOrientations()
            ->orderBy('element')
            ->pluck('orientation_key', 'element')
            ->all();

        ksort($pairs);

        return $pairs;
    }

    private function seedSpellTypes(): void
    {
        foreach (['Offensif', 'Buff', 'Debuff', 'Téléportation', 'Soin', 'Défensif', 'Invocation'] as $name) {
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
