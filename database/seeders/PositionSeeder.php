<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    protected array $positions = [
        ["title" => "CSM"],
        ["title" => "SDR"],
        ["title" => "IT Specialist"],
        ["title" => "Research Manager"],
        ["title" => "Researcher"],
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
