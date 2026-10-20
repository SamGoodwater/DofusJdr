<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Spell\ClassLevel1SpellCatalog;
use Tests\TestCase;

final class ClassLevel1SpellCatalogTest extends TestCase
{
    public function test_iop_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load();
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Iop',
            ['Pression', 'Attaque Naturelle', 'Épée Divine', 'Épée de Givre', 'Bond', 'Concentration']
        );
        $this->assertSame('jdr:attaque-naturelle', $entries[1]['official_id']);
        $this->assertSame('water', $entries[3]['element']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('3', $entries[2]['pa']);
    }

    public function test_cra_catalog_has_eight_playable_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::craPath());
        $this->assertSame('Crâ', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(8, $entries);
        $this->assertSame(
            [
                'Flèche harcelante',
                'Flèche enflammée',
                'Flèche d\'immobilisation',
                'Flèche du vagabond',
                'Flèche évasive',
                'Tir de repli',
                'Tir éloigné',
                'Tir perçant',
            ],
            array_column($entries, 'name')
        );
        $this->assertSame('playable', $entries[0]['state']);
        $this->assertSame('jdr:cra:fleche-harcelante', $entries[0]['official_id']);
        $this->assertFalse($entries[0]['sight_line']);
        $this->assertSame('2', $entries[0]['cast_per_turn']);
        $this->assertSame('1', $entries[2]['cast_per_target']);
        $this->assertSame('chance', $entries[2]['attack_characteristic_key']);
        $this->assertSame('vitality', $entries[4]['attack_characteristic_key']);
        $this->assertSame('3', $entries[4]['pa']);
        $this->assertSame([0, 1, 2, 3], array_column(array_slice($entries, 0, 4), 'choice_order'));
        $this->assertSame(2, $entries[4]['slot_index']);
        $this->assertSame(3, $entries[6]['slot_index']);
    }

    public function test_eniripsa_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::eniripsaPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Eniripsa',
            ['Mot Vivifiant', 'Mot d’Amitié', 'Mot de Jouvence', 'Mot Galvanisant', 'Mot de Frayeur', 'Mot d’Envol']
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertSame('auto_success', $entries[0]['resolution_mode']);
        $this->assertSame('1d4+Sagesse', $entries[0]['sub_effects'][0]['params']['value']);
        $this->assertSame('1d4+Sagesse', $entries[2]['sub_effects'][0]['params']['value']);
    }

    public function test_sram_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sramPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Sram',
            ['Sournoiserie', 'Cruauté', 'Fourvoiement', 'Perfidie', 'Dérobade', 'Invisibilité']
        );
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertSame('earth', $entries[2]['element']);
        $this->assertSame('5', $entries[5]['pa']);
        $this->assertSame('3', $entries[0]['pa']);
    }

    public function test_xelor_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::xelorPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Xélor',
            ['Aiguille', 'Gelure', 'Sablier de Xélor', 'Poussière', 'Téléportation', 'Permutation']
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertTrue($entries[0]['po_editable']);
        $this->assertSame('intel', $entries[0]['attack_characteristic_key']);
        $this->assertSame('chance', $entries[1]['attack_characteristic_key']);
    }

    public function test_feca_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::fecaPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Féca',
            ['Attaque Naturelle', 'Rempart', 'Glyphe Agressif', 'Bouclier Élémentaire', 'Bouclier Féca', 'Armure Aqueuse']
        );
        $this->assertSame('glyph', $entries[2]['target_type']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('protéger', $entries[4]['sub_effects'][0]['slug']);
    }

    public function test_osamodas_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::osamodasPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Osamodas',
            ['Serres du Vautour', 'Griffes du Chtigre', 'Crocs du Mulou', 'Piqûre motivante', 'Tofu', 'Dragoune']
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertSame('agi', $entries[0]['attack_characteristic_key']);
        $this->assertSame(['Invocation'], $entries[4]['types']);
        $this->assertSame('invoquer', $entries[4]['sub_effects'][0]['slug']);
        $this->assertSame('jdr:summon:tofu', $entries[4]['sub_effects'][0]['params']['monster_official_id']);
    }

    public function test_enutrof_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::enutrofPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Enutrof',
            ['Lancer de Pièces', 'Roulage de Pelle', 'Pelle des Anciens', 'Corruption', 'Maladresse', 'Souterrain']
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertSame('chance', $entries[0]['attack_characteristic_key']);
        $this->assertSame('saving_throw', $entries[4]['resolution_mode']);
    }

    public function test_sadida_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sadidaPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Sadida',
            ['Ronce', 'Larme de Sadida', 'Ronce Insolente', 'Poison Paralysant', 'Poupée Sadida', 'Ronce Apaisante']
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertSame(['Invocation'], $entries[4]['types']);
        $this->assertSame('soigner', $entries[5]['sub_effects'][0]['slug']);
    }

    public function test_sacrieur_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sacrieurPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Sacrieur',
            ['Punition', 'Absorption', 'Folie', 'Épée Volante', 'Attirance', 'Sacrifice']
        );
        $this->assertSame('1d4', $entries[1]['sub_effects'][0]['params']['life_steal_formula']);
        $this->assertSame('pull', $entries[4]['sub_effects'][0]['params']['movement_kind']);
    }

    public function test_pandawa_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::pandawaPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Pandawa',
            ['Poing Enflammé', 'Vague à Lame', 'Souffle Alcoolisé', 'Vulnérabilité', 'Picole', 'Chamrak']
        );
        $this->assertSame('3', $entries[2]['pa']);
        $this->assertSame('booster', $entries[4]['sub_effects'][0]['slug']);
        $this->assertSame('push', $entries[5]['sub_effects'][0]['params']['movement_kind']);
    }

    public function test_ecaflip_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::ecaflipPath());
        $entries = $this->assertIopStyleLevel1Slots(
            $catalog,
            'Ecaflip',
            ['Topkaj', 'Yams', 'Griffe Invocatrice', 'Langue Râpeuse', 'Bond du Félin', 'Entrechat']
        );
        $this->assertSame('intel', $entries[0]['attack_characteristic_key']);
        $this->assertSame('chance', $entries[1]['attack_characteristic_key']);
        $this->assertSame('auto_success', $entries[4]['resolution_mode']);
    }

    public function test_added_class_catalogs_have_six_spells_in_three_slots(): void
    {
        $expected = [
            [ClassLevel1SpellCatalog::roublardPath(), 'Roublard', ['Pulsar', 'Espingole', 'Grenado', 'Filet', 'Détonateur', 'Botte']],
            [ClassLevel1SpellCatalog::zobalPath(), 'Zobal', ['Brincadeira', 'Parafuso', 'Caire', 'Distance', 'Cavalcade', 'Plastron']],
            [ClassLevel1SpellCatalog::steamerPath(), 'Steamer', ['Torpille', 'Longue-vue', 'Harpon', 'Salve Aquatique', 'Harponneuse', 'Gardienne']],
            [ClassLevel1SpellCatalog::eliotropePath(), 'Eliotrope', ['Affront', 'Audace', 'Snub', 'Rosée', 'Portail', 'Cicatrisation']],
            [ClassLevel1SpellCatalog::huppermagePath(), 'Huppermage', ['Lance-flamme', 'Stalagmite', 'Glaçon', 'Bourrasque', 'Éther', 'Runification']],
            [ClassLevel1SpellCatalog::ouginakPath(), 'Ouginak', ['Molosse', 'Charogne', 'Croc du Molosse', 'Cerbère', 'Traque', 'Amarok']],
            [ClassLevel1SpellCatalog::forgelancePath(), 'Forgelance', ['Estoc Brûlant', 'Lance du Lac', 'Brûlure de Lance', 'Éclat Liquide', 'Charge Héroïque', 'Phalange']],
        ];

        foreach ($expected as [$path, $breed, $names]) {
            $catalog = ClassLevel1SpellCatalog::load($path);
            $entries = $this->assertIopStyleLevel1Slots($catalog, $breed, $names);
            $this->assertSame('3', $entries[0]['pa'], $breed);
            $this->assertSame('3', $entries[2]['pa'], $breed);
        }
    }

    public function test_load_all_finds_all_class_kits(): void
    {
        $catalogs = ClassLevel1SpellCatalog::loadAllInDirectory();
        $this->assertCount(38, $catalogs);

        $breeds = array_values(array_unique(array_map(
            static fn (ClassLevel1SpellCatalog $c): string => $c->breedName(),
            $catalogs
        )));
        sort($breeds);
        $this->assertSame(
            ['Crâ', 'Ecaflip', 'Eliotrope', 'Eniripsa', 'Enutrof', 'Forgelance', 'Féca', 'Huppermage', 'Iop', 'Osamodas', 'Ouginak', 'Pandawa', 'Roublard', 'Sacrieur', 'Sadida', 'Sram', 'Steamer', 'Xélor', 'Zobal'],
            $breeds
        );
    }

    public function test_progression_catalogs_have_eighteen_spells_on_nine_levels(): void
    {
        $levels = [2, 4, 5, 6, 7, 8, 10, 11, 12];
        $files = glob(ClassLevel1SpellCatalog::directory().'/*-progression.json') ?: [];
        $this->assertCount(19, $files);

        foreach ($files as $file) {
            $catalog = ClassLevel1SpellCatalog::load($file);
            $entries = $catalog->entries();
            $got = [];
            foreach ($entries as $entry) {
                $got[] = [$entry['character_level'], $entry['choice_order']];
            }
            $elementalLevels = str_ends_with($file, 'cra-progression.json') ? [4, 7, 12] : [];
            $expected = [];
            foreach ($levels as $level) {
                $variants = in_array($level, $elementalLevels, true) ? 4 : 2;
                for ($order = 0; $order < $variants; $order++) {
                    $expected[] = [$level, $order];
                }
            }
            $this->assertCount(count($expected), $entries, $catalog->breedName());
            $this->assertSame($expected, $got, $catalog->breedName());
        }
    }

    /**
     * @param  list<string>  $names
     * @return list<array<string, mixed>>
     */
    private function assertIopStyleLevel1Slots(ClassLevel1SpellCatalog $catalog, string $breed, array $names): array
    {
        $this->assertSame($breed, $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries, $breed);

        $slots = [];
        foreach ($entries as $entry) {
            $slots[$entry['slot_index']][] = $entry['choice_order'];
        }
        ksort($slots);
        $this->assertSame([1, 2, 3], array_keys($slots), $breed);
        sort($slots[1]);
        sort($slots[2]);
        sort($slots[3]);
        $this->assertSame([0, 1, 2, 3], $slots[1], $breed);
        $this->assertSame([0], $slots[2], $breed);
        $this->assertSame([0], $slots[3], $breed);
        $this->assertSame($names, array_column($entries, 'name'), $breed);

        return $entries;
    }

    public function test_iop_spells_carry_three_intensification_tiers(): void
    {
        $catalog = ClassLevel1SpellCatalog::load();
        $pression = $catalog->entries()[0];
        $this->assertCount(3, $pression['intensification']);
        $this->assertSame('I', $pression['intensification'][0]['tier']);
        $this->assertSame(13, $pression['intensification'][0]['required_creature_level']);
        $this->assertStringContainsString('2d6', $pression['intensification'][0]['effect']);

        $progression = ClassLevel1SpellCatalog::load(
            ClassLevel1SpellCatalog::directory().'/iop-progression.json'
        );
        $tempete = null;
        foreach ($progression->entries() as $entry) {
            if ($entry['name'] === 'Tempête de Puissance') {
                $tempete = $entry;
                break;
            }
        }
        $this->assertNotNull($tempete);
        $this->assertSame(12, $tempete['character_level']);
        $this->assertSame('5d6 + Agilité (Air) en petite zone. Magique, 5 PA, 1×/tour.', $tempete['intensification'][0]['effect']);
        $this->assertSame('—', $tempete['intensification'][1]['effect']);
        $this->assertSame('—', $tempete['intensification'][2]['effect']);
    }
}
