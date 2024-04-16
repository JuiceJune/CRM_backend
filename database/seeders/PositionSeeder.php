<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    protected array $positions = [
        ["title" => "CSM"],
        ["title" => "SDR"],
        ["title" => "BDM"],
        ["title" => "IT Specialist"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->positions as $position) {
            Position::create($position);
        }
    }
}
