<?php

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Tests Feature pour SpellController
 *
 * Vérifie que :
 * - Un utilisateur peut modifier un sort qu'il a créé
 * - Un admin peut modifier n'importe quel sort
 * - La méthode updateBreeds synchronise correctement les breeds (affichés « Classes »)
 * - La méthode updateSpellTypes synchronise correctement les types de sort
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class SpellControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_spell_global_metadata(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($admin)->patch(route('entities.spells.update', $spell), [
            'name' => $spell->name,
            'cast_in_line' => true,
            'cast_in_diagonal' => true,
            'target_type' => 'trap',
            'max_stack' => 10,
            'global_cooldown' => 6,
        ]);

        $response->assertSessionHasNoErrors();
        $spell->refresh();
        $this->assertTrue($spell->cast_in_line);
        $this->assertTrue($spell->cast_in_diagonal);
        $this->assertSame('trap', $spell->target_type);
        $this->assertSame(10, $spell->max_stack);
        $this->assertSame(6, $spell->global_cooldown);
    }

    /**
     * Auteur : peut mettre à jour les champs (FormRequest aligné sur SpellPolicy).
     */
    public function test_author_can_update_own_spell_fields(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create([
            'created_by' => $author->id,
            'name' => 'Ancien nom',
        ]);

        $response = $this->actingAs($author)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.update', $spell), [
                'name' => 'Nouveau nom auteur',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Nouveau nom auteur', $spell->fresh()->name);
    }

    /**
     * Non-auteur non-admin : refus de mise à jour des champs.
     */
    public function test_non_author_cannot_update_foreign_spell_fields(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create([
            'created_by' => $owner->id,
            'name' => 'Sort verrouillé',
        ]);

        $response = $this->actingAs($other)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.update', $spell), [
                'name' => 'Tentative',
            ]);

        $response->assertForbidden();
        $this->assertSame('Sort verrouillé', $spell->fresh()->name);
    }

    /**
     * updateBreeds conserve character_level / slot_index / choice_order des liaisons existantes.
     */
    public function test_update_breeds_preserves_existing_pivot_slots(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create(['created_by' => $user->id]);
        $breedKeep = Breed::factory()->create();
        $breedDrop = Breed::factory()->create();

        $spell->breeds()->attach([
            $breedKeep->id => [
                'character_level' => 7,
                'slot_index' => 2,
                'choice_order' => 3,
            ],
            $breedDrop->id => [
                'character_level' => 1,
                'slot_index' => 0,
                'choice_order' => 0,
            ],
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [$breedKeep->id],
            ]);

        $response->assertRedirect();
        $pivot = $spell->fresh()->breeds()->where('breeds.id', $breedKeep->id)->first()?->pivot;
        $this->assertNotNull($pivot);
        $this->assertSame(7, (int) $pivot->character_level);
        $this->assertSame(2, (int) $pivot->slot_index);
        $this->assertSame(3, (int) $pivot->choice_order);
    }

    public function test_spell_global_metadata_validation_rejects_out_of_range_values(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.update', $spell), [
                'name' => $spell->name,
                'target_type' => 'aura',
                'max_stack' => 11,
                'global_cooldown' => -1,
            ]);

        $response->assertSessionHasErrors(['target_type', 'max_stack', 'global_cooldown']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Désactiver le middleware role pour les tests (on teste les policies directement)
        $this->withoutMiddleware(CheckRole::class);
        // Désactiver explicitement le CSRF pour ces tests
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    /**
     * Test : Un utilisateur peut ajouter des classes à son sort
     */
    public function test_user_can_add_breeds_to_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed1 = Breed::factory()->create();
        $breed2 = Breed::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateBreeds', $spell), [
                '_method' => 'PATCH',
                'breeds' => [$breed1->id, $breed2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->breeds);
        $this->assertTrue($spell->fresh()->breeds->contains($breed1));
        $this->assertTrue($spell->fresh()->breeds->contains($breed2));
    }

    /**
     * Test : Un utilisateur peut retirer des classes de son sort
     */
    public function test_user_can_remove_breeds_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed1 = Breed::factory()->create();
        $breed2 = Breed::factory()->create();
        $breed3 = Breed::factory()->create();

        // Ajouter initialement 3 breeds
        $spell->breeds()->attach([$breed1->id, $breed2->id, $breed3->id]);

        // Retirer breed2 et breed3, garder seulement breed1
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [$breed1->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $spell->fresh()->breeds);
        $this->assertTrue($spell->fresh()->breeds->contains($breed1));
        $this->assertFalse($spell->fresh()->breeds->contains($breed2));
        $this->assertFalse($spell->fresh()->breeds->contains($breed3));
    }

    /**
     * Test : Un utilisateur peut remplacer toutes les classes de son sort
     */
    public function test_user_can_replace_all_breeds_in_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $oldBreed = Breed::factory()->create();
        $newBreed1 = Breed::factory()->create();
        $newBreed2 = Breed::factory()->create();

        // Ajouter un breed initialement
        $spell->breeds()->attach($oldBreed->id);

        // Remplacer par de nouveaux breeds
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [$newBreed1->id, $newBreed2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->breeds);
        $this->assertFalse($spell->fresh()->breeds->contains($oldBreed));
        $this->assertTrue($spell->fresh()->breeds->contains($newBreed1));
        $this->assertTrue($spell->fresh()->breeds->contains($newBreed2));
    }

    /**
     * Test : Un utilisateur peut vider toutes les classes d'un sort
     */
    public function test_user_can_clear_all_breeds_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed1 = Breed::factory()->create();
        $breed2 = Breed::factory()->create();

        // Ajouter des breeds initialement
        $spell->breeds()->attach([$breed1->id, $breed2->id]);

        // Vider tous les breeds
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $spell->fresh()->breeds);
    }

    /**
     * Test : Un admin peut modifier les classes de n'importe quel sort
     */
    public function test_admin_can_update_breeds_of_any_spell(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $breed1 = Breed::factory()->create();
        $breed2 = Breed::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [$breed1->id, $breed2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->breeds);
    }

    /**
     * Test : Un utilisateur ne peut pas modifier les classes d'un sort qu'il n'a pas créé
     */
    public function test_user_cannot_update_breeds_of_other_user_spell(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $breed1 = Breed::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [$breed1->id],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $spell->fresh()->breeds);
    }

    /**
     * Test : La validation échoue si classes n'est pas un array
     */
    public function test_update_breeds_fails_if_breeds_is_not_array(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('breeds');
    }

    /**
     * Test : La validation échoue si une classe n'existe pas
     */
    public function test_update_breeds_fails_if_breed_does_not_exist(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed = Breed::factory()->create();

        // Supprimer définitivement le breed pour qu'il n'existe plus
        $breed->forceDelete();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => [$breed->id],
            ]);

        $response->assertSessionHasErrors('breeds.0');
    }

    /**
     * Test : La validation échoue si classes est manquant
     */
    public function test_update_breeds_fails_if_breeds_is_missing(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateBreeds', $spell), [
                '_method' => 'PATCH',
            ]);

        $response->assertSessionHasErrors('breeds');
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les classes
     */
    public function test_guest_cannot_update_breeds(): void
    {
        $spell = Spell::factory()->create();
        $breed = Breed::factory()->create();

        $response = $this->post(route('entities.spells.updateBreeds', $spell), [
            '_method' => 'PATCH',
            'breeds' => [$breed->id],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : Un utilisateur peut ajouter des types de sort à son sort
     */
    public function test_user_can_add_spell_types_to_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id, $spellType2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->spellTypes);
        $this->assertTrue($spell->fresh()->spellTypes->contains($spellType1));
        $this->assertTrue($spell->fresh()->spellTypes->contains($spellType2));
    }

    /**
     * Test : Un utilisateur peut retirer des types de sort de son sort
     */
    public function test_user_can_remove_spell_types_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();
        $spellType3 = SpellType::factory()->create();

        // Ajouter initialement 3 types de sort
        $spell->spellTypes()->attach([$spellType1->id, $spellType2->id, $spellType3->id]);

        // Retirer spellType2 et spellType3, garder seulement spellType1
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $spell->fresh()->spellTypes);
        $this->assertTrue($spell->fresh()->spellTypes->contains($spellType1));
        $this->assertFalse($spell->fresh()->spellTypes->contains($spellType2));
        $this->assertFalse($spell->fresh()->spellTypes->contains($spellType3));
    }

    /**
     * Test : Un utilisateur peut remplacer tous les types de sort de son sort
     */
    public function test_user_can_replace_all_spell_types_in_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $oldSpellType = SpellType::factory()->create();
        $newSpellType1 = SpellType::factory()->create();
        $newSpellType2 = SpellType::factory()->create();

        // Ajouter un type de sort initialement
        $spell->spellTypes()->attach($oldSpellType->id);

        // Remplacer par de nouveaux types de sort
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$newSpellType1->id, $newSpellType2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->spellTypes);
        $this->assertFalse($spell->fresh()->spellTypes->contains($oldSpellType));
        $this->assertTrue($spell->fresh()->spellTypes->contains($newSpellType1));
        $this->assertTrue($spell->fresh()->spellTypes->contains($newSpellType2));
    }

    /**
     * Test : Un utilisateur peut vider tous les types de sort d'un sort
     */
    public function test_user_can_clear_all_spell_types_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();

        // Ajouter des types de sort initialement
        $spell->spellTypes()->attach([$spellType1->id, $spellType2->id]);

        // Vider tous les types de sort
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $spell->fresh()->spellTypes);
    }

    /**
     * Test : Un admin peut modifier les types de sort de n'importe quel sort
     */
    public function test_admin_can_update_spell_types_of_any_spell(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id, $spellType2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->spellTypes);
    }

    /**
     * Test : Un utilisateur ne peut pas modifier les types de sort d'un sort qu'il n'a pas créé
     */
    public function test_user_cannot_update_spell_types_of_other_user_spell(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $spellType1 = SpellType::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $spell->fresh()->spellTypes);
    }

    /**
     * Test : La validation échoue si spellTypes n'est pas un array
     */
    public function test_update_spell_types_fails_if_spell_types_is_not_array(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('spellTypes');
    }

    /**
     * Test : La validation échoue si un type de sort n'existe pas
     */
    public function test_update_spell_types_fails_if_spell_type_does_not_exist(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType = SpellType::factory()->create();

        // Supprimer définitivement le type de sort pour qu'il n'existe plus
        $spellType->forceDelete();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType->id],
            ]);

        $response->assertSessionHasErrors('spellTypes.0');
    }

    /**
     * Test : La validation échoue si spellTypes est manquant
     */
    public function test_update_spell_types_fails_if_spell_types_is_missing(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
            ]);

        $response->assertSessionHasErrors('spellTypes');
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les types de sort
     */
    public function test_guest_cannot_update_spell_types(): void
    {
        $spell = Spell::factory()->create();
        $spellType = SpellType::factory()->create();

        $response = $this->post(route('entities.spells.updateSpellTypes', $spell), [
            '_method' => 'PATCH',
            'spellTypes' => [$spellType->id],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page d'édition charge les types de sort disponibles et les breeds déjà liés sur la ressource sort.
     */
    public function test_edit_page_loads_spell_types_and_spell_includes_linked_breeds(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $breed1 = Breed::factory()->create(['name' => 'Classe 1']);
        $spellType1 = SpellType::factory()->create(['name' => 'Type 1']);
        $spell->breeds()->attach($breed1->id);
        $spell->spellTypes()->attach($spellType1->id);

        $response = $this->actingAs($user)
            ->get(route('entities.spells.edit', $spell));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/spell/Edit')
            ->has('spell')
            ->has('availableSpellTypes')
            ->where('spell.data.breeds.0.id', $breed1->id)
            ->where('spell.data.spellTypes.0.id', $spellType1->id)
        );
    }

    /**
     * Test : La charge utile JSON pour l’éditeur modal expose les mêmes blocs que la page Edit.
     */
    public function test_edit_payload_returns_json_for_authorized_user(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('entities.spells.edit-payload', $spell));

        $response->assertOk();
        $response->assertJsonStructure([
            'spell' => ['id', 'name'],
            'availableSpellTypes',
            'availableEffects',
            'effectEntityType',
            'effectFormOptions',
            'spellEffectGroups',
            'spellHolders' => ['monsters', 'npcs', 'breeds'],
        ]);
        $response->assertJsonPath('effectEntityType', 'spell');
        // Payload allégé : pas de dump massif des définitions (recherche API dédiée).
        $this->assertSame([], $response->json('availableEffects'));
        $this->assertArrayNotHasKey('spellEffects', $response->json('spell'));
    }

    /**
     * La fiche d’édition liste les monstres, PNJ et classes qui possèdent le sort.
     */
    public function test_edit_payload_lists_monsters_npcs_and_breeds_that_own_the_spell(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['created_by' => $user->id]);

        $monsterCreature = $this->creatureNamed('Bouftou du test');
        $monster = Monster::factory()->create(['creature_id' => $monsterCreature->id]);
        $npcCreature = $this->creatureNamed('Ganymède du test');
        $npc = Npc::factory()->create(['creature_id' => $npcCreature->id]);
        $orphan = $this->creatureNamed('Créature orpheline');
        $spell->creatures()->attach([$monsterCreature->id, $npcCreature->id, $orphan->id]);

        $breed = Breed::factory()->create(['name' => 'Iop du test']);
        $spell->breeds()->attach($breed->id);

        $response = $this->actingAs($user)
            ->getJson(route('entities.spells.edit-payload', $spell));

        $response->assertOk();
        $response->assertJsonPath('spellHolders.monsters.0.id', $monster->id);
        $response->assertJsonPath('spellHolders.monsters.0.name', 'Bouftou du test');
        $response->assertJsonPath('spellHolders.npcs.0.id', $npc->id);
        $response->assertJsonPath('spellHolders.npcs.0.name', 'Ganymède du test');
        $response->assertJsonPath('spellHolders.breeds.0.id', $breed->id);
        $response->assertJsonPath('spellHolders.breeds.0.name', 'Iop du test');
        $this->assertCount(1, $response->json('spellHolders.monsters'));
        $this->assertCount(1, $response->json('spellHolders.npcs'));
    }

    /**
     * Test : redirect_after_update=index renvoie vers la liste (éditeur modal).
     */
    public function test_spell_update_redirects_to_index_when_requested(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.index'))
            ->patch(route('entities.spells.update', $spell), [
                'name' => 'Nom modal '.$spell->id,
                'redirect_after_update' => 'index',
            ]);

        $response->assertRedirect(route('entities.spells.index'));
    }

    /**
     * Test : redirect_after_update=stay renvoie en arrière (modal liste : pas de navigation vers la fiche sort).
     */
    public function test_spell_update_redirects_back_when_stay_requested(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.index'))
            ->patch(route('entities.spells.update', $spell), [
                'name' => 'Nom stay '.$spell->id,
                'redirect_after_update' => 'stay',
            ]);

        $response->assertRedirect(route('entities.spells.index'));
    }

    /**
     * Test : redirect_after_update=edit renvoie vers l’éditeur (page fiche sort).
     */
    public function test_spell_update_redirects_to_edit_when_requested(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.update', $spell), [
                'name' => 'Nom éditeur '.$spell->id,
                'redirect_after_update' => 'edit',
            ]);

        $response->assertRedirect(route('entities.spells.edit', $spell));
    }

    /**
     * Test : La synchronisation des breeds fonctionne avec plusieurs breeds
     */
    public function test_sync_breeds_works_with_multiple_breeds(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $breeds = Breed::factory()->count(5)->create();
        $breedIds = $breeds->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateBreeds', $spell), [
                'breeds' => $breedIds,
            ]);

        $response->assertRedirect();
        $this->assertCount(5, $spell->fresh()->breeds);
        foreach ($breeds as $breed) {
            $this->assertTrue($spell->fresh()->breeds->contains($breed));
        }
    }

    /**
     * Test : La synchronisation des types de sort fonctionne avec plusieurs types
     */
    public function test_sync_spell_types_works_with_multiple_types(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellTypes = SpellType::factory()->count(5)->create();
        $spellTypeIds = $spellTypes->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => $spellTypeIds,
            ]);

        $response->assertRedirect();
        $this->assertCount(5, $spell->fresh()->spellTypes);
        foreach ($spellTypes as $spellType) {
            $this->assertTrue($spell->fresh()->spellTypes->contains($spellType));
        }
    }

    /**
     * Créature minimale : la factory embarque encore des colonnes retirées du schéma.
     */
    private function creatureNamed(string $name): Creature
    {
        $columns = array_flip(Schema::getColumnListing('creatures'));
        $attributes = array_intersect_key(Creature::factory()->raw(['name' => $name]), $columns);

        return Creature::query()->create($attributes);
    }
}
