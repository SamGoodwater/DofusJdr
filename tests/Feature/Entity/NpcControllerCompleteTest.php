<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Campaign;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Item;
use App\Models\Entity\Language;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Scenario;
use App\Models\Entity\Shop;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\Type\ItemType;
use App\Models\User;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * CRUD PNJ : création Creature, show nested, patch identité, langues, kit d’équipement.
 */
class NpcControllerCompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_store_creates_creature_and_npc(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('entities.npcs.store'), [
            'name' => 'Garde de test',
            'location' => 'Astrub',
            'level' => '8',
            'npc_role' => NpcRole::GUARD,
            'size' => CreatureSize::MOYEN,
            'redirect_after_create' => 'edit',
        ]);

        $npc = Npc::query()->whereHas('creature', fn ($q) => $q->where('name', 'Garde de test'))->first();
        $this->assertNotNull($npc);
        $this->assertInstanceOf(Creature::class, $npc->creature);
        $this->assertSame('Astrub', $npc->creature->location);
        $this->assertSame(NpcRole::GUARD, $npc->npc_role);
        $this->assertSame(CreatureSize::MOYEN, (int) $npc->size);
        $this->assertSame($npc->state, $npc->creature->state);
        $this->assertSame($admin->id, $npc->creature->created_by);
        $this->assertNull($npc->creature->life);
        $this->assertNull($npc->creature->pa);
        $this->assertNull($npc->creature->pm);
        $this->assertNull($npc->creature->ca);
        $this->assertNull($npc->creature->res_fixe_neutre);
        $this->assertNull($npc->creature->res_fixe_feu);
        $this->assertFalse($npc->creature->hasExplicitTotal('res_fixe_neutre'));
        $this->assertFalse($npc->creature->hasExplicitTotal('res_fixe_feu'));
        $this->assertSame(0, (int) $npc->creature->acrobatie_mastery);
        $this->assertSame(0, (int) $npc->creature->read_level);
        $this->assertSame(3, (int) $npc->creature->write_level);
        $this->assertNull($npc->creature->image);
        $response->assertRedirect(route('entities.npcs.edit', $npc));
    }

    public function test_show_hides_foreign_draft_spells(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableSpell = Spell::factory()->create([
            'name' => 'Sort Public PNJ',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpell = Spell::factory()->create([
            'name' => 'Sort Brouillon PNJ',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $creature->spells()->attach([$playableSpell->id, $draftSpell->id]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->get(route('entities.npcs.show', $npc));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/npc/Show')
            ->has('npc.data.creature.spells')
            ->where('npc.data.creature.spells', function ($spells) use ($playableSpell, $draftSpell) {
                $ids = collect($spells)->pluck('id')->all();

                return in_array($playableSpell->id, $ids, true)
                    && ! in_array($draftSpell->id, $ids, true);
            })
        );
    }

    public function test_show_hides_foreign_draft_creature_traits_from_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableTrait = CreatureTrait::factory()->create([
            'name' => 'Trait Public PNJ',
            'state' => CreatureTrait::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftTrait = CreatureTrait::factory()->create([
            'name' => 'Trait Brouillon PNJ',
            'state' => CreatureTrait::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $creature->creatureTraits()->attach([$playableTrait->id, $draftTrait->id]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->get(route('entities.npcs.show', $npc));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/npc/Show')
            ->where('npc.data.creature', function ($creature) use ($playableTrait, $draftTrait) {
                $traits = $creature['creatureTraits'] ?? $creature['creature_traits'] ?? [];
                $ids = collect($traits)->pluck('id')->all();

                return in_array($playableTrait->id, $ids, true)
                    && ! in_array($draftTrait->id, $ids, true);
            })
        );
    }

    public function test_show_hides_foreign_draft_shop_panoply_and_campaign_from_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $visibility = [
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ];
        Shop::factory()->create([
            'name' => 'Réserve secrète',
            'npc_id' => $npc->id,
            'state' => Shop::STATE_DRAFT,
            ...$visibility,
        ]);
        $playablePanoply = Panoply::factory()->create([
            'name' => 'Panoplie publique PNJ',
            'state' => Panoply::STATE_PLAYABLE,
            ...$visibility,
        ]);
        $draftPanoply = Panoply::factory()->create([
            'name' => 'Panoplie brouillon PNJ',
            'state' => Panoply::STATE_DRAFT,
            ...$visibility,
        ]);
        $npc->panoplies()->attach([$playablePanoply->id, $draftPanoply->id]);
        $playableCampaign = Campaign::factory()->create([
            'name' => 'Campagne publique PNJ',
            'state' => Campaign::STATE_PLAYABLE,
            ...$visibility,
        ]);
        $draftCampaign = Campaign::factory()->create([
            'name' => 'Intrigue secrète',
            'state' => Campaign::STATE_DRAFT,
            ...$visibility,
        ]);
        $npc->campaigns()->attach([$playableCampaign->id, $draftCampaign->id]);
        $playableScenario = Scenario::factory()->create([
            'name' => 'Scénario public PNJ',
            'state' => Scenario::STATE_PLAYABLE,
            ...$visibility,
        ]);
        $draftScenario = Scenario::factory()->create([
            'name' => 'Scénario brouillon PNJ',
            'state' => Scenario::STATE_DRAFT,
            ...$visibility,
        ]);
        $npc->scenarios()->attach([$playableScenario->id, $draftScenario->id]);

        $response = $this->get(route('entities.npcs.show', $npc));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/npc/Show')
            ->where('npc.data.shop', fn ($shop) => $shop === null)
            ->where('npc.data.panoplies', function ($panoplies) use ($playablePanoply, $draftPanoply) {
                $ids = collect($panoplies)->pluck('id')->all();

                return in_array($playablePanoply->id, $ids, true)
                    && ! in_array($draftPanoply->id, $ids, true);
            })
            ->where('npc.data.campaigns', function ($campaigns) use ($playableCampaign, $draftCampaign) {
                $ids = collect($campaigns)->pluck('id')->all();

                return in_array($playableCampaign->id, $ids, true)
                    && ! in_array($draftCampaign->id, $ids, true);
            })
            ->where('npc.data.scenarios', function ($scenarios) use ($playableScenario, $draftScenario) {
                $ids = collect($scenarios)->pluck('id')->all();

                return in_array($playableScenario->id, $ids, true)
                    && ! in_array($draftScenario->id, $ids, true);
            })
        );
    }

    public function test_update_patches_creature_name_and_location(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create(['state' => Npc::STATE_DRAFT]);

        $this->actingAs($admin)
            ->from(route('entities.npcs.edit', $npc))
            ->patch(route('entities.npcs.update', $npc), [
                'name' => 'Nouveau nom',
                'location' => 'Bonta',
                'npc_role' => NpcRole::MERCHANT,
            ])
            ->assertRedirect(route('entities.npcs.edit', $npc));

        $npc->refresh();
        $this->assertSame('Nouveau nom', $npc->creature->name);
        $this->assertSame('Bonta', $npc->creature->location);
        $this->assertSame(NpcRole::MERCHANT, $npc->npc_role);
    }

    public function test_update_languages_syncs_pivot_order(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create();
        $langA = Language::factory()->create(['name' => 'Amaknéen']);
        $langB = Language::factory()->create(['name' => 'Roublard']);

        $this->actingAs($admin)
            ->patch(route('entities.npcs.updateLanguages', $npc), [
                'languages' => [$langB->id, $langA->id],
            ])
            ->assertRedirect();

        $ids = $npc->fresh()->languages->pluck('id')->all();
        $this->assertSame([$langB->id, $langA->id], $ids);
        $this->assertSame(0, (int) $npc->fresh()->languages->first()->pivot->sort_order);
    }

    public function test_update_items_allows_two_rings_and_rejects_two_hats(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create();

        $ringType = ItemType::factory()->create([
            'name' => 'Anneau test',
            'dofusdb_type_id' => 9,
            'show_in_catalog' => true,
        ]);
        $hatType = ItemType::factory()->create([
            'name' => 'Chapeau test',
            'dofusdb_type_id' => 16,
            'show_in_catalog' => true,
        ]);

        $ring1 = Item::factory()->create(['item_type_id' => $ringType->id, 'name' => 'Anneau A']);
        $ring2 = Item::factory()->create(['item_type_id' => $ringType->id, 'name' => 'Anneau B']);
        $hat1 = Item::factory()->create(['item_type_id' => $hatType->id, 'name' => 'Chapeau A']);
        $hat2 = Item::factory()->create(['item_type_id' => $hatType->id, 'name' => 'Chapeau B']);

        $this->actingAs($admin)
            ->patch(route('entities.npcs.updateItems', $npc), [
                'items' => [$ring1->id, $ring2->id],
            ])
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(
            [$ring1->id, $ring2->id],
            $npc->creature->fresh()->items->pluck('id')->all()
        );

        $this->actingAs($admin)
            ->from(route('entities.npcs.edit', $npc))
            ->patch(route('entities.npcs.updateItems', $npc), [
                'items' => [$hat1->id, $hat2->id],
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('items');
    }

    public function test_deleting_breed_nulls_npc_breed_id(): void
    {
        $breed = Breed::factory()->create();
        $npc = Npc::factory()->create(['breed_id' => $breed->id]);

        $breed->forceDelete();

        $npc->refresh();
        $this->assertNull($npc->breed_id);
        $this->assertDatabaseHas('npcs', ['id' => $npc->id]);
    }

    public function test_player_cannot_store_npc(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($player)
            ->post(route('entities.npcs.store'), [
                'name' => 'Interdit',
            ])
            ->assertForbidden();
    }

    public function test_admin_index_passes_breeds_for_create_modal(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Breed::factory()->create([
            'name' => 'Iop',
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $this->actingAs($admin)
            ->get(route('entities.npcs.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/npc/Index')
                ->has('breeds')
                ->has('filters')
                ->where('breeds.0.name', 'Iop'));
    }

    public function test_show_hides_foreign_draft_breed_and_specialization_from_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $draftBreed = Breed::factory()->create([
            'name' => 'Classe Secrète PNJ',
            'state' => Breed::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpec = Specialization::factory()->create([
            'name' => 'Spé Secrète PNJ',
            'state' => Specialization::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'breed_id' => $draftBreed->id,
            'specialization_id' => $draftSpec->id,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->get(route('entities.npcs.show', $npc))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/npc/Show')
                ->where('npc.data.breed', null)
                ->where('npc.data.specialization', null));

        $this->actingAs($author)
            ->get(route('entities.npcs.show', $npc))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('npc.data.breed.id', $draftBreed->id)
                ->where('npc.data.breed.name', 'Classe Secrète PNJ')
                ->where('npc.data.specialization.id', $draftSpec->id)
                ->where('npc.data.specialization.name', 'Spé Secrète PNJ'));
    }

    public function test_show_keeps_playable_breed_and_specialization_for_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableBreed = Breed::factory()->create([
            'name' => 'Iop Public',
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $playableSpec = Specialization::factory()->create([
            'name' => 'Voie Publique',
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'breed_id' => $playableBreed->id,
            'specialization_id' => $playableSpec->id,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->get(route('entities.npcs.show', $npc))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('npc.data.breed.id', $playableBreed->id)
                ->where('npc.data.breed.name', 'Iop Public')
                ->where('npc.data.specialization.id', $playableSpec->id)
                ->where('npc.data.specialization.name', 'Voie Publique'));
    }

    public function test_edit_still_loads_draft_breed_and_specialization_for_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature = Creature::factory()->create(['created_by' => $admin->id]);
        $draftBreed = Breed::factory()->create([
            'name' => 'Classe WIP Édition',
            'state' => Breed::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $admin->id,
        ]);
        $draftSpec = Specialization::factory()->create([
            'name' => 'Spé WIP Édition',
            'state' => Specialization::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $admin->id,
        ]);
        $npc = Npc::factory()->create([
            'creature_id' => $creature->id,
            'breed_id' => $draftBreed->id,
            'specialization_id' => $draftSpec->id,
            'state' => Npc::STATE_DRAFT,
        ]);

        $this->actingAs($admin)
            ->get(route('entities.npcs.edit', $npc))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/npc/Edit')
                ->where('npc.data.breed.id', $draftBreed->id)
                ->where('npc.data.specialization.id', $draftSpec->id));
    }
}
