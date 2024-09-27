<?php

namespace Database\Seeders;

use App\Models\Mission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $missions = [
            ['name' => 'Coin Collector', 'mission_type_id' => 1, 'image' => '/images/missions/1.png'],
            ['name' => 'Crypto Miner', 'mission_type_id' => 1, 'image' => '/images/missions/2.png'],
            ['name' => 'Token Tycoon', 'mission_type_id' => 1, 'image' => '/images/missions/3.png'],
            ['name' => 'Blockchainer', 'mission_type_id' => 1, 'image' => '/images/missions/4.png'],
        ];

        foreach ($missions as $key => $mission) {
            \App\Models\Mission::updateOrCreate(['id' => $key + 1], ['id' => $key + 1, ...$mission]);
        }

        Mission::all()->each(function ($mission) {
            $mission->levels()->createMany([
                ['level' => 1, 'cost' => 100, 'production_per_hour' => 10],
                ['level' => 2, 'cost' => 200, 'production_per_hour' => 20],
                ['level' => 3, 'cost' => 300, 'production_per_hour' => 30],
                ['level' => 4, 'cost' => 400, 'production_per_hour' => 40],
                ['level' => 5, 'cost' => 500, 'production_per_hour' => 50],
                ['level' => 6, 'cost' => 1000, 'production_per_hour' => 100],
                ['level' => 7, 'cost' => 2000, 'production_per_hour' => 200],
                ['level' => 8, 'cost' => 4000, 'production_per_hour' => 400],
                ['level' => 9, 'cost' => 8000, 'production_per_hour' => 800],
                ['level' => 10, 'cost' => 16000, 'production_per_hour' => 1600],
            ]);
        });
    }
}
