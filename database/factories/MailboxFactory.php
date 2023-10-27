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
            'domain' => fake()->domainName,
            'avatar' => "mailboxes/avatars/default.png",
            'phone' => fake()->phoneNumber,
            'password' => fake()->password,
            'app_password' => fake()->password,
            'email_provider' => "gmail",
        ];
    }
}
