<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pos.com',
            'password' => bcrypt('password'),
            'level' => 1,
            'phone' => '081234567890',
            'address' => 'Jakarta, Indonesia',
        ]);

        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@pos.com',
            'password' => bcrypt('password'),
            'level' => 2,
            'phone' => '081234567891',
            'address' => 'Jakarta, Indonesia',
        ]);

        \App\Models\User::create([
            'name' => 'Leader',
            'email' => 'leader@pos.com',
            'password' => bcrypt('password'),
            'level' => 3,
            'phone' => '081234567892',
            'address' => 'Jakarta, Indonesia',
        ]);

        \App\Models\User::create([
            'name' => 'Kasir',
            'email' => 'kasir@pos.com',
            'password' => bcrypt('password'),
            'level' => 4,
            'phone' => '081234567893',
            'address' => 'Jakarta, Indonesia',
        ]);

        \App\Models\User::create([
            'name' => 'Manager',
            'email' => 'manager@pos.com',
            'password' => bcrypt('password'),
            'level' => 5,
            'phone' => '081234567894',
            'address' => 'Jakarta, Indonesia',
        ]);
    }
}
