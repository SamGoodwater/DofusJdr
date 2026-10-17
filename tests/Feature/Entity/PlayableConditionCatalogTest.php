<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Condition;
use App\Services\Condition\PlayableConditionCatalog;
use Tests\TestCase;

/**
 * Le catalogue ajoute les états D&D et Dofus sans réécrire une fiche déjà jouable.
 */
class PlayableConditionCatalogTest extends TestCase
{
    public function test_sync_keeps_playable_text_promotes_raw_and_deletes_the_rest(): void
    {
        $empoisonne = Condition::factory()->create([
            'name' => 'Empoisonné',
            'description' => 'Texte déjà rédigé.',
            'state' => Condition::STATE_PLAYABLE,
        ]);
        Condition::factory()->create([
            'name' => 'Enraciné',
            'description' => 'brut',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 6,
        ]);
        Condition::factory()->create([
            'name' => 'Invulnérable en Mêlée',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 376,
        ]);
        Condition::factory()->create([
            'name' => 'Jeton inutile',
            'state' => Condition::STATE_RAW,
            'dofusdb_id' => 99999,
        ]);

        $stats = app(PlayableConditionCatalog::class)->sync(deleteRaw: true);

        $empoisonne->refresh();
        $this->assertSame('Texte déjà rédigé.', $empoisonne->description);
        $this->assertSame(Condition::STATE_PLAYABLE, $empoisonne->state);

        $enracine = Condition::query()->where('name', 'Enraciné')->first();
        $this->assertNotNull($enracine);
        $this->assertSame(Condition::STATE_PLAYABLE, $enracine->state);
        $this->assertStringContainsString('PM restent utilisables', (string) $enracine->description);
        $this->assertStringContainsString('stateRooted.png', (string) $enracine->image);
        $this->assertNull($enracine->dofusdb_id);
        $this->assertTrue($enracine->cant_be_moved);
        $this->assertTrue($enracine->cant_be_pushed);

        $cac = Condition::query()->where('name', 'Invulnérable au càc')->first();
        $this->assertNotNull($cac);
        $this->assertTrue($cac->invulnerable_melee);
        $this->assertSame(0, Condition::query()->where('name', 'Invulnérable en Mêlée')->count());

        $saoul = Condition::query()->where('name', 'Saoul')->first();
        $this->assertNotNull($saoul);
        $this->assertStringContainsString('jets d’attaque à distance', (string) $saoul->description);

        $this->assertNotNull(Condition::query()->where('name', 'À terre')->first());
        $this->assertNotNull(Condition::query()->where('name', 'Épuisement')->first());

        $intaclable = Condition::query()->where('name', 'Intaclable')->first();
        $this->assertNotNull($intaclable);
        $this->assertTrue($intaclable->cant_be_tackled);
        $this->assertFalse($intaclable->cant_tackle);
        $this->assertStringContainsString('jets de fuite', (string) $intaclable->description);

        $intacleur = Condition::query()->where('name', 'Intacleur')->first();
        $this->assertNotNull($intacleur);
        $this->assertTrue($intacleur->cant_tackle);
        $this->assertFalse($intacleur->cant_be_tackled);

        $resistant = Condition::query()->where('name', 'Résistant')->first();
        $this->assertNotNull($resistant);
        $this->assertFalse($resistant->invulnerable);
        $this->assertStringContainsString('divisés par 2', (string) $resistant->description);
        $this->assertNotNull(Condition::query()->where('name', 'Résistant à l\'élément Eau')->first());
        $this->assertNotNull(Condition::query()->where('name', 'Terrorisé')->first());
        $this->assertSame(0, Condition::query()->where('state', Condition::STATE_RAW)->count());
        $this->assertGreaterThan(0, $stats['deleted']);
        $this->assertSame(0, $stats['skipped']);

        app(PlayableConditionCatalog::class)->sync(deleteRaw: true);
        $this->assertSame(1, Condition::query()->where('name', 'À terre')->count());
        $this->assertSame('Texte déjà rédigé.', $empoisonne->fresh()->description);
    }
}
