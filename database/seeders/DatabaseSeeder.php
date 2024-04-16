<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        $account = Account::query()->create([
            'name' => 'Main',
        ]);

        User::query()->create([
                'account_id' => $account["id"],
                'name' => "admin",
                'email' => "admin@admin.com",
                'password' => Hash::make('admin'),
                'role_id' => Role::where('title', 'SUPER-ADMIN')->first()->id,
                'position_id' => Position::where('title', 'IT Specialist')->first()->id
            ]
        );
    }
}
