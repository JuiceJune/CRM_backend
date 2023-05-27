<?php

namespace Database\Factories;

use App\Models\EmailProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class MailboxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->email,
            'name' => fake()->name,
            'phone' => fake()->phoneNumber,
            'avatar' => "mailboxes/avatars/default.png",
            'domain' => fake()->domainName,
            'password' => fake()->password,
            'create_date' => fake()->date,
            'app_password' => fake()->password,
            'for_linkedin' => fake()->randomElement([true, false]),
            'email_provider_id' => fake()->numberBetween(1, EmailProvider::count()),
        ];
    }
}
