<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Persistance et validation des caractéristiques composables (monstre / PNJ).
 */
final class CreatureComposableCharacteristicsUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_monster_update_persists_creature_total_and_context(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();
        $creature = $monster->creature;
        $this->assertNotNull($creature);

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'ca' => '18',
                'ca_context' => '1',
                'life' => '95',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $creature->refresh();
        $this->assertSame('18', $creature->ca);
        $this->assertSame('1', $creature->ca_context);
        $this->assertSame('95', $creature->life);
    }

    public function test_monster_update_rejects_invalid_context_formula(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'ca_context' => '{[5-8]}',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['ca_context']);
    }

    public function test_npc_update_rejects_malformed_total_formula(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $npc = Npc::factory()->create();

        $this->actingAs($admin)
            ->from(route('entities.npcs.edit', $npc))
            ->patch(route('entities.npcs.update', $npc), [
                'ini' => '{[niveau / 3}',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['ini']);
    }

    public function test_monster_update_persists_profile_masteries(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create([
            'acrobatics_mastery' => 0,
            'save_vitality_mastery' => 0,
        ]);

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'acrobatics_mastery' => 2,
                'save_vitality_mastery' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $monster->refresh();
        $this->assertSame(2, (int) $monster->acrobatics_mastery);
        $this->assertSame(1, (int) $monster->save_vitality_mastery);
    }

    public function test_explicit_total_priority_at_runtime_when_column_set(): void
    {
        $creature = Monster::factory()->create()->creature;
        $this->assertNotNull($creature);

        $creature->update([
            'ca' => '99',
            'ca_context' => '0',
            'level' => '5',
        ]);

        $this->assertTrue($creature->fresh()->hasExplicitTotal('ca'));
    }
}
