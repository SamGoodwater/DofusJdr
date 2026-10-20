<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Capability;
use App\Models\User;
use Tests\TestCase;

/**
 * Vérifie que la mise à jour HTTP normalise les champs NOT NULL (ex. saisie vide → null en prod).
 */
class CapabilityControllerUpdateTest extends TestCase
{
    public function test_update_accepts_null_time_before_use_again_and_persists_db_default(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $capability = Capability::factory()->create([
            'time_before_use_again' => '1 fois par combat',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->patchJson(
            '/entities/capabilities/'.$capability->id,
            [
                'name' => $capability->name,
                'time_before_use_again' => null,
                'ritual_available' => null,
            ]
        );

        $response->assertRedirect();
        $capability->refresh();
        $this->assertSame('0', $capability->time_before_use_again);
        $this->assertTrue($capability->ritual_available);
    }

    /**
     * Modal EntitySurfaceHost : stay → back (pas de navigation vers la fiche capacité).
     */
    public function test_update_redirects_back_when_stay_requested(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $capability = Capability::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)
            ->from(route('entities.capabilities.index'))
            ->patch(route('entities.capabilities.update', $capability), [
                'name' => 'Capacité stay '.$capability->id,
                'redirect_after_update' => 'stay',
            ]);

        $response->assertRedirect(route('entities.capabilities.index'));
    }

    public function test_update_redirects_to_edit_when_requested(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $capability = Capability::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)
            ->from(route('entities.capabilities.edit', $capability))
            ->patch(route('entities.capabilities.update', $capability), [
                'name' => 'Capacité éditeur '.$capability->id,
                'redirect_after_update' => 'edit',
            ]);

        $response->assertRedirect(route('entities.capabilities.edit', $capability));
    }
}
