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
            'role_id' => Role::ADMIN,
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => uniqid(),
        ]);

        User::create([
            'role_id' => Role::COUNTER,
            'username' => 'loket',
            'name' => 'Loket',
            'email' => 'loket@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => uniqid(),
        ]);

        User::create([
            'role_id' => Role::SERVICE,
            'counter_id' => '01KFQ29GGX80S41P7WDP91QNQA',
            'username' => 'service1',
            'name' => 'Service1',
            'email' => 'service1@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => uniqid(),
        ]);

        User::create([
            'role_id' => Role::KIOSK,
            'username' => 'kiosk',
            'name' => 'Kiosk',
            'email' => 'kiosk@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => uniqid(),
        ]);
    }
}
