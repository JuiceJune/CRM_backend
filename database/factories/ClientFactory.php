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
            'logo' => "clients/logos/default.png",
            "name" => fake()->name,
            "email" => fake()->email,
            'start_date' => fake()->date,
            'location' => fake()->city,
            'industry' => fake()->randomElement(['Technology', 'Finance', 'Trade', 'Commercial Real Estate', 'Car & Automobile Sales', 'Engineering Services']),
            'company' => fake()->company,
        ];
    }
}
