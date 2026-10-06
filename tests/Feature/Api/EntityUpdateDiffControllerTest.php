<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\User;
use App\Services\Entity\EntityUpdateDiffService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Apply / restore d’instantané : pas d’IDOR entre comptes, SEC-07 sur les niveaux d’accès.
 */
class EntityUpdateDiffControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_other_admin_cannot_apply_or_restore_foreign_snapshot(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $other = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'created_by' => $owner->id,
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
        ]);

        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $item->update(['name' => 'Cape nouvelle', 'description' => 'après']);
        $diff = $svc->remember($owner, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());
        $snapshotId = $diff['snapshot_id'];

        $this->actingAs($other)
            ->postJson("/api/entities/items/{$item->id}/update-diff/apply", [
                'snapshot_id' => $snapshotId,
                'restore_keys' => ['name'],
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->actingAs($other)
            ->postJson("/api/entities/items/{$item->id}/update-diff/restore", [
                'snapshot_id' => $snapshotId,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $item->refresh();
        $this->assertSame('Cape nouvelle', $item->name);
        $this->assertSame('après', $item->description);

        $this->actingAs($owner)
            ->postJson("/api/entities/items/{$item->id}/update-diff/restore", [
                'snapshot_id' => $snapshotId,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame('avant', $item->description);
    }

    public function test_game_master_restore_does_not_revert_access_levels(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create([
            'created_by' => $gm->id,
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $item->update(['name' => 'Cape nouvelle']);
        $diff = $svc->remember($gm, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());

        $item->update([
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($gm)
            ->postJson("/api/entities/items/{$item->id}/update-diff/restore", [
                'snapshot_id' => $diff['snapshot_id'],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame(User::ROLE_GAME_MASTER, (int) $item->read_level);
        $this->assertSame(User::ROLE_ADMIN, (int) $item->write_level);
    }

    public function test_game_master_apply_cannot_restore_access_level_keys(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create([
            'created_by' => $gm->id,
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $item->update(['name' => 'Cape nouvelle']);
        $diff = $svc->remember($gm, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());

        $item->update(['read_level' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->postJson("/api/entities/items/{$item->id}/update-diff/apply", [
                'snapshot_id' => $diff['snapshot_id'],
                'restore_keys' => ['name', 'read_level'],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame(User::ROLE_GAME_MASTER, (int) $item->read_level);
    }

    public function test_admin_restore_can_revert_access_levels(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'created_by' => $admin->id,
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $item->update([
            'name' => 'Cape nouvelle',
            'read_level' => User::ROLE_GAME_MASTER,
        ]);
        $diff = $svc->remember($admin, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());

        $this->actingAs($admin)
            ->postJson("/api/entities/items/{$item->id}/update-diff/restore", [
                'snapshot_id' => $diff['snapshot_id'],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame(User::ROLE_GUEST, (int) $item->read_level);
        $this->assertSame(User::ROLE_GAME_MASTER, (int) $item->write_level);
    }
}
