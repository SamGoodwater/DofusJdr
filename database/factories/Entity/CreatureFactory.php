<?php

namespace Database\Factories\Entity;

use App\Models\Entity\Creature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Creature>
 */
class CreatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $levels = [
            User::ROLE_GUEST,
            User::ROLE_USER,
            User::ROLE_PLAYER,
            User::ROLE_GAME_MASTER,
            User::ROLE_ADMIN,
            User::ROLE_SUPER_ADMIN,
        ];
        $readLevel = fake()->randomElement($levels);
        $writeLevel = fake()->randomElement(array_values(array_filter($levels, fn (int $lvl) => $lvl >= $readLevel)));

        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'hostility' => fake()->numberBetween(0, 4),
            'location' => fake()->optional()->city(),
            'level' => (string) fake()->numberBetween(1, 200),
            'other_info' => fake()->optional()->sentence(),
            'life' => (string) fake()->numberBetween(30, 5000),
            'pa' => (string) fake()->numberBetween(3, 12),
            'pm' => (string) fake()->numberBetween(3, 12),
            'po' => (string) fake()->numberBetween(0, 20),
            'ini' => (string) fake()->numberBetween(0, 100),
            'invocation' => (string) fake()->numberBetween(0, 10),
            'touch' => (string) fake()->numberBetween(0, 20),
            'ca' => (string) fake()->numberBetween(0, 50),
            'dodge_pa' => (string) fake()->numberBetween(0, 50),
            'dodge_pm' => (string) fake()->numberBetween(0, 50),
            'fuite' => (string) fake()->numberBetween(0, 50),
            'tacle' => (string) fake()->numberBetween(0, 50),
            'vitality' => (string) fake()->numberBetween(0, 50),
            'sagesse' => (string) fake()->numberBetween(0, 50),
            'strong' => (string) fake()->numberBetween(0, 50),
            'intel' => (string) fake()->numberBetween(0, 50),
            'agi' => (string) fake()->numberBetween(0, 50),
            'chance' => (string) fake()->numberBetween(0, 50),
            'do_fixe_neutre' => (string) fake()->numberBetween(0, 100),
            'do_fixe_terre' => (string) fake()->numberBetween(0, 100),
            'do_fixe_feu' => (string) fake()->numberBetween(0, 100),
            'do_fixe_air' => (string) fake()->numberBetween(0, 100),
            'do_fixe_eau' => (string) fake()->numberBetween(0, 100),
            'res_fixe_neutre' => (string) fake()->numberBetween(0, 100),
            'res_fixe_terre' => (string) fake()->numberBetween(0, 100),
            'res_fixe_feu' => (string) fake()->numberBetween(0, 100),
            'res_fixe_air' => (string) fake()->numberBetween(0, 100),
            'res_fixe_eau' => (string) fake()->numberBetween(0, 100),
            'res_neutre' => (string) fake()->numberBetween(0, 50),
            'res_terre' => (string) fake()->numberBetween(0, 50),
            'res_feu' => (string) fake()->numberBetween(0, 50),
            'res_air' => (string) fake()->numberBetween(0, 50),
            'res_eau' => (string) fake()->numberBetween(0, 50),
            'acrobatie_bonus' => (string) fake()->numberBetween(0, 10),
            'discretion_bonus' => (string) fake()->numberBetween(0, 10),
            'escamotage_bonus' => (string) fake()->numberBetween(0, 10),
            'athletisme_bonus' => (string) fake()->numberBetween(0, 10),
            'intimidation_bonus' => (string) fake()->numberBetween(0, 10),
            'arcane_bonus' => (string) fake()->numberBetween(0, 10),
            'histoire_bonus' => (string) fake()->numberBetween(0, 10),
            'investigation_bonus' => (string) fake()->numberBetween(0, 10),
            'nature_bonus' => (string) fake()->numberBetween(0, 10),
            'religion_bonus' => (string) fake()->numberBetween(0, 10),
            'dressage_bonus' => (string) fake()->numberBetween(0, 10),
            'medecine_bonus' => (string) fake()->numberBetween(0, 10),
            'perception_bonus' => (string) fake()->numberBetween(0, 10),
            'perspicacite_bonus' => (string) fake()->numberBetween(0, 10),
            'survie_bonus' => (string) fake()->numberBetween(0, 10),
            'persuasion_bonus' => (string) fake()->numberBetween(0, 10),
            'representation_bonus' => (string) fake()->numberBetween(0, 10),
            'supercherie_bonus' => (string) fake()->numberBetween(0, 10),
            'kamas' => fake()->optional()->numerify('####'),
            'drop_' => fake()->optional()->sentence(),
            'other_item' => fake()->optional()->sentence(),
            'other_consumable' => fake()->optional()->sentence(),
            'other_resource' => fake()->optional()->sentence(),
            'other_spell' => fake()->optional()->sentence(),
            'state' => fake()->randomElement([Creature::STATE_DRAFT, Creature::STATE_PLAYABLE]),
            'read_level' => $readLevel,
            'write_level' => $writeLevel,
            'image' => fake()->optional()->imageUrl(),
            'created_by' => User::factory(),
        ];
    }
}
