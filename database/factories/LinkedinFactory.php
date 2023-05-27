<?php

namespace Database\Factories;

use App\Models\EmailProvider;
use App\Models\Mailbox;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class LinkedinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'link' => fake()->url,
            'avatar' => "linkedin_accounts/avatars/default.png",
            'warmup' => fake()->randomElement([true, false]),
            'password' => fake()->password,
            'create_date' => fake()->date,
            'mailbox_id' => fake()->numberBetween(1, 30),
        ];
    }
}
