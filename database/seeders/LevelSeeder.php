<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['level' => 1, 'name' => 'Rookie Trader', 'from_balance' => 5_000, 'to_balance' => 15_000],
            ['level' => 2, 'name' => 'Crypto Enthusiast', 'from_balance' => 15_000, 'to_balance' => 50_000],
            ['level' => 3, 'name' => 'Blockchain Pro', 'from_balance' => 50_000, 'to_balance' => 150_000],
            ['level' => 4, 'name' => 'Token Expert', 'from_balance' => 150_000, 'to_balance' => 500_000],
            ['level' => 5, 'name' => 'Crypto Master', 'from_balance' => 500_000, 'to_balance' => 1_000_000],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(['level' => $level['level']], $level);
        }
    }
}
