<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role_id' => Role::ROLE_ADMIN,
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => uniqid(),
        ]);

        User::create([
            'role_id' => Role::ROLE_STAFF,
            'counter_id' => '01KFM0C8X2PH0G1Q9NDQ3MDG51',
            'username' => 'staff',
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => uniqid(),
        ]);

        User::factory()->count(50)->create();
    }
}
