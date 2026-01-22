<?php

namespace Database\Seeders;

use App\Models\Counter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('counters')->insert([
            'id' => '01KFM0C8X2PH0G1Q9NDQ3MDG51',
            'service_id' => '01KFM08MA4VRFXZGZTSTFN7787',
            'name' => 'Counter 1',
            'status' => Counter::STATUS_OPEN,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Counter::factory()->count(5)->create();
    }
}
