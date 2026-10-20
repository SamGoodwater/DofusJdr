<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Breed\ClassBreedCatalog;
use Tests\TestCase;

final class ClassBreedCatalogTest extends TestCase
{
    public function test_load_all_returns_nineteen_classes_in_dofus_order(): void
    {
        $catalogs = ClassBreedCatalog::loadAllInDirectory();
        $names = array_map(
            static fn (ClassBreedCatalog $catalog): string => (string) ($catalog->entry()['name'] ?? ''),
            $catalogs
        );

        $this->assertSame(ClassBreedCatalog::BASE_CLASS_NAMES, $names);
        $this->assertCount(19, ClassBreedCatalog::PREFERRED_FILES);
        $this->assertSame('iop', ClassBreedCatalog::load(ClassBreedCatalog::iopPath())->slug());
    }

    public function test_each_base_class_has_four_voices_from_rules(): void
    {
        $expected = [
            'Féca' => ['fire' => 'degats', 'earth' => 'tank', 'water' => 'entrave', 'air' => 'protection'],
            'Osamodas' => ['fire' => 'soin', 'earth' => 'entrave', 'water' => 'degats', 'air' => 'placement'],
            'Enutrof' => ['fire' => 'soin', 'earth' => 'degats', 'water' => 'amelioration', 'air' => 'entrave'],
            'Sram' => ['fire' => 'placement', 'earth' => 'degats', 'water' => 'amelioration', 'air' => 'entrave'],
            'Xélor' => ['fire' => 'amelioration', 'earth' => 'degats', 'water' => 'entrave', 'air' => 'placement'],
            'Ecaflip' => ['fire' => 'soin', 'earth' => 'amelioration', 'water' => 'degats', 'air' => 'placement'],
            'Eniripsa' => ['fire' => 'soin', 'earth' => 'entrave', 'water' => 'protection', 'air' => 'amelioration'],
            'Iop' => ['fire' => 'amelioration', 'earth' => 'degats', 'water' => 'protection', 'air' => 'placement'],
            'Crâ' => ['fire' => 'amelioration', 'earth' => 'degats', 'water' => 'entrave', 'air' => 'placement'],
            'Sadida' => ['fire' => 'entrave', 'earth' => 'tank', 'water' => 'soin', 'air' => 'degats'],
            'Sacrieur' => ['fire' => 'entrave', 'earth' => 'tank', 'water' => 'degats', 'air' => 'placement'],
            'Pandawa' => ['fire' => 'tank', 'earth' => 'degats', 'water' => 'entrave', 'air' => 'placement'],
            'Roublard' => ['fire' => 'degats', 'earth' => 'amelioration', 'water' => 'entrave', 'air' => 'placement'],
            'Zobal' => ['fire' => 'tank', 'earth' => 'entrave', 'water' => 'placement', 'air' => 'protection'],
            'Steamer' => ['fire' => 'soin', 'earth' => 'degats', 'water' => 'amelioration', 'air' => 'placement'],
            'Eliotrope' => ['fire' => 'soin', 'earth' => 'placement', 'water' => 'degats', 'air' => 'entrave'],
            'Huppermage' => ['fire' => 'amelioration', 'earth' => 'entrave', 'water' => 'placement', 'air' => 'degats'],
            'Ouginak' => ['fire' => 'entrave', 'earth' => 'tank', 'water' => 'amelioration', 'air' => 'degats'],
            'Forgelance' => ['fire' => 'degats', 'earth' => 'placement', 'water' => 'protection', 'air' => 'entrave'],
        ];

        foreach (ClassBreedCatalog::loadAllInDirectory() as $catalog) {
            $entry = $catalog->entry();
            $this->assertNotNull($entry);
            $name = $entry['name'];
            $this->assertArrayHasKey($name, $expected);
            $this->assertSame($expected[$name], $entry['element_orientations'], $name);
            $this->assertLessThanOrEqual(ClassBreedCatalog::DESCRIPTION_MAX, mb_strlen((string) $entry['description']));
        }
    }

    public function test_sacrieur_catalog_keeps_berserker_identity(): void
    {
        $entry = ClassBreedCatalog::load(ClassBreedCatalog::sacrieurPath())->entry();

        $this->assertNotNull($entry);
        $this->assertSame('Sacrieur', $entry['name']);
        $this->assertSame('11', $entry['dofusdb_id']);
        $this->assertSame('Berserker', $entry['description_fast']);
        $this->assertSame('draft', $entry['state']);
        $this->assertStringStartsWith('Les Sacrieurs sont des berserkers', (string) $entry['description']);
    }

    public function test_feca_and_iop_catalogs_match_dofus_ids(): void
    {
        $feca = ClassBreedCatalog::load(ClassBreedCatalog::fecaPath())->entry();
        $iop = ClassBreedCatalog::load(ClassBreedCatalog::iopPath())->entry();

        $this->assertSame('Féca', $feca['name'] ?? null);
        $this->assertSame('1', $feca['dofusdb_id'] ?? null);
        $this->assertSame('Protecteur', $feca['description_fast'] ?? null);
        $this->assertNotNull($feca['mechanics'] ?? null);
        $this->assertStringContainsString('Pile ou Face', (string) $feca['mechanics']);
        $this->assertSame('Iop', $iop['name'] ?? null);
        $this->assertSame('8', $iop['dofusdb_id'] ?? null);
        $this->assertNull($iop['mechanics'] ?? null);
        $this->assertSame('Guerrier téméraire', $iop['description_fast'] ?? null);
    }
}
