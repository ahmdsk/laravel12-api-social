<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Ahmad',
                'email' => 'ahmad@test.com',
                'password' => bcrypt('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jhon Doe',
                'email' => 'jhon@test.com',
                'password' => bcrypt('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
