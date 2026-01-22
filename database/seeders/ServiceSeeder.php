<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            'id' => '01KFM08MA4VRFXZGZTSTFN7787',
            'name' => 'General Inquiry',
            'code' => 'GEN',
            'opening_time' => '08:00:00',
            'closing_time' => '17:00:00',
            'max_queue_per_day' => 100,
            'avg_wait_time' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Service::factory()->count(rand(5, 15))->create();
    }
}
