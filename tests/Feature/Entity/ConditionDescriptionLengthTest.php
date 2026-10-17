<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Condition;
use App\Models\User;
use Tests\TestCase;

/**
 * La description d'un état n'a pas de plafond de caractères.
 */
class ConditionDescriptionLengthTest extends TestCase
{
    public function test_store_persists_a_description_longer_than_the_former_255_limit(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $description = str_repeat('a', 70_000);

        $response = $this->actingAs($admin)
            ->withHeader('X-Inertia', 'true')
            ->post(route('entities.conditions.store'), [
                'name' => 'État longue description',
                'description' => $description,
            ]);

        $response->assertRedirect();
        $this->assertSame($description, Condition::query()->where('name', 'État longue description')->value('description'));
    }

    public function test_update_persists_a_description_longer_than_text(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $condition = Condition::factory()->create([
            'description' => 'court',
            'created_by' => $admin->id,
        ]);
        $description = str_repeat('é', 40_000);

        $response = $this->actingAs($admin)->patchJson(
            route('entities.conditions.update', $condition),
            ['description' => $description],
        );

        $response->assertOk();
        $this->assertSame($description, $condition->fresh()->description);
    }
}
