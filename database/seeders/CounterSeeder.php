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
            'id' => '01KFQ29GGX80S41P7WDP91QNQA',
            'service_id' => '01KFM08MA4VRFXZGZTSTFN7787',
            'name' => 'Counter Ruang 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
