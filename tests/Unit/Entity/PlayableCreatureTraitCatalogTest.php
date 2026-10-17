<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

/**
 * Le catalogue de traits reste permanent et tient dans la limite de description.
 */
final class PlayableCreatureTraitCatalogTest extends TestCase
{
    public function test_traits_are_permanent_catalog_entries(): void
    {
        /** @var list<array{name: string, description: string, image: string}> $traits */
        $traits = require dirname(__DIR__, 3).'/database/seeders/data/entities/creature-traits/playable-catalog.php';

        $names = array_column($traits, 'name');
        $this->assertSame($names, array_values(array_unique($names)));
        $this->assertContains('Vif / Vive', $names);
        $this->assertContains('Rapide', $names);
        $this->assertNotContains('École prédestinée', $names);

        foreach ($traits as $trait) {
            $this->assertLessThanOrEqual(255, mb_strlen($trait['description']), $trait['name']);
            $this->assertStringStartsWith('/storage/images/icons/traits/', $trait['image']);
            $this->assertStringNotContainsString('dissip', mb_strtolower($trait['description']));
            $this->assertDoesNotMatchRegularExpression('/\b\d+\s+tours?\b/u', $trait['description']);
        }

        $byName = array_column($traits, 'description', 'name');
        $this->assertSame('Commence le combat en premier sans jet d’initiative.', $byName['Vif / Vive']);
        $this->assertStringContainsString('PM de base augmentent de 2', $byName['Rapide']);
    }
}
