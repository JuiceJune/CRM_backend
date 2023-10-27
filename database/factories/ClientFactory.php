<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'avatar' => "clients/avatars/default.png",
            "name" => fake()->name,
            "email" => fake()->email,
            'location' => fake()->city,
            'industry' => fake()->randomElement(['Technology', 'Finance', 'Trade', 'Commercial Real Estate', 'Car & Automobile Sales', 'Engineering Services']),
            'start_date' => fake()->date,
        ];
    }
}
