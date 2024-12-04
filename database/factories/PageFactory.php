<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Page;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);
        return [
            "name" => $name,
            "slug" => \Illuminate\Support\Str::slug($name),
            "uniqid" => uniqid(),
            "keyword" => $this->faker->word(),
            "order_num" => $this->faker->numberBetween(0, 100),
            "is_dropdown" => $this->faker->boolean(),
            "is_public" => $this->faker->boolean(),
            "is_visible" => $this->faker->boolean(),
            "is_editable" => $this->faker->boolean(),
            "page_id" => null,
            'created_by' => \App\Models\User::where('role', 'super_admin')->first()?->id,
        ];
    }
}
