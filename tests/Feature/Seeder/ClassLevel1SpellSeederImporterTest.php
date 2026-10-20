<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Breed;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Services\Seeder\Monster\ClassSummonSeederImporter;
use App\Services\Seeder\Spell\ClassLevel1SpellCatalog;
use App\Services\Seeder\Spell\ClassLevel1SpellSeederImporter;
use App\Support\ElementBitmask;
use Database\Seeders\SubEffectSeeder;
use Tests\TestCase;

final class ClassLevel1SpellSeederImporterTest extends TestCase
{
    public function test_imports_iop_level_1_kit_and_is_idempotent(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $iop = Breed::factory()->create([
            'name' => 'Iop',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Colère de Iop',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $iop->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load();
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $pression = Spell::query()->where('dofusdb_id', '13106')->first();
        $this->assertNotNull($pression);
        $this->assertSame('Pression', $pression->name);
        $this->assertSame(Spell::STATE_AUTO, $pression->state);
        $this->assertFalse($pression->auto_update);
        $this->assertSame('3', $pression->pa);
        $this->assertSame('2', $pression->cast_per_turn);
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $pression->resolution_mode);
        $this->assertSame('strong', $pression->attack_characteristic_key);
        $this->assertFalse($pression->is_magic);
        $this->assertSame(ElementBitmask::fromSlug('earth'), $pression->element);
        $this->assertSame(Spell::CATEGORY_CLASS, $pression->category);

        $attaque = Spell::query()->where('official_id', 'jdr:attaque-naturelle')->first();
        $this->assertNotNull($attaque);
        $this->assertSame('intel', $attaque->attack_characteristic_key);
        $this->assertTrue($attaque->po_editable);
        $this->assertSame('4', $attaque->po_max);

        $givre = Spell::query()->where('official_id', 'jdr:epee-de-givre')->first();
        $this->assertNotNull($givre);
        $this->assertSame('Épée de Givre', $givre->name);
        $this->assertSame('3', $givre->pa);
        $this->assertSame('chance', $givre->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('water'), $givre->element);

        $bond = Spell::query()->where('dofusdb_id', '15660')->first();
        $this->assertNotNull($bond);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $bond->resolution_mode);
        $this->assertFalse($bond->sight_line);
        $this->assertSame('3', $bond->pa);

        $concentration = Spell::query()->where('dofusdb_id', '30767')->first();
        $this->assertNotNull($concentration);
        $this->assertStringContainsString('+2 aux jets d’attaque', (string) $concentration->effect);
        $this->assertStringNotContainsString('1d4', (string) $concentration->effect);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());
        $this->assertGreaterThan(0, $pression->effects()->count());
        $this->assertTrue($pression->spellTypes()->where('name', 'Offensif')->exists());

        $iop->refresh();
        $pressionPivot = $iop->spells()->where('spells.id', $pression->id)->first()?->pivot;
        $this->assertNotNull($pressionPivot);
        $this->assertSame(1, (int) $pressionPivot->character_level);
        $this->assertSame(1, (int) $pressionPivot->slot_index);
        $this->assertSame(0, (int) $pressionPivot->choice_order);

        $attaquePivot = $iop->spells()->where('spells.id', $attaque->id)->first()?->pivot;
        $this->assertSame(1, (int) $attaquePivot?->slot_index);
        $this->assertSame(1, (int) $attaquePivot?->choice_order);

        $extraPivot = $iop->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertNotNull($extraPivot);
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot->character_level
        );
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_SLOT_INDEX,
            (int) $extraPivot->slot_index
        );

        $second = $importer->import($catalog);
        $this->assertSame([], $second['created']);
        $this->assertCount(6, $second['updated']);
        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());
        $this->assertSame('3', $pression->fresh()->pa);
    }

    public function test_imports_cra_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $cra = Breed::factory()->create([
            'name' => 'Crâ',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Ancien sort hors kit',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $cra->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::craPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(8, $first['created']);
        $this->assertSame([], $first['skipped']);

        $harcelante = Spell::query()->where('official_id', 'jdr:cra:fleche-harcelante')->first();
        $this->assertNotNull($harcelante);
        $this->assertSame('Flèche harcelante', $harcelante->name);
        $this->assertSame(Spell::STATE_PLAYABLE, $harcelante->state);
        $this->assertFalse($harcelante->auto_update);
        $this->assertSame('3', $harcelante->pa);
        $this->assertSame('1', $harcelante->po_min);
        $this->assertSame('7', $harcelante->po_max);
        $this->assertFalse($harcelante->sight_line);
        $this->assertSame('2', $harcelante->cast_per_turn);
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $harcelante->resolution_mode);
        $this->assertSame('agi', $harcelante->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('air'), $harcelante->element);
        $this->assertSame(Spell::CATEGORY_CLASS, $harcelante->category);

        $immobilisation = Spell::query()->where('dofusdb_id', '32436')->first();
        $this->assertSame('Flèche d\'immobilisation', $immobilisation?->name);
        $this->assertSame('chance', $immobilisation?->attack_characteristic_key);
        $this->assertSame('1', $immobilisation?->cast_per_target);
        $this->assertSame(ElementBitmask::fromSlug('water'), $immobilisation?->element);

        $evasive = Spell::query()->where('official_id', 'jdr:cra:fleche-evasive')->first();
        $this->assertSame('Flèche évasive', $evasive?->name);
        $this->assertSame('vitality', $evasive?->attack_characteristic_key);
        $this->assertSame('3', $evasive?->pa);
        $this->assertSame('2', $evasive?->cast_per_target);
        $this->assertNull($evasive?->element);

        $repli = Spell::query()->where('dofusdb_id', '32470')->first();
        $this->assertSame('Tir de repli', $repli?->name);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $repli?->resolution_mode);
        $this->assertSame('2', $repli?->cast_per_turn);
        $this->assertFalse($repli?->is_magic);

        $this->assertSame(8, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());

        $cra->refresh();
        $harcelantePivot = $cra->spells()->where('spells.id', $harcelante->id)->first()?->pivot;
        $this->assertSame(1, (int) $harcelantePivot?->character_level);
        $this->assertSame(1, (int) $harcelantePivot?->slot_index);
        $this->assertSame(0, (int) $harcelantePivot?->choice_order);

        $repliPivot = $cra->spells()->where('spells.id', $repli->id)->first()?->pivot;
        $this->assertSame(2, (int) $repliPivot?->slot_index);
        $this->assertSame(1, (int) $repliPivot?->choice_order);

        $extraPivot = $cra->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_SLOT_INDEX,
            (int) $extraPivot?->slot_index
        );
    }

    public function test_imports_eniripsa_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $eni = Breed::factory()->create([
            'name' => 'Eniripsa',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Mot de Reconstitution',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $eni->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::eniripsaPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $vivifiant = Spell::query()->where('dofusdb_id', '28572')->first();
        $this->assertNotNull($vivifiant);
        $this->assertSame('Mot Vivifiant', $vivifiant->name);
        $this->assertSame(Spell::STATE_AUTO, $vivifiant->state);
        $this->assertFalse($vivifiant->auto_update);
        $this->assertSame('3', $vivifiant->pa);
        $this->assertSame('2', $vivifiant->cast_per_turn);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $vivifiant->resolution_mode);
        $this->assertTrue($vivifiant->auto_success_if_willing_target);
        $this->assertSame(ElementBitmask::fromSlug('water'), $vivifiant->element);
        $this->assertTrue($vivifiant->spellTypes()->where('name', 'Soin')->exists());

        $jouvence = Spell::query()->where('dofusdb_id', '28573')->first();
        $this->assertSame('Mot de Jouvence', $jouvence?->name);
        $this->assertSame('3', $jouvence?->pa);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $jouvence?->resolution_mode);

        $frayeur = Spell::query()->where('dofusdb_id', '13175')->first();
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $frayeur?->resolution_mode);
        $this->assertSame('agi', $frayeur?->attack_characteristic_key);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());

        $eni->refresh();
        $vivifiantPivot = $eni->spells()->where('spells.id', $vivifiant->id)->first()?->pivot;
        $this->assertSame(1, (int) $vivifiantPivot?->character_level);
        $this->assertSame(1, (int) $vivifiantPivot?->slot_index);
        $this->assertSame(0, (int) $vivifiantPivot?->choice_order);

        $extraPivot = $eni->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_SLOT_INDEX,
            (int) $extraPivot?->slot_index
        );
    }

    public function test_imports_sram_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $sram = Breed::factory()->create([
            'name' => 'Sram',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Marque Mortuaire',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $sram->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sramPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $sournoiserie = Spell::query()->where('dofusdb_id', '32536')->first();
        $this->assertNotNull($sournoiserie);
        $this->assertSame('Sournoiserie', $sournoiserie->name);
        $this->assertSame('3', $sournoiserie->pa);
        $this->assertSame('strong', $sournoiserie->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('earth'), $sournoiserie->element);

        $fourvoiement = Spell::query()->where('dofusdb_id', '12909')->first();
        $this->assertSame('Fourvoiement', $fourvoiement?->name);
        $this->assertSame('3', $fourvoiement?->pa);
        $this->assertSame(ElementBitmask::fromSlug('earth'), $fourvoiement?->element);

        $invis = Spell::query()->where('dofusdb_id', '32367')->first();
        $this->assertSame('5', $invis?->pa);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $invis?->resolution_mode);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());

        $sram->refresh();
        $invisPivot = $sram->spells()->where('spells.id', $invis->id)->first()?->pivot;
        $this->assertSame(3, (int) $invisPivot?->slot_index);
        $this->assertSame(0, (int) $invisPivot?->choice_order);

        $extraPivot = $sram->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    public function test_imports_xelor_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $xelor = Breed::factory()->create([
            'name' => 'Xélor',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Momification',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $xelor->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::xelorPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $aiguille = Spell::query()->where('dofusdb_id', '30842')->first();
        $this->assertNotNull($aiguille);
        $this->assertSame('Aiguille', $aiguille->name);
        $this->assertSame('3', $aiguille->pa);
        $this->assertSame('intel', $aiguille->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('fire'), $aiguille->element);
        $this->assertTrue($aiguille->po_editable);

        $sablier = Spell::query()->where('dofusdb_id', '30843')->first();
        $this->assertSame('Sablier de Xélor', $sablier?->name);
        $this->assertSame('3', $sablier?->pa);
        $this->assertSame(ElementBitmask::fromSlug('water'), $sablier?->element);

        $poussiere = Spell::query()->where('dofusdb_id', '13317')->first();
        $this->assertSame('Poussière', $poussiere?->name);
        $this->assertSame('3', $poussiere?->pa);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());

        $xelor->refresh();
        $aiguillePivot = $xelor->spells()->where('spells.id', $aiguille->id)->first()?->pivot;
        $this->assertSame(1, (int) $aiguillePivot?->slot_index);
        $this->assertSame(0, (int) $aiguillePivot?->choice_order);

        $extraPivot = $xelor->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    public function test_imports_feca_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $feca = Breed::factory()->create([
            'name' => 'Féca',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Immunité',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $feca->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::fecaPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $attaque = Spell::query()->where('dofusdb_id', '32363')->first();
        $this->assertNotNull($attaque);
        $this->assertSame('Attaque Naturelle', $attaque->name);
        $this->assertSame('3', $attaque->pa);
        $this->assertSame('intel', $attaque->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('fire'), $attaque->element);

        $glyphe = Spell::query()->where('official_id', 'jdr:feca-glyphe-agressif')->first();
        $this->assertSame('Glyphe Agressif', $glyphe?->name);
        $this->assertSame('5', $glyphe?->pa);
        $this->assertSame('glyph', $glyphe?->target_type);
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $glyphe?->resolution_mode);

        $bouclier = Spell::query()->where('dofusdb_id', '32365')->first();
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $bouclier?->resolution_mode);
        $this->assertTrue($bouclier?->spellTypes()->where('name', 'Défensif')->exists());

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());

        $feca->refresh();
        $attaquePivot = $feca->spells()->where('spells.id', $attaque->id)->first()?->pivot;
        $this->assertSame(1, (int) $attaquePivot?->slot_index);
        $this->assertSame(0, (int) $attaquePivot?->choice_order);

        $extraPivot = $feca->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    public function test_imports_osamodas_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();
        $this->seedClassSummons();

        $osa = Breed::factory()->create([
            'name' => 'Osamodas',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Fouet',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $osa->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::osamodasPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $serres = Spell::query()->where('dofusdb_id', '31133')->first();
        $this->assertNotNull($serres);
        $this->assertSame('Serres du Vautour', $serres->name);
        $this->assertSame('3', $serres->pa);
        $this->assertSame('agi', $serres->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('air'), $serres->element);

        $tofu = Spell::query()->where('dofusdb_id', '31971')->first();
        $this->assertSame('3', $tofu?->pa);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $tofu?->resolution_mode);
        $this->assertTrue($tofu?->spellTypes()->where('name', 'Invocation')->exists());
        $summon = Monster::query()->where('official_id', 'jdr:summon:tofu')->first();
        $this->assertNotNull($summon);
        $this->assertTrue($tofu->monsters()->where('monsters.id', $summon->id)->exists());
        $tofu->load(['effects.degrees.effectSubEffects.subEffect']);
        $sub = $tofu->effects->first()?->degrees->first()?->effectSubEffects->first();
        $this->assertSame('invoquer', $sub?->subEffect?->slug);
        $this->assertSame($summon->id, (int) ($sub?->params['monster_id'] ?? 0));

        $this->assertSame(6, Spell::query()->where('category', Spell::CATEGORY_CLASS)->where('state', Spell::STATE_AUTO)->count());

        $osa->refresh();
        $tofuPivot = $osa->spells()->where('spells.id', $tofu->id)->first()?->pivot;
        $this->assertSame(2, (int) $tofuPivot?->slot_index);
        $this->assertSame(0, (int) $tofuPivot?->choice_order);

        $extraPivot = $osa->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    public function test_imports_enutrof_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $enu = Breed::factory()->create([
            'name' => 'Enutrof',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Coffre Animé',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $enu->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::enutrofPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $pieces = Spell::query()->where('dofusdb_id', '13338')->first();
        $this->assertNotNull($pieces);
        $this->assertSame('Lancer de Pièces', $pieces->name);
        $this->assertSame('3', $pieces->pa);
        $this->assertSame('chance', $pieces->attack_characteristic_key);
        $this->assertSame(ElementBitmask::fromSlug('water'), $pieces->element);

        $pelle = Spell::query()->where('dofusdb_id', '13345')->first();
        $this->assertSame('Pelle des Anciens', $pelle?->name);
        $this->assertSame('3', $pelle?->pa);
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $pelle?->resolution_mode);

        $maladresse = Spell::query()->where('dofusdb_id', '13337')->first();
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $maladresse?->resolution_mode);
        $this->assertSame('sagesse', $maladresse?->save_characteristic_key);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_AUTO)->count());

        $enu->refresh();
        $piecesPivot = $enu->spells()->where('spells.id', $pieces->id)->first()?->pivot;
        $this->assertSame(1, (int) $piecesPivot?->slot_index);
        $this->assertSame(0, (int) $piecesPivot?->choice_order);

        $extraPivot = $enu->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    public function test_imports_sadida_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();
        $this->seedClassSummons();

        $sadida = Breed::factory()->create([
            'name' => 'Sadida',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Ronce Insolente',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $sadida->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sadidaPath());
        $first = app(ClassLevel1SpellSeederImporter::class)->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $ronce = Spell::query()->where('dofusdb_id', '13552')->first();
        $this->assertSame('Ronce', $ronce?->name);
        $this->assertSame('3', $ronce?->pa);
        $this->assertSame('strong', $ronce?->attack_characteristic_key);

        $poupee = Spell::query()->where('dofusdb_id', '29617')->first();
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $poupee?->resolution_mode);
        $this->assertTrue($poupee?->spellTypes()->where('name', 'Invocation')->exists());

        $sadida->refresh();
        $this->assertSame(1, (int) $sadida->spells()->where('spells.id', $ronce->id)->first()?->pivot->slot_index);
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $sadida->spells()->where('spells.id', $extra->id)->first()?->pivot->character_level
        );
    }

    public function test_imports_sacrieur_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $sacri = Breed::factory()->create([
            'name' => 'Sacrieur',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Transfert',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $sacri->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sacrieurPath());
        $first = app(ClassLevel1SpellSeederImporter::class)->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $punition = Spell::query()->where('dofusdb_id', '32373')->first();
        $this->assertSame('Punition', $punition?->name);
        $this->assertSame('3', $punition?->pa);

        $attirance = Spell::query()->where('dofusdb_id', '30544')->first();
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $attirance?->resolution_mode);
        $this->assertSame('agi', $attirance?->attack_characteristic_key);

        $sacri->refresh();
        $this->assertSame(2, (int) $sacri->spells()->where('spells.id', $attirance->id)->first()?->pivot->slot_index);
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $sacri->spells()->where('spells.id', $extra->id)->first()?->pivot->character_level
        );
    }

    public function test_imports_pandawa_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $panda = Breed::factory()->create([
            'name' => 'Pandawa',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Souillure',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $panda->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::pandawaPath());
        $first = app(ClassLevel1SpellSeederImporter::class)->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $poing = Spell::query()->where('dofusdb_id', '2008')->first();
        $this->assertSame('Poing Enflammé', $poing?->name);
        $this->assertSame('3', $poing?->pa);
        $this->assertSame('intel', $poing?->attack_characteristic_key);

        $picole = Spell::query()->where('dofusdb_id', '12780')->first();
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $picole?->resolution_mode);

        $panda->refresh();
        $this->assertSame(1, (int) $panda->spells()->where('spells.id', $poing->id)->first()?->pivot->slot_index);
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $panda->spells()->where('spells.id', $extra->id)->first()?->pivot->character_level
        );
    }

    public function test_imports_ecaflip_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $eca = Breed::factory()->create([
            'name' => 'Ecaflip',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Roulette',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $eca->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::ecaflipPath());
        $first = app(ClassLevel1SpellSeederImporter::class)->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $topkaj = Spell::query()->where('dofusdb_id', '12846')->first();
        $this->assertSame('Topkaj', $topkaj?->name);
        $this->assertSame('3', $topkaj?->pa);
        $this->assertSame('intel', $topkaj?->attack_characteristic_key);

        $bond = Spell::query()->where('dofusdb_id', '12844')->first();
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $bond?->resolution_mode);
        $this->assertFalse($bond?->sight_line);

        $eca->refresh();
        $this->assertSame(1, (int) $eca->spells()->where('spells.id', $topkaj->id)->first()?->pivot->slot_index);
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $eca->spells()->where('spells.id', $extra->id)->first()?->pivot->character_level
        );
    }

    public function test_imports_added_class_level_1_kits(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();
        $this->seedClassSummons();

        $cases = [
            ['Roublard', ClassLevel1SpellCatalog::roublardPath(), '13442', 'Pulsar'],
            ['Zobal', ClassLevel1SpellCatalog::zobalPath(), '13425', 'Brincadeira'],
            ['Steamer', ClassLevel1SpellCatalog::steamerPath(), '13865', 'Torpille'],
            ['Eliotrope', ClassLevel1SpellCatalog::eliotropePath(), '14574', 'Portail'],
            ['Huppermage', ClassLevel1SpellCatalog::huppermagePath(), '13666', 'Lance-flamme'],
            ['Ouginak', ClassLevel1SpellCatalog::ouginakPath(), '13756', 'Molosse'],
            ['Forgelance', ClassLevel1SpellCatalog::forgelancePath(), '23754', 'Estoc Brûlant'],
        ];

        foreach ($cases as [$name, $path, $dofusId, $spellName]) {
            $breed = Breed::factory()->create([
                'name' => $name,
                'state' => Breed::STATE_DRAFT,
                'read_level' => 0,
                'write_level' => 3,
                'created_by' => null,
            ]);
            $catalog = ClassLevel1SpellCatalog::load($path);
            $result = app(ClassLevel1SpellSeederImporter::class)->import($catalog);
            $this->assertCount(6, $result['created'], $name);
            $this->assertSame([], $result['skipped'], $name);
            $spell = Spell::query()->where('dofusdb_id', $dofusId)->first();
            $this->assertSame($spellName, $spell?->name, $name);
            $this->assertSame(Spell::STATE_AUTO, $spell?->state, $name);
            $this->assertSame(6, $breed->fresh()->spells()->count(), $name);
        }

        $runification = Spell::query()->where('dofusdb_id', '13670')->first();
        $this->assertNotNull($runification);
        $this->assertSame('Runification', $runification->name);
        $this->assertStringContainsString('Contraste reste +1', (string) $runification->effect);
        $this->assertStringNotContainsString('1d4 aux dégâts', (string) $runification->effect);
    }

    public function test_imports_iop_progression_and_places_colere_at_level_10(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $iop = Breed::factory()->create([
            'name' => 'Iop',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Colère de Iop',
            'dofusdb_id' => '15661',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $iop->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(
            ClassLevel1SpellCatalog::directory().'/iop-progression.json'
        );
        $first = app(ClassLevel1SpellSeederImporter::class)->import($catalog);

        $this->assertCount(17, $first['created']);
        $this->assertCount(1, $first['updated']);
        $this->assertSame([], $first['skipped']);

        $colere = Spell::query()->where('dofusdb_id', '15661')->first();
        $this->assertNotNull($colere);
        $this->assertSame('Colère de Iop', $colere->name);
        $this->assertSame(Spell::STATE_AUTO, $colere->state);
        $this->assertSame('5', $colere->pa);
        $this->assertSame('10', $colere->level);
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $colere->resolution_mode);

        $iop->refresh();
        $pivot = $iop->spells()->where('spells.id', $colere->id)->first()?->pivot;
        $this->assertSame(10, (int) $pivot?->character_level);
        $this->assertSame(1, (int) $pivot?->slot_index);
        $this->assertSame(0, (int) $pivot?->choice_order);
        $this->assertSame(18, $iop->spells()->count());
    }

    public function test_full_import_keeps_level_1_and_progression_slots_together(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        Breed::factory()->create([
            'name' => 'Iop',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);

        $result = app(ClassLevel1SpellSeederImporter::class)->import();
        $this->assertNotEmpty($result['skipped']);

        $iop = Breed::query()->where('name', 'Iop')->first();
        $this->assertNotNull($iop);
        $slotted = $iop->spells()
            ->wherePivot('character_level', '>', 0)
            ->get();
        $this->assertCount(24, $slotted);
        $levels = $slotted->pluck('pivot.character_level')->unique()->sort()->values()->all();
        $this->assertSame([1, 2, 4, 5, 6, 7, 8, 10, 11, 12], array_map('intval', $levels));
    }

    public function test_imports_iop_intensification_degrees_on_pression(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();
        Breed::factory()->create([
            'name' => 'Iop',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);

        app(ClassLevel1SpellSeederImporter::class)->import(ClassLevel1SpellCatalog::load());

        $pression = Spell::query()->where('dofusdb_id', '13106')->first();
        $this->assertNotNull($pression);
        $this->assertStringContainsString('1d6 + Force', (string) $pression->effect);

        $pression->load(['effects.degrees.effectSubEffects']);
        $degrees = $pression->effects->first()?->degrees->sortBy('degree')->values();
        $this->assertNotNull($degrees);
        $this->assertCount(4, $degrees);
        $this->assertSame(1, (int) $degrees[0]->required_creature_level);
        $this->assertSame(13, (int) $degrees[1]->required_creature_level);
        $this->assertSame(16, (int) $degrees[2]->required_creature_level);
        $this->assertSame(20, (int) $degrees[3]->required_creature_level);
        $this->assertSame('2d6+[strong]', $degrees[1]->effectSubEffects->first()?->params['value_formula'] ?? null);
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

    private function seedClassSummons(): void
    {
        app(ClassSummonSeederImporter::class)->import();
    }
}
