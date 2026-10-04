<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Création monstre via modal (creature + monster).
 */
class MonsterControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_monster_with_new_creature(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('entities.monsters.store'), [
            'name' => 'Bouftou test',
            'description' => 'Créature de test',
            'level' => '5',
            'redirect_after_create' => 'edit',
        ]);

        $monster = Monster::query()->whereHas('creature', fn ($q) => $q->where('name', 'Bouftou test'))->first();
        $this->assertNotNull($monster);
        $this->assertInstanceOf(Creature::class, $monster->creature);
        $this->assertSame($admin->id, $monster->creature->created_by);
        $this->assertSame('draft', $monster->creature->state);
        $this->assertNull($monster->creature->life);
        $this->assertNull($monster->creature->pa);
        $this->assertNull($monster->creature->pm);
        $this->assertNull($monster->creature->ca);
        $this->assertNull($monster->creature->res_fixe_neutre);
        $this->assertNull($monster->creature->res_fixe_feu);
        $this->assertFalse($monster->creature->hasExplicitTotal('res_fixe_neutre'));
        $this->assertFalse($monster->creature->hasExplicitTotal('res_fixe_feu'));
        $this->assertSame(0, (int) $monster->creature->acrobatie_mastery);
        $this->assertSame(0, (int) $monster->creature->read_level);
        $this->assertSame(3, (int) $monster->creature->write_level);
        $this->assertNull($monster->creature->image);
        $response->assertRedirect(route('entities.monsters.edit', $monster));
    }

    public function test_player_cannot_store_monster(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($player)
            ->post(route('entities.monsters.store'), [
                'name' => 'Interdit',
            ])
            ->assertForbidden();
    }
}
