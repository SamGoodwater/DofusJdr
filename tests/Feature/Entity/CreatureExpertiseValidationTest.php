<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Monster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreatureExpertiseValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function monster_update_rejects_out_of_range_profile_mastery(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'athletics_mastery' => 3,
                'save_vitality_mastery' => 2,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['athletics_mastery', 'save_vitality_mastery']);
    }
}
