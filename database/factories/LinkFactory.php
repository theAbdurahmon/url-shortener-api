<?php

namespace Database\Factories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => $this->faker->url(),
            'slug' => $this->faker->unique()->lexify('????????'),
            'title' => $this->faker->sentence(),
            'expires_at' => null,
            'is_active' => true,
            'password' => null,
            'click_limit' => null,
            'clicks_count' => 0,
        ];
    }
}
