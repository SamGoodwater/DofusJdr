<?php

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Scenario;
use App\Models\User;
use Tests\TestCase;

/**
 * Enregistrement sheet d'un scénario (PATCH entities.scenarios.update).
 */
class ScenarioControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        $this->withSession(['_token' => 'test-token']);
    }

    public function test_admin_can_update_scenario_metadata(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $scenario = Scenario::factory()->create([
            'name' => 'Scénario original',
            'description' => 'Ancienne accroche',
            'keyword' => 'incarnam',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('entities.scenarios.edit', $scenario))
            ->patch(route('entities.scenarios.update', $scenario), [
                'name' => 'Scénario corrigé',
                'description' => 'Nouvelle accroche',
                'keyword' => 'amakna',
            ]);

        $response->assertRedirect();
        $fresh = $scenario->fresh();
        $this->assertSame('Scénario corrigé', $fresh->name);
        $this->assertSame('Nouvelle accroche', $fresh->description);
        $this->assertSame('amakna', $fresh->keyword);
    }

    public function test_guest_cannot_update_scenario_metadata(): void
    {
        $author = User::factory()->create();
        $scenario = Scenario::factory()->create([
            'name' => 'Scénario public',
            'created_by' => $author->id,
        ]);

        $response = $this->patch(route('entities.scenarios.update', $scenario), [
            'name' => 'Nom volé',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame('Scénario public', $scenario->fresh()->name);
    }
}
