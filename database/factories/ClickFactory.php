<?php

namespace Database\Factories;

use App\Models\Click;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Link;

/**
 * @extends Factory<Click>
 */
class ClickFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link_id' => Link::factory(),
            'ip_address' => $this->faker->ipv4(),
            'country' => $this->faker->countryCode(),
            'city' => $this->faker->city(),
            'device_type' => $this->faker->randomElement(['phone', 'desktop', 'tablet']),
            'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari']),
            'os' => $this->faker->randomElement(['Windows', 'iOS', 'Android']),
            'referer' => $this->faker->randomLetter(),
            'clicked_at' => now(),
        ];
    }
}
