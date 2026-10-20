<?php

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Campaign;
use App\Models\User;
use Tests\TestCase;

/**
 * Enregistrement sheet d'une campagne (PATCH entities.campaigns.update).
 */
class CampaignControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        $this->withSession(['_token' => 'test-token']);
    }

    public function test_admin_can_update_campaign_metadata(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $campaign = Campaign::factory()->create([
            'name' => 'Campagne originale',
            'description' => 'Ancien pitch',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('entities.campaigns.edit', $campaign))
            ->patch(route('entities.campaigns.update', $campaign), [
                'name' => 'Campagne corrigée',
                'description' => 'Nouveau pitch',
            ]);

        $response->assertOk();
        $fresh = $campaign->fresh();
        $this->assertSame('Campagne corrigée', $fresh->name);
        $this->assertSame('Nouveau pitch', $fresh->description);
    }

    public function test_inertia_update_redirects_back_to_the_edit_sheet(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $campaign = Campaign::factory()->create([
            'name' => 'Campagne Inertia',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('entities.campaigns.edit', $campaign))
            ->withHeader('X-Inertia', 'true')
            ->patch(route('entities.campaigns.update', $campaign), [
                'name' => 'Campagne Inertia enregistrée',
            ]);

        $response->assertRedirect(route('entities.campaigns.edit', $campaign));
        $this->assertSame('Campagne Inertia enregistrée', $campaign->fresh()->name);
    }
}
