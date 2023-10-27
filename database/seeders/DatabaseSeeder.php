<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PositionSeeder::class
        ]);
         \App\Models\User::factory()->create(['name' => "admin", 'email' => "admin@admin.com", 'password' => 'admin',
             'avatar' => 'users/avatars/default.png', 'role_id' => 1, 'position_id' => 2]);
         \App\Models\User::factory(30)->create();
         \App\Models\Client::factory(20)->create();
         \App\Models\Mailbox::factory(30)->create();
         \App\Models\Project::factory(10)->create();
    }
}
