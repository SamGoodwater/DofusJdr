<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Monster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Maîtrises de compétences / sauvegardes portées par le monstre (groupe Profil).
 */
final class CreatureExpertiseValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function monster_update_persists_profile_masteries_on_monster(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'athletics_mastery' => 2,
                'save_agility_mastery' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $monster->refresh();
        $this->assertSame(2, (int) $monster->athletics_mastery);
        $this->assertSame(1, (int) $monster->save_agility_mastery);
    }

    #[Test]
    public function monster_update_rejects_mastery_above_its_tier_limit(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'athletics_mastery' => 3,
                'save_agility_mastery' => 2,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['athletics_mastery', 'save_agility_mastery']);
    }
}
