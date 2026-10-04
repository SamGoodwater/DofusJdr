<?php

namespace Tests\Unit\Creature;

use App\Models\Entity\Creature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fiche vide : résistances fixes nulles (composition), pas un total explicite à 0.
 */
class CreatureFixedResistanceDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_resistances_stay_null_on_blank_creature(): void
    {
        $creature = Creature::query()->create([
            'name' => 'Res Fixe Default',
            'created_by' => User::factory()->create()->id,
        ]);

        $fresh = $creature->fresh();
        foreach (['res_fixe_neutre', 'res_fixe_terre', 'res_fixe_feu', 'res_fixe_air', 'res_fixe_eau'] as $column) {
            $this->assertNull($fresh->{$column});
            $this->assertFalse($fresh->hasExplicitTotal($column));
        }
    }
}
