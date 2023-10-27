<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Linkedin;
use App\Models\Mailbox;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'logo' => "projects/logos/default.png",
            'name' => fake()->company,
            'client_id' => fake()->numberBetween(1, 20),
            'start_date' => fake()->date,
            'end_date' => fake()->date,
            'price' => fake()->numberBetween(1000, 10000),
        ];
    }
    public function configure()
    {
        return $this->afterCreating(function (Project $project) {
            $mailbox1 = fake()->numberBetween(1, 30);
            $mailbox2 = fake()->numberBetween(1, 30);
            $project->mailboxes()->attach([$mailbox1, $mailbox2]);

            $user1 = fake()->numberBetween(1, 30);
            $user2 = fake()->numberBetween(1, 30);
            $project->users()->attach([$user1, $user2]);
        });
    }
}
